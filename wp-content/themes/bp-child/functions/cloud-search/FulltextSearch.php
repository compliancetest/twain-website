<?php
require_once(THE_FUNCTION . '/aws/sdk/aws-autoloader.php');
use Aws\CloudSearch\CloudSearchClient;

class FulltextSearch extends BaseAWS
{

    private $_domainName = '';

    private $_allowed_post_types = array(
        'press-release', 'blog', 'event', 'page', 'forum', 'product-service', 'service', 'test-case', 'test-suite', 'topic', 'bp_doc'
    );

    private $_allowedSortFields = array(
        'post_title', 'post_type', 'last_updated_date'
    );

    private $_allowedSortOrder = array(
        'asc', 'desc'
    );

    private $_allowedFields = array(
        'post_type', 'community'
    );

    public function __construct()
    {
        $this->_client = get_transient('fulltext_cloud_search_object');
        if (!$this->_client) {

            $this->_domainName = get_option('cloudsearch_fulltext_domain_name');

            $configClient = CloudSearchClient::factory(self::getAWSConfigs());

            $this->_client = $configClient->getDomainClient($this->_domainName, array(
                'credentials' => $configClient->getCredentials()
            ));
            set_transient('fulltext_cloud_search_object', $this->_client, 300);
        }
    }

    public function search($params = false, $full_results = false)
    {
        global $wpdb;
        $str = array();
        $str['return'] = '_all_fields';
        $str['facet'] = '{ "post_type": {sort:"bucket", size:100}, "community": {} }';
        if ($full_results) {
            $str['size'] = 10000;
            unset($params['page']);
        } else {
            $str['size'] = SEARCH_RESULTS_LIMIT;
        }
        $l = '';
        $range_checked = false;
        if (is_user_logged_in()) {
            if (is_super_admin()) {
                //super admin should see all items
                $l .= "  (or ( term field=visibility 1 ) (  term field=visibility 3   ) ( term field=visibility 2 ) )";
            } else {
                //usual user should see only own and community items
                $groups = groups_get_user_groups(get_current_user_id());
                $groups_str = '';
                foreach ($groups['groups'] AS $group_id) {
                    $groups_str .= ' ( term field=community_id ' . $group_id . ' ) ';
                }
                if (!empty($groups_str)) {
                    $groups_str = ' ( or ' . $groups_str . ' ) ';
                } else {
                    $groups_str = ' ( or ( term field=community_id 1 ) ) ';
                }
                $private_where = '';
                $organisation_members = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM wp_organisations_members WHERE organisation_id = ( SELECT organisation_id FROM wp_organisations_members WHERE user_id = %d ) ", get_current_user_id()));
                if ($organisation_members) {
                    foreach ($organisation_members AS $organisation_members) {
                        $private_where .= '( term field=post_author_id ' . $organisation_members->user_id . ' )';
                    }
                }
                if (!empty($private_where)) {
                    $private_where = ' ( or ' . $private_where . ' ) ';
                } else {
                    $private_where = " ( term field=post_author_id " . get_current_user_id() . " ) ";
                }
                $l .= "  (or ( term field=visibility 1 ) (  and ( term field=visibility 3 )  " . $private_where . " ) ( and ( term field=visibility 2 ) " . $groups_str . " ) )";
            }
        } else {
            //non-logged in user should see only public items
            $l .= "  ( term field=visibility 1 )";
        }
        $str['sort'] = '_score desc';

        foreach ($params AS $k => $v) {
            if ($k == 'q') {
                if (!empty($v)) {
                    $str['query'] = $v;
                }
            } else if ($k == 'page') {
                if ($v != 1) {
                    $str['start'] = ((--$v * SEARCH_RESULTS_LIMIT));
                }
            } else if ($k == 'orderby') {
                if (in_array($v, $this->_allowedSortFields)) {
                    $sortOrder = isset($params['order']) ? $params['order'] : 'asc';
                    if (in_array($sortOrder, $this->_allowedSortOrder)) {
                        $str['sort'] = $v . " " . $sortOrder;
                    }
                }
            } else if ($k == 'order') {

            } else if ($k == 'date_from' || $k == 'date_to') {
                if (!$range_checked) {
                    if (isset($params['date_from']) && !empty($params['date_from']) && validateDate($params['date_from'])) {
                        $from = "['" . $params['date_from'] . 'T00:00:00Z' . "'";
                    } else {
                        $from = '{';
                    }
                    if (isset($params['date_to']) && !empty($params['date_to']) && validateDate($params['date_to'])) {
                        $to = "'" . $params['date_to'] . 'T23:59:59Z' . "']";
                    } else {
                        $to = '}';
                    }
                    if ("$from, $to" !== '{, }') {
                        $l .= "(range field=last_updated_date $from, $to   ) ";
                    }
                    $range_checked = true;
                }
            } else {
                if ($v !== 'All' && in_array($k, $this->_allowedFields)) {
                    $l .= " (term field=" . $k . " '" . urldecode($v) . "') ";
                }
            }
        }
        if (!empty($l)) {
            $str['filterQuery'] = ' ( and ' . $l . ' ) ';
        }
        if (!isset($str['query'])) {
            $str['query'] = 'matchall';
            $str['queryParser'] = 'structured';
        }
        try {
            $r = $this->_client->search($str);
        } catch (Exception $e) {
            return false;
        }
        return $r;
    }

