<?php

namespace app\Classes;

use App\CloudSearchDomainTrait;

class RegistrySearch
{

    use CloudSearchDomainTrait;

    private $_allowedSortFields = array(
        'name', 'date'
    );
    private $_allowedSortOrder = array(
        'asc', 'desc'
    );

    private $_allowedFields = array(
        'type', 'owner', 'test_suite', 'test_type', 'role', 'level', 'status', 'date_from', 'date_to'
    );

    public function __construct($status = false)
    {
        $this->_client = $this->getRegistryEndpointCloudSearchClient($status);
    }

    public function search($params = false, $fullResults = false)
    {
        foreach ($params as $kkk => $paramValue) {
            if (empty($paramValue)) unset($params[$kkk]);
        }

        $str = array();
        $str['return'] = '_all_fields';
        $str['facet'] = '{ "type": {}, "test_type": {}, "test_suite": {}, "owner": {}, "level": {}, "role": {}, "status": {} }';
        if ($fullResults) {
            $str['size'] = 10000;
            unset($params['page']);
        } else {
            $str['size'] = SEARCH_RESULTS_LIMIT;
        }
        $l = '';
        $range_checked = false;

        $str['sort'] = 'name asc';
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
                        $l .= "(range field=date $from, $to   ) ";
                    }
                    $range_checked = true;
                }
            } else {
                if ($v !== 'All' && $k != 'order' && in_array($k, $this->_allowedFields)) {
                    $l .= " (term field=" . $k . " '" . str_replace('&ndash;', '&#8211;', htmlentities($v)) . "') ";
                }
            }
        }

        $publicWhere = $communityWhere = $organisationWhere = '';

        foreach (getCommunities() as $community) {
            if ($community->list_only_certified) {
                $publicWhere .= " ( and ( term field=visibility 1 ) (term field=community_id '$community->id' ) (term field=status 'Verified')) ";
            } else {
                $publicWhere .= " ( and ( term field=visibility 1 ) (term field=community_id '$community->id' ) ) ";
            }
        }

        if (is_user_logged_in()) {
            foreach (getUserCommunities(get_current_user_id()) as $community) {
                if ($community->list_only_certified) {
                    $communityWhere .= " ( and ( term field=visibility 2 ) (term field=community_id '$community->id' ) (term field=status 'Verified')) ";
                } else {
                    $communityWhere .= " ( and ( term field=visibility 2 ) (term field=community_id '$community->id' ) ) ";
                }
            }

            if ($communityWhere) {
                $communityWhere = ' (or ' . $communityWhere . ') ';
            }

            $userOrganisation = ct_get_user_organisation(get_current_user_id());
            if ($userOrganisation) {
                foreach (getCommunities() as $community) {
                    if ($community->list_only_certified) {
                        $organisationWhere .= " ( and ( term field=visibility 3 ) ( term field=organisation_id $userOrganisation->id) (term field=community_id '$community->id' ) (term field=status 'Verified')) ";
                    } else {
                        $organisationWhere .= " ( and ( term field=visibility 3 ) ( term field=organisation_id $userOrganisation->id) (term field=community_id '$community->id' ) ) ";
                    }
                }
            }
            if ($organisationWhere) {
                $organisationWhere = ' (or ' . $organisationWhere . ') ';
            }
        }

        if (!empty($l) || !empty($publicWhere) || !empty($communityWhere) || !empty($organisationWhere)) {

            if (is_super_admin()) {
                if (!empty($l)) {
                    $str['filterQuery'] = ' ( and ' . $l . ' ) ';
                }
            } else {
                $str['filterQuery'] = ' ( and ' . $l . ' (or ' . $publicWhere . ' ' . $communityWhere . ' ' . $organisationWhere . ' ) ) ';
            }

        }
        if (!isset($str['query'])) {
            $str['query'] = 'matchall';
            $str['queryParser'] = 'structured';
        }
        try {
            $r = $this->_client->search($str);
        } catch (Exception $e) {
            var_dump($e->getMessage());
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