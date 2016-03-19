<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{

    protected $table = 'wp_test_cases';

    protected $primaryKey = 'case_id';

    public function getTestExecutionProfileId()
    {
        $result = PostMeta::where(array('post_id' => $this->case_id, 'meta_key' => 'test_execution'))->first();
        if($result){
            return $result->meta_value;
        }
        return false;
    }

}