    public function fullUpload($post_id = false)
    {
        global $wpdb;
        $data = $response_data = array();
        if ($post_id) {
            $posts = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_posts WHERE post_type IN( '" . implode("', '", $this->_allowed_post_types) . "' ) AND ID = %d ", $post_id));
        } else {
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type IN( '" . implode("', '", $this->_allowed_post_types) . "' )");
        }
        if ($posts) {
            foreach ($posts AS $post) {
                $groups = groups_get_user_groups($post->post_author);
                $communityNames = array();
                if (is_array($groups['groups'])) {
                    foreach ($groups['groups'] AS $group) {
                        $communityNames[] = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_bp_groups WHERE id = %d ", $group));
                    }
                }
                if (empty($communityNames)) {
                    $communityNames = array('TWAIN');
                    $groups['groups'] = array(1);
                }
                if ($post->post_type == 'test-suite' && $post->ID != $wpdb->get_var($wpdb->prepare("SELECT suite_id FROM wp_test_suites WHERE family_mark IN( SELECT family_mark FROM wp_test_suites WHERE suite_id = %d ) ORDER BY suite_id DESC LIMIT 1", $post->ID))) {
                    continue;
                }
                $post_data = $this->_processPost($post);
                if (in_array($post->post_type, array('event', 'blog', 'press-release'))) {
                    $community_id = ( integer )get_post_meta($post->ID, 'blog_community_id', true);
                    if ($community_id === 0 || $community_id === 1) {
                        $groups['groups'] = array(1);
                        $post_data['visibility'] = 1;
                    } else {
                        $communityNames = array($wpdb->get_var($wpdb->prepare("SELECT name FROM wp_bp_groups WHERE id = %d ", $community_id)));
                        $groups['groups'] = array($community_id);
                        $post_data['visibility'] = 2;
                    }
                }
                $temp_data = array(
                    'community' => $communityNames,
                    'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($post->post_modified)) . 'Z',
                    'post_author_name' => cp_get_user_fullname($post->post_author),
                    'post_author_id' => $post->post_author,
                    'post_content' => $post_data['descr'],
                    'post_status' => $post->post_status,
                    'post_title' => $post->post_title,
                    'post_type' => $post_data['type'],
                    'post_id' => $post->ID,
                    'visibility' => $post_data['visibility'],
                    'community_id' => $groups['groups'],
                    'for_search' => $post_data['for_search'],
                    'link' => get_permalink($post->ID)
                );
                array_push($data, array('type' => 'add', 'id' => $post->ID, 'fields' => $temp_data));
            }
        }
        if (!$post_id) {
            $test_scenarios = $wpdb->get_results("SELECT * FROM wp_test_suites_scenarios");
            foreach ($test_scenarios AS $test_scenario) {
                $post = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_posts WHERE ID = %d ", $test_scenario->suite_id));
                if (!$post || $test_scenario->code == 'Default') {
                    continue;
                }
                $groups = groups_get_user_groups($post->post_author);
                $communityNames = array();
                if (is_array($groups['groups'])) {
                    foreach ($groups['groups'] AS $group) {
                        $communityNames[] = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_bp_groups WHERE id = %d ", $group));
                    }
                }
                if (empty($communityNames)) {
                    $communityNames = array('TWAIN');
                    $groups['groups'] = array(1);
                }
                $temp_data = array(
                    'community' => $communityNames,
                    'last_updated_date' => date('Y-m-d\TH:i:s', strtotime($post->post_modified)) . 'Z',
                    'post_author_name' => cp_get_user_fullname($post->post_author),
                    'post_author_id' => $post->post_author,
                    'post_content' => $test_scenario->description,
                    'post_status' => $post->post_status,
                    'post_title' => $test_scenario->code,
                    'post_type' => 'Test Scenario',
                    'post_id' => $post->ID,
                    'visibility' => 1,
                    'community_id' => $groups['groups'],
                    'for_search' => $test_scenario->description . 'Test Scenario' . $test_scenario->code . cp_get_user_fullname($post->post_author),
                    'link' => get_permalink($post->ID)
                );
                array_push($data, array('type' => 'add', 'id' => 'scenario_' . $test_scenario->id, 'fields' => $temp_data));
            }
        }
        if (!empty($data)) {
            $data = $this->_client->uploadDocuments(array('documents' => json_encode($data), 'contentType' => 'application/json'));
            $response_data = array(
                'Status' => $data->getPath('status'),
                'Added' => $data->getPath('adds'),
                'Deleted' => $data->getPath('deletes')
            );
        }
        return $response_data;
    }

