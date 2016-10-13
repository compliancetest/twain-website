<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransactionChangeLog extends Model
{
    use UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'execution_id',
        'user_id',
        'test_outcome_status_id',
        'changed_by_server_validation',
        'deleted_by'
    ];

    /**
     * Log new entry on Transaction update
     * @param Transaction $transaction
     * @param $userId
     * @param bool $newOutcomeStatusId
     * @param bool $changedByServerValidation
     */
    public static function addLog(Transaction $transaction, $userId, $newOutcomeStatusId = false, $changedByServerValidation = false, $deletedBy = 0)
    {
        TransactionChangeLog::create([
            'execution_id' => $transaction->execution_id,
            'user_id' => $userId,
            'test_outcome_status_id' => $newOutcomeStatusId ? TestOutcomeStatus::getIdByCode($newOutcomeStatusId) : TestOutcomeStatus::getSuccessId(),
            'changed_by_server_validation' => $changedByServerValidation,
            'deleted_by' => $deletedBy,
        ]);
    }

     /**
     * Get logs data
     * @param $filters
     * @return mixed
     */
    public static function getLogs($filters)
    {
        $logs = new TransactionChangeLog();
        return $logs->setWhereQuery($filters)->orderBy('updated_at', 'desc')->paginate(25);
    }

    /**
     * Get Filters list
     * @param $filters
     * @return array
     */
    public static function getFilters($filters)
    {
        $logs = new TransactionChangeLog();
        return $logs->processFilters($filters);
    }

    /**
     * Where query based on filters selected by user
     * @param $filters
     * @return mixed
     */
    public function setWhereQuery($filters)
    {
        $model = DB::table('transaction_change_logs');
        if ($filters['user_id']) {
            $model->where('user_id', $filters['user_id']);
        }
        if ($filters['execution_id']) {
            $model->where('execution_id', $filters['execution_id']);
        }
        if ($filters['test_outcome_status_id']) {
            $model->where('test_outcome_status_id', $filters['test_outcome_status_id']);
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
            'test_outcome_status_id' => $this->setWhereQuery($filters)->groupBy('test_outcome_status_id')->pluck('test_outcome_status_id'),
        ];
        return $arr;
    }
}
