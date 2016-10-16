<?php

namespace app\Classes;

use App\CloudSearchDomainTrait;

class SiteSearch
{

    use CloudSearchDomainTrait;

    private $_allowedSortFields = array(
        'post_title', 'post_type', 'last_updated_date'
    );

    private $_allowedSortOrder = array(
        'asc', 'desc'
    );

    private $_allowedFields = array(
        'post_type', 'community', '_id', 'community_id'
    );

    public function __construct()
    {
        $this->_client = $this->getFulltextEndpointCloudSearchClient(false);
    }

    public function search($params = false, $onlyFilters = false, $fullResults = false)
    {
        global $wpdb;

        foreach ($params as $kkk => $paramValue) {
            if (empty($paramValue)) unset($params[$kkk]);
        }
        $str = array();
        $str['return'] = '_all_fields';
        $str['facet'] = '{ "post_type": {sort:"bucket"}, "community_id": {}, "community": {sort:"bucket"} }';
        if ($fullResults) {
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
                $groups = getUserCommunities(get_current_user_id());
                $groups_str = '';
                foreach ($groups AS $group) {
                    $groups_str .= " ( term field=community_id '$group->id' ) ";
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
}