<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{

    protected $table = 'wp_test_cases';

    protected $primaryKey = 'case_id';

    /**
     * Get test case Test Execution profile
     * @return bool
     */
    public function getTestExecutionProfileId()
    {
        $result = PostMeta::where(array('post_id' => $this->case_id, 'meta_key' => 'test_execution'))->first();
        if ($result) {
            return $result->meta_value;
        }
        return false;
    }

    /**
     * Get test case Test Data Profile
     * @return bool
     */
    public function getTestDataProfileId()
    {
        $result = PostMeta::where(array('post_id' => $this->case_id, 'meta_key' => 'test_data_profile'))->first();
        if ($result) {
            return $result->meta_value;
        }
        return false;
    }

    /**
     * Method used to generate string ID, e.g. 'SC-01 v1.0'
     * @return string
     */
    public function getStringId()
    {
        $strId = $this->case_name . ' v' . $this->version_major . '.' . $this->version_minor;
        if ($this->version_patch) {
            $strId .= '.' . $this->version_patch;
        }
        return $strId;
    }

    /**
     * Check profile's is_optional flag
     * @return bool
     */
    public function isOptional($testCaseId)
    {
        $result = PostMeta::where(['post_id' => $testCaseId, 'meta_key' => 'testcase_status', 'meta_value' => 'Yes'])->first();
        if ($result) {
            return true;
        }
        return false;
    }

}