    public function fullDelete($post_id = false)
    {
        global $wpdb;
        $data = $response_data = array();
        if ($post_id) {
            $posts = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_posts WHERE post_type IN( '" . implode("', '", $this->_allowed_post_types) . "' ) AND ID = %d ", $post_id));
        } else {
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type IN( '" . implode("', '", $this->_allowed_post_types) . "' )");
        }
        if ($posts) {
            foreach ($posts AS $post) {
                if ($post->post_type == 'test-suite' && $post->ID != $wpdb->get_var($wpdb->prepare("SELECT suite_id FROM wp_test_suites WHERE family_mark IN( SELECT family_mark FROM wp_test_suites WHERE suite_id = %d ) ORDER BY suite_id DESC LIMIT 1", $post->ID))) {
                    continue;
                }
                array_push($data, array('type' => 'delete', 'id' => $post->ID));
            }
        }
        //upload test scenarious only on bulk upload action
        if (!$post_id) {
            $test_scenarios = $wpdb->get_results("SELECT * FROM wp_test_suites_scenarios");
            foreach ($test_scenarios AS $test_scenario) {
                $post = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_posts WHERE ID = %d ", $test_scenario->suite_id));
                if (!$post || $test_scenario->code == 'Default') {
                    continue;
                }
                array_push($data, array('type' => 'delete', 'id' => 'scenario_' . $test_scenario->id));
            }
        }
        if (!empty($data)) {
            $data = $this->_client->uploadDocuments(array('documents' => json_encode($data), 'contentType' => 'application/json'));
            $response_data = array(
                'Status' => $data->getPath('status'),
                'Added' => $data->getPath('adds'),
                'Deleted' => $data->getPath('deletes')
            );
        }
        return $response_data;
    }

    private function _processPost($post)
    {
        switch ($post->post_type) {
            case 'page':
                $data = array(
                    'type' => 'Page',
                    'visibility' => 1,
                    'for_search' => 'Page',
                    'descr' => $post->post_content
                );
                break;

            case 'product-service':
                $product = new ProductAndService($post->ID);
                $product->load();
                $data = array(
                    'type' => 'Product',
                    'visibility' => $product->visibility == 'Public' ? 1 : $product->visibility == 'Community' ? 2 : 3,
                    'for_search' => 'Product',
                    'descr' => $product->descrition
                );
                break;
            case 'service':
                $service = new Service($post->ID);
                $service->load();
                $data = array(
                    'type' => 'Service',
                    'visibility' => $service->service_visibility == 'Public' ? 1 : $service->service_visibility == 'Community' ? 2 : 3,
                    'for_search' => 'Service',
                    'descr' => $service->service_description
                );
                break;
            case 'test-case':
                $data = array(
                    'type' => 'Test Case',
                    'visibility' => 1,
                    'for_search' => 'Test Case',
                    'descr' => get_post_meta($post->ID, 'test_intent_description', true)
                );
                break;
            case 'test-suite':
                $data = array(
                    'type' => 'Test Suite',
                    'visibility' => 1,
                    'for_search' => 'Test Suite',
                    'descr' => get_post_meta($post->ID, 'ts_description', true)
                );
                break;
            case 'topic':
                $data = array(
                    'type' => 'Forum Topic',
                    'visibility' => 1,
                    'for_search' => 'Forum Topic',
                    'descr' => $post->post_content
                );
                break;
            case 'forum':
                $data = array(
                    'type' => 'Forum Post',
                    'visibility' => 1,
                    'for_search' => 'Forum Post',
                    'descr' => $post->post_content
                );
                break;
            case 'bp_doc':
                $data = array(
                    'type' => 'Wiki Article',
                    'visibility' => 1,
                    'for_search' => 'Wiki Article',
                    'descr' => $post->post_content
                );
                break;
            case 'blog':
                $data = array(
                    'type' => 'Blog',
                    'for_search' => 'Blog',
                    'descr' => $post->post_content
                );
                break;
            case 'event':
                $data = array(
                    'type' => 'Event',
                    'for_search' => 'Event',
                    'descr' => $post->post_content
                );
                break;
            case 'press-release':
                $data = array(
                    'type' => 'Press Release',
                    'for_search' => 'Press Release',
                    'descr' => $post->post_content
                );
                break;
            default:
                $data = array(
                    'type' => '',
                    'visibility' => 1,
                    'for_search' => '',
                    'descr' => ''
                );
                break;
        }
        return $data;
    }

