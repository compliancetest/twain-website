<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSuite extends Model
{

    protected $table = 'wp_test_suites';

    protected $primaryKey = 'suite_id';

    /**
     * Method used to generate string ID, e.g. 'TWAINIBOAPP_V1.0'
     * @return string
     */
    public function getStringId()
    {
        $identifierRow = PostMeta::where(['meta_key' => 'ts_identifier', 'post_id' => $this->suite_id])->first();
        $strId = $identifierRow->meta_value . ' v' . $this->version_major . '.' . $this->version_minor;
        if ($this->version_patch) {
            $strId .= '.' . $this->version_patch;
        }
        return $strId;
    }
}
