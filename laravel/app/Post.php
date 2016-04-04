<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'wp_posts';

    protected $primaryKey = 'ID';

    /**
     * Method used to generate string ID, e.g. 'Document_Imaging'
     * @return string
     */
    public function getProductStringId()
    {
        $identifierRow = PostMeta::where(['post_id' => $this->ID])->get()->keyBy('meta_key');
        $strId = $identifierRow['product_id']->meta_value;
        return $strId;
    }
}