    public function delete_item($id)
    {
        $data = array();
        array_push($data, array('type' => 'delete', 'id' => $id));
        $data = $this->_client->uploadDocuments(array('documents' => json_encode($data), 'contentType' => 'application/json'));
        return array(
            'Status' => $data->getPath('status'),
            'Added' => $data->getPath('adds'),
            'Deleted' => $data->getPath('deletes')
        );
    }

    public static function createDomain()
    {
        $domainName = get_option('cloudsearch_fulltext_domain_name');
        $client = CloudSearchClient::factory(array(
            'key' => get_option('aws_s3_key'),
            'secret' => get_option('aws_s3_secret'),
            'region' => 'us-west-2'
        ));

        $result = $client->describeDomains(array(
            'DomainNames' => array($domainName),
        ));
        if (empty($result->getPath('DomainStatusList'))) {
            $result = $client->createDomain(array(
                'DomainName' => $domainName
            ));
        }
        return $result;
    }

    public static function configureFields()
    {
        $domainName = get_option('cloudsearch_fulltext_domain_name');
        $client = CloudSearchClient::factory(array(
            'key' => get_option('aws_s3_key'),
            'secret' => get_option('aws_s3_secret'),
            'region' => 'us-west-2'
        ));

        $result = $client->describeDomains(array(
            'DomainNames' => array($domainName),
        ));
        if (!empty($result->getPath('DomainStatusList'))) {

            $result = $client->defineAnalysisScheme(array(
                'DomainName' => $domainName,
                'AnalysisScheme' => array(
                    'AnalysisSchemeName' => 'twain',
                    'AnalysisSchemeLanguage' => 'en',
                    'AnalysisOptions' => array(
                        'Synonyms' => '{}',
                        'Stopwords' => '[]',
                        'StemmingDictionary' => '{}',
                        'JapaneseTokenizationDictionary' => '[]',
                        'AlgorithmicStemming' => 'full',
                    ),
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'for_search',
                    'IndexFieldType' => 'text',
                    'TextOptions' => array(
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => false,
                        'HighlightEnabled' => false,
                        'AnalysisScheme' => 'twain'
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'link',
                    'IndexFieldType' => 'text',
                    'TextOptions' => array(
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => false,
                        'HighlightEnabled' => false,
                        'AnalysisScheme' => 'twain'
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_author_name',
                    'IndexFieldType' => 'text',
                    'TextOptions' => array(
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => false,
                        'HighlightEnabled' => false,
                        'AnalysisScheme' => 'twain'
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_content',
                    'IndexFieldType' => 'text',
                    'TextOptions' => array(
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => true,
                        'HighlightEnabled' => false,
                        'AnalysisScheme' => 'twain'
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_title',
                    'IndexFieldType' => 'text',
                    'TextOptions' => array(
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => true,
                        'HighlightEnabled' => true,
                        'AnalysisScheme' => 'twain'
                    )
                ),
            ));


            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'community_id',
                    'IndexFieldType' => 'int-array',
                    'IntArrayOptions' => array(
                        'DefaultValue' => 0,
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'visibility',
                    'IndexFieldType' => 'int-array',
                    'IntArrayOptions' => array(
                        'DefaultValue' => 0,
                        'FacetEnabled' => true,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_author_id',
                    'IndexFieldType' => 'int',
                    'IntOptions' => array(
                        'DefaultValue' => 0,
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => false,
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_id',
                    'IndexFieldType' => 'int',
                    'IntOptions' => array(
                        'DefaultValue' => 0,
                        'FacetEnabled' => false,
                        'SearchEnabled' => false,
                        'ReturnEnabled' => true,
                        'SortEnabled' => false,
                    )
                ),
            ));


            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'last_updated_date',
                    'IndexFieldType' => 'date',
                    'DateOptions' => array(
                        'FacetEnabled' => true,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => true,
                    )
                ),
            ));


            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_status',
                    'IndexFieldType' => 'literal',
                    'LiteralOptions' => array(
                        'DefaultValue' => 'N/A',
                        'FacetEnabled' => false,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => true,
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'post_type',
                    'IndexFieldType' => 'literal',
                    'LiteralOptions' => array(
                        'DefaultValue' => 'N/A',
                        'FacetEnabled' => true,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                        'SortEnabled' => true,
                    )
                ),
            ));

            $client->defineIndexField(array(
                'DomainName' => $domainName,
                'IndexField' => array(
                    'IndexFieldName' => 'community',
                    'IndexFieldType' => 'literal-array',
                    'LiteralArrayOptions' => array(
                        'DefaultValue' => '["N/A"]',
                        'FacetEnabled' => true,
                        'SearchEnabled' => true,
                        'ReturnEnabled' => true,
                    ),
                ),
            ));

            $result = $client->indexDocuments(array(
                'DomainName' => $domainName,
            ));
            return $result;
        }
    }
} 