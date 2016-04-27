<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityMeta extends Model
{

    use UuidTrait;

    public $incrementing = false;

    protected $table = 'communities_meta';

    protected $fillable = array('community_id', 'meta_key', 'meta_value');

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    public static function handleForum($createForum, $community, $groupStatus = 'public')
    {
        return true;
        $forum_id = 0;
        $forum_ids = bbp_get_group_forum_ids($community->id);

        if (!empty($forum_ids))
            $forum_id = (int)is_array($forum_ids) ? $forum_ids[0] : $forum_ids;

        // Create a forum, or not
        switch ($createForum) {
            case true  :

                // Bail if initial content was already created
                if (!empty($forum_id))
                    return;

                // Set the default forum status
                switch ($groupStatus) {
                    case 'hidden'  :
                        $status = bbpress()->hidden_status_id;
                        break;
                    case 'private' :
                        $status = bbpress()->private_status_id;
                        break;
                    case 'public'  :
                    default        :
                        $status = bbpress()->public_status_id;
                        break;
                }

                // Create the initial forum
                $forum_id = bbp_insert_forum(array(
                    'post_parent' => (int)get_option('_bbp_group_forums_root_id', 0),
                    'post_title' => $community->title,
                    'post_content' => $community->description,
                    'post_status' => $status
                ));

                // Toggle forum on
                CommunityMeta::updateOrCreate(['community_id' => $community->id, 'meta_key' => 'forum_id', 'meta_value' => $forum_id]);

                break;
            case false :

                // Forum was created but is now being undone
                if (!empty($forum_id)) {

                    // Delete the forum
                    wp_delete_post($forum_id, true);

                    // Delete meta values
                    $metaData = CommunityMeta::find(['community_id' => $community->id]);
                    $metaData->delete();

                }
                $communityMeta = Community::findFirst(['community_id' => $community->id, 'meta_key' => 'forum_id']);
                CommunityMeta::destroy($communityMeta->id);

                break;
        }
    }
}
