<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuiteSearch extends Model
{
    /**
     * Where query based on filters selected by user
     * @param $filters
     * @return mixed
     */
    public function setWhereQuery($filters)
    {
        $this->whereModel = LaravelTestSuite::from('test_suites as ts')->select('ts.*', 'c.slug as communitySlug', 'c.title as communityTitle', 'tst.type');

        $this->whereModel->join('communities AS c', function ($join) {
            $join->on('ts.community_id', '=', 'c.id');
        });

        $this->whereModel->join('test_suites_types AS tst', function ($join) {
            $join->on('tst.test_suite_id', '=', 'ts.id');
        });


        if ($filters['type']) {
            $this->whereModel->where('tst.type', $filters['type']);
        }

        if (!empty(trim($filters['q']))) {
            $q = trim($filters['q']);
            $this->whereModel->whereRaw(" (ts.issuer LIKE '%{$q}%' OR ts.full_name LIKE '%{$q}%' OR c.title LIKE '%{$q}%' OR c.description LIKE '%{$q}%' OR ts.description LIKE '%{$q}%') ");
        }

        if ($filters['issuer']) {
            $this->whereModel->where('issuer', $filters['issuer']);
        }
        if ($filters['status']) {
            $this->whereModel->where('status', $filters['status']);
        }
        if ($filters['test_case_id']) {
            $this->whereModel->where('tc.test_case_id', $filters['test_case_id']);
        }
        if ($filters['community_id']) {
            $this->whereModel->where('community_id', $filters['community_id']);
        }
        if ($filters['date_from']) {
            $this->whereModel->whereRaw(" ( created_at > '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date_from'])) . "' ) ");
        }
        if ($filters['date_to']) {
            $this->whereModel->whereRaw(" ( created_at < '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date_to'] . ' 23:59:59')) . "' ) ");
        }
        if ($filters['orderby']) {
            $order = isset($filters['order']) ? $filters['order'] : 'asc';
            $this->whereModel->orderBy($filters['orderby'], $order);
        }
        if (!is_super_admin()) {
            $q = (function ($q) {
                foreach (Community::all() as $community) {
                    if ($community->getActiveMember(Auth::user()->ID)) {
                        if (!$community->isAdmin()) {
                            $q->orWhere(['status' => 'Active', 'community_id' => $community->id]);
                        } else {
                            $q->orWhere(['community_id' => $community->id]);
                        }
                    }

                }
            });
            $this->whereModel->where($q);
        }
        return $this->whereModel;
    }

    /**
     * Process filters and configure where query
     * @param $filters
     * @return array
     */
    public function processFilters($filters)
    {
        $arr = [
            'type' => $this->setWhereQuery($filters)->groupBy('tst.type')->orderBy('tst.type')->pluck('tst.type'),
            'issuer' => $this->setWhereQuery($filters)->groupBy('issuer')->orderBy('issuer')->pluck('issuer'),
            'status' => $this->setWhereQuery($filters)->groupBy('status')->orderBy('status')->pluck('status'),
            'community_id' => $this->setWhereQuery($filters)->groupBy('community_id')->pluck('community_id'),
        ];
        return $arr;
    }

    /**
     * @param $filters
     * @param int $totalPerPage
     * @return mixed
     */
    public static function getTestSuites($filters, $totalPerPage = 25)
    {
        $testSuiteModel = new self();
        return $testSuiteModel->setWhereQuery($filters)->groupBy('ts.id')->orderBy('ts.created_at', 'desc')->paginate($totalPerPage);
    }

    /**
     * Get Filters list
     * @param $filters
     * @return array
     */
    public static function getFilters($filters)
    {
        $entry = new self();
        return $entry->processFilters($filters);
    }
}
