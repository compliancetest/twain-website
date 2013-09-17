<?php
/**
* ComplianceTest Blog Functions
*/
$postTypes = array('press-release', 'event', 'blog', 'link');

add_action("init", "ct_create_news_and_events_post_type");
function ct_create_news_and_events_post_type()
{
    
    //Press Releases
    register_post_type( 'press-release', 
        array(
            'labels' => array(
                            'name' => 'Press Releases',
                            'singular_name' => 'Press Release',
//                            'slug' => 'press-release',
                            'add_new' => 'Add New',
                            'add_new_item' => 'Add New Press',
                            'edit_item' => 'Edit Press',
                            'new_item' => 'New Press',
                            'all_items' => 'All Presses',
                            'view_item' => 'View Press',
                            'search_items' => 'Search Presses',
                            'not_found' => 'No presses found',
                            'not_found_in_trash' => 'No presses found in trash',
                            'parent_item_colon' => '',
                            'menu_name' => 'Press Releases'
                        ),
            'public'    => true,
            'publicly_queryable'    => true,
            'show_ui'   => true,
            'show_in_menu'  => true,
            'query_var' => true,
            'rewrite'   => array( 'slug' => 'press-releases' ),
            'capability_type'   => 'post',
            'has_archive'   => true, 
            'hierarchical'  => false,
            'menu_position' => null,
            'supports'      => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                        
        )
    );
    
    //Blogs
    register_post_type( 'blog', 
        array(
            'labels' => array(
                            'name' => 'Blogs',
                            'singular_name' => 'Blog',
                            'slug' => 'blog',
                            'add_new' => 'Add New',
                            'add_new_item' => 'Add New Blog',
                            'edit_item' => 'Edit Blog',
                            'new_item' => 'New Blog',
                            'all_items' => 'All Blogs',
                            'view_item' => 'View Blog',
                            'search_items' => 'Search Blogs',
                            'not_found' => 'No blogs found',
                            'not_found_in_trash' => 'No blogs found in trash',
                            'parent_item_colon' => '',
                            'menu_name' => 'Blogs'
                        ),
            'public'    => true,
            'publicly_queryable'    => true,
            'show_ui'   => true,
            'show_in_menu'  => true,
            'query_var' => true,
            'rewrite'   => array( 'slug' => 'blogs' ),
            'capability_type'   => 'post',
            'has_archive'   => true, 
            'hierarchical'  => false,
            'menu_position' => null,
            'supports'      => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                        
        )
    );
    
    
    //Events
    register_post_type( 'event', 
        array(
            'labels' => array(
                            'name' => 'Events',
                            'singular_name' => 'Event',
                            'slug' => 'event',
                            'add_new' => 'Add New',
                            'add_new_item' => 'Add New Event',
                            'edit_item' => 'Edit Event',
                            'new_item' => 'New Event',
                            'all_items' => 'All Events',
                            'view_item' => 'View Event',
                            'search_items' => 'Search Events',
                            'not_found' => 'No events found',
                            'not_found_in_trash' => 'No events found in trash',
                            'parent_item_colon' => '',
                            'menu_name' => 'Events'
                        ),
            'public'    => true,
            'publicly_queryable'    => true,
            'show_ui'   => true,
            'show_in_menu'  => true,
            'query_var' => true,
            'rewrite'   => array( 'slug' => 'events' ),
            'capability_type'   => 'post',
            'has_archive'   => 'events', 
            'hierarchical'  => false,
            'menu_position' => null,
            'supports'      => array('title', 'editor'/*, 'author', 'thumbnail', 'excerpt', 'comments'*/),
                        
        )
    );
    
    //link
    register_post_type( 'link', 
        array(
            'labels' => array(
                            'name' => 'Links',
                            'singular_name' => 'Link',
                            'slug' => 'link',
                            'add_new' => 'Add Link',
                            'add_new_item' => 'Add New Link',
                            'edit_item' => 'Edit Link',
                            'new_item' => 'New Link',
                            'all_items' => 'All Links',
                            'view_item' => 'View Link',
                            'search_items' => 'Search Links',
                            'not_found' => 'No links found',
                            'not_found_in_trash' => 'No links found in trash',
                            'parent_item_colon' => '',
                            'menu_name' => 'Links'
                        ),
            'public'    => true,
            'publicly_queryable'    => true,
            'show_ui'   => true,
            'show_in_menu'  => true,
            'query_var' => true,
            'rewrite'   => array( 'slug' => 'links' ),
            'capability_type'   => 'post',
            'has_archive'   => true, 
            'hierarchical'  => false,
            'menu_position' => null,
            'supports'      => array('title'/*, 'editor', 'author', 'thumbnail', 'excerpt', 'comments'*/),
                        
        )
    );
    flush_rewrite_rules();
}
//Add Community Meta Box 
add_action('add_meta_boxes', 'ct_add_community_metabox_to_blog');
function ct_add_community_metabox_to_blog()
{
    add_meta_box('community_metabox', 'Communities', 'ct_blog_community_metabox', 'event', 'side');
    add_meta_box('community_metabox', 'Communities', 'ct_blog_community_metabox', 'link', 'side');
    add_meta_box('community_metabox', 'Communities', 'ct_blog_community_metabox', 'press-release', 'side');
    add_meta_box('community_metabox', 'Communities', 'ct_blog_community_metabox', 'blog', 'side');
}
function ct_blog_community_metabox($post)
{    
    //Getting Communities
    $groups = groups_get_groups();
    //Getting Post Metadata
    $community_id = get_post_meta($post->ID, 'blog_community_id', true);
    ?>
    <div style="border: solid 1px #ddd">
        <div class="tabs-panel" style="padding: 5px 10px">        
            <ul class="categorychecklist form-no-clear" style="margin: 0;">
                <li><label class="selectit"><input type="radio" name="blog_community" value="0" <?php if(!$community_id){ ?>checked="checked" <?php }?> /> Not assigned</label></li>
            <?php foreach($groups['groups'] as $group){ ?>
                <li><label class="selectit"><input type="radio" name="blog_community" value="<?php echo $group->id?>" <?php if($community_id == $group->id){ ?>checked="checked" <?php }?> /> <?php echo $group->name?></label></li>
            <?php } ?>            
            </ul>
            <input type="hidden" name="blog_community_id_nonce" value="<?php echo wp_create_nonce('save_blog_community_id') ?>" />
        </div>
    </div>
    <?php
}


