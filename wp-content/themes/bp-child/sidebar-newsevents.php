<?php
/**
* Sidebar for News and Events Page
*/
global $postTypes, $year, $community_id, $wp_post_types;

$postTypes = array('press-release', 'event', 'blog', 'link');
$communities = ct_get_blog_communities(null, $year);

?>
<div id="newsevents-sidebar">
    <div class="expandable">
        <h6 class="exp_title">Type</h6>
        <div class="exp_content">
        <?php                                                             
            foreach($postTypes as $ptype)
            {
                ?><a href="<?php echo get_post_type_archive_link($ptype)?>"><?php echo $wp_post_types[$ptype]->labels->name ?></a><?php
            }
        ?>
        </div>
    </div>
    <div class="expandable">
        <h6 class="exp_title">Community</h6>
        <div class="exp_content">                           
        <?php                                 
            foreach($communities as $g_id)
            {
                $group = groups_get_group(array('group_id' => $g_id));
                
                ?>
                <a href="<?php the_permalink() ?>?community_id=<?php echo $g_id?>" <?php if($community_id == $g_id){?> class="current"<?php }?>>
                    <?php echo bp_get_group_name($group)?>                                    
                </a><?php
            }
        ?>
        </div>
    </div>                  
</div>  
<?php
