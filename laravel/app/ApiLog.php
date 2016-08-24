<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiLog extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'request_type', 'uri',
        'request', 'response'
    ];

    /**
     * Get logs data
     * @param $filters
     * @return mixed
     */
    public static function getLogs($filters)
    {
        $logs = new ApiLog();
        return $logs->setWhereQuery($filters)->orderBy('updated_at', 'desc')->paginate(25);
    }

    /**
     * Get Filters list
     * @param $filters
     * @return array
     */
    public static function getFilters($filters)
    {
        $logs = new ApiLog();
        return $logs->processFilters($filters);
    }

    /**
     * Where query based on filters selected by user
     * @param $subscriptions
     * @param $filters
     * @return null
     */
    public function setWhereQuery($filters)
    {
        $model = DB::table('api_logs');
        if ($filters['user_id']) {
            $model->where('user_id', $filters['user_id']);
        }
        if ($filters['request_type']) {
            $model->where('request_type', $filters['request_type']);
        }
        if ($filters['date']) {
            $model->whereRaw(" ( updated_at > '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'])) . "' AND updated_at <  '" . date('Y-m-d H:i:s', getUTCTimeStamp($filters['date'] . ' 23:59:59')) . "' ) ");
        }
        return $model;
    }

    /**
     * Process filters and configure where query
     * @param $filters
     * @return array
     */
    public function processFilters($filters)
    {
        $arr = [
            'user_id' => $this->setWhereQuery($filters)->groupBy('user_id')->pluck('user_id'),
            'request_type' => $this->setWhereQuery($filters)->groupBy('request_type')->pluck('request_type'),
        ];
        return $arr;
    }
}