add_action('save_post', 'ct_save_community_for_blog');
function ct_save_community_for_blog($post_id)
{
    if(!isset($_POST['blog_community_id_nonce']))
        return $post_id;
    
    if(!wp_verify_nonce($_POST['blog_community_id_nonce'], 'save_blog_community_id'))
        return $post_id;
        
    $community_id = isset($_POST['blog_community']) ? $_POST['blog_community'] : 0;
    
    update_post_meta($post_id, 'blog_community_id', $community_id);
    
    return $post_id;
}

/**
* Getting Groups that have News/Events
*/

function ct_get_blog_communities($type = null, $year = null)
{
    global $wpdb;
    
    $query = "SELECT DISTINCT(pm.meta_value) FROM $wpdb->postmeta AS pm " .
             "LEFT JOIN $wpdb->posts AS p ON p.ID=pm.post_id ";
    $where = array(" pm.meta_key='blog_community_id' ", " p.post_status='publish' ", " pm.meta_value > 0 ");
    if($type)
    {
        $where[] = $wpdb->prepare(" p.post_type=%s ", $type);
    }
    
    if($year)
    {
        $where[] = $wpdb->prepare(" YEAR(p.post_date)=%s ", $year);
    }
    $query .= " WHERE " . implode(" AND ", $where);
    
    $ids = $wpdb->get_col($query);
    
    return $ids;
}

/**
* Getting years
*/

function ct_get_blog_years($type = null, $community_id = null)
{
    global $wpdb, $postTypes;
    
    $query = "SELECT DISTINCT(YEAR(p.post_date)) as pyear FROM $wpdb->posts AS p " .
             "LEFT JOIN $wpdb->postmeta AS pm ON p.ID=pm.post_id ";
    $where = array( " p.post_status='publish' ", " pm.meta_key='blog_community_id' " );
    
    if($type)
    {
        $where[] = $wpdb->prepare(" p.post_type=%s ", $type);
    }else{
        $where[] = $wpdb->prepare(" p.post_type IN ('" . implode("', '", $postTypes) . "') ");
    }
    
    if($community_id)
    {
        $where[] = $wpdb->prepare(" pm.meta_value=%d ", $community_id);
    }
    $query .= " WHERE " . implode(" AND ", $where) . " ORDER BY pyear DESC";
    
    $ids = $wpdb->get_col($query);
    
    return $ids;
}

/**
* Display Press Releases
*/
function ct_display_blog_articles($postType, $community_id = null, $year = null, $limit = 5, $showTitle = false)
{
    global $wp_post_types;
    
    $args = array(
        'posts_per_page'    =>  $limit,
        'post_type'         =>  $postType,
        'orderby'           =>  'date',
        'order'             =>  'desc',
        'cat'               =>  $cat->term_id
    );
    if($community_id)
        $args['meta_query'] = array(
                                array('key' => 'blog_community_id', 'value' => $community_id, 'compare' => '=')
                             );
    if($year)
        $args['year'] = $year;
        
    //Getting Articles
    $the_query = new WP_Query( $args );                            
    $articles = $the_query->get_posts();
    
    if($articles)
    {   
        if($showTitle)
        {
            ?><h3><?php echo $wp_post_types[$postType]->labels->name?></h3><?php
        }
        ?>                        
        
        <ul class="articles" id="<?php echo $postType?>">
        <?php
        foreach($articles as $article){                           
            if($postType == 'event'){
                ?>
                <li>
                    <h5><a href="<?php echo get_permalink($article->ID)?>"><?php echo get_the_title($article)?></a> (<span class="date"><?php echo get_post_meta($article->ID, 'event_date', true)?></span>)</h5>
                </li>
                <?php
            }else if($postType == 'link'){
                ?>
                <li>
                    <h5><a href="<?php echo get_post_meta($article->ID, 'link_url', true)?>"><?php echo get_the_title($article)?></a></h5>                                
                    <p><?php echo get_post_meta($article->ID, 'link_description', true)?></p>                    
                    <div class="clear"></div>                    
                </li>
                <?php
            }else{
                ?>
                <li>
                    <h5 class="left"><a href="<?php echo get_permalink($article->ID)?>"><?php echo get_the_title($article)?></a> <span class="date">(<?php echo formatDate($article->post_date);?>)</span></h5>                                
                    
                    <div class="clear"></div>
                    <?php
                        if(has_excerpt($article->ID))
                        {
                            ?><p><?php echo apply_filters( 'get_the_excerpt', $article->post_excerpt );?></p><?php
                        }
                    ?>
                </li>
                <?php
            }        
        }
        
        if($the_query->max_num_pages > 1){ 
            ?>
            <li class="readmore-articles"><a href="<?php echo get_post_type_archive_link($postType)?>">Read More <?php echo $wp_post_types[$postType]->labels->name?></a></li>
            <?php 
        }
        
        ?>
        </ul>
        <?php
        
    }
}
