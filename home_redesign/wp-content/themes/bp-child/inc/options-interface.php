<?php

/**
* Get all theme files
*/

function listFolderFiles($dir,$exclude){ 
    $ffs = scandir($dir); 
    $files .=  '<select class="ulli clone-template">'; 
    foreach($ffs as $ff){ 
        if(is_array($exclude) and !in_array($ff,$exclude)){ 
            if($ff != '.' && $ff != '..'){ 
            if(!is_dir($dir.'/'.$ff)){ 
            //$files .=  '<li><a href="edit_page.php?path='.ltrim($dir.'/'.$ff,'./').'">'.$ff.'</a>'; 
            $files .=  '<option value="'.ltrim($dir.'/'.$ff,'./').'">'.$ff.'</option>'; 
            } else { 
            $files .=  '<option>'.$ff;    
            } 
            if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff,$exclude); 
            $files .=  '</option>'; 
            } 
        } 
    } 
    $files .= '</select>';
    
    return $files; 
} 

/**
* Get the custom post types
*/

function cpt_save_postdata() {
		    global $post;
		    if ($_POST['cpt-hidd'] == 'true') {
		        $cp_public = get_post_meta($post->ID, 'cp_public', true);
		        $cp_publicly_queryable = get_post_meta($post->ID, 'cp_publicly_queryable', true);
		        $cp_show_ui = get_post_meta($post->ID, 'cp_show_ui', true);
		        $cp_show_in_menu = get_post_meta($post->ID, 'cp_show_in_menu', true); 
		        $cp_query_var = get_post_meta($post->ID, 'cp_query_var', true); 
		        $cp_rewrite = get_post_meta($post->ID, 'cp_rewrite', true); 
		        $cp_has_archive = get_post_meta($post->ID, 'cp_has_archive', true); 
		        $cp_hierarchical = get_post_meta($post->ID, 'cp_hierarchical', true);
		        $cp_capability_type = get_post_meta($post->ID, 'cp_capability_type', true);
		        $cp_menu_position = get_post_meta($post->ID, 'cp_menu_position', true);
		        $cp_s_title = get_post_meta($post->ID, 'cp_s_title', true);
		        $cp_s_editor = get_post_meta($post->ID, 'cp_s_editor', true);
		        $cp_s_author = get_post_meta($post->ID, 'cp_s_author', true);
		        $cp_s_thumbnail = get_post_meta($post->ID, 'cp_s_thumbnail', true);
		        $cp_s_excerpt = get_post_meta($post->ID, 'cp_s_excerpt', true);
		        $cp_s_comments = get_post_meta($post->ID, 'cp_s_comments', true);
		        $cp_general_name = get_post_meta($post->ID, 'cp_general_name', true);
		        $cp_singular_name = get_post_meta($post->ID, 'cp_singular_name', true);
		        $cp_add_new = get_post_meta($post->ID, 'cp_add_new', true);
		        $cp_add_new_item = get_post_meta($post->ID, 'cp_add_new_item', true);
		        $cp_edit_item = get_post_meta($post->ID, 'cp_edit_item', true);
		        $cp_new_item = get_post_meta($post->ID, 'cp_new_item', true);
		        $cp_all_items = get_post_meta($post->ID, 'cp_all_items', true);
		        $cp_view_item = get_post_meta($post->ID, 'cp_view_item', true);
		        $cp_search_items = get_post_meta($post->ID, 'cp_search_items', true);
		        $cp_not_found = get_post_meta($post->ID, 'cp_not_found', true);
		        $cp_not_found_in_trash = get_post_meta($post->ID, 'cp_not_found_in_trash', true);
		        $cp_parent_item_colon = get_post_meta($post->ID, 'cp_parent_item_colon', true);
		
		        update_post_meta($post->ID, 'cp_public', $_POST['cp_public'], $cp_public);
		        update_post_meta($post->ID, 'cp_publicly_queryable', $_POST['cp_publicly_queryable'], $cp_publicly_queryable);
		        update_post_meta($post->ID, 'cp_show_ui', $_POST['cp_show_ui'], $cp_show_ui);
		        update_post_meta($post->ID, 'cp_show_in_menu', $_POST['cp_show_in_menu'], $cp_show_in_menu);
		        update_post_meta($post->ID, 'cp_query_var', $_POST['cp_query_var'], $cp_query_var);
		        update_post_meta($post->ID, 'cp_rewrite', $_POST['cp_rewrite'], $cp_rewrite);
		        update_post_meta($post->ID, 'cp_has_archive', $_POST['cp_has_archive'], $cp_has_archive);
		        update_post_meta($post->ID, 'cp_hierarchical', $_POST['cp_hierarchical'], $cp_hierarchical);
		        update_post_meta($post->ID, 'cp_capability_type', $_POST['cp_capability_type'], $cp_capability_type);
		        update_post_meta($post->ID, 'cp_menu_position', $_POST['cp_menu_position'], $cp_menu_position);
		        update_post_meta($post->ID, 'cp_s_title', $_POST['cp_s_title'], $cp_s_title);
		        update_post_meta($post->ID, 'cp_s_editor', $_POST['cp_s_editor'], $cp_s_editor);
		        update_post_meta($post->ID, 'cp_s_author', $_POST['cp_s_author'], $cp_s_author);
		        update_post_meta($post->ID, 'cp_s_thumbnail', $_POST['cp_s_thumbnail'], $cp_s_thumbnail);
		        update_post_meta($post->ID, 'cp_s_excerpt', $_POST['cp_s_excerpt'], $cp_s_excerpt);
		        update_post_meta($post->ID, 'cp_s_comments', $_POST['cp_s_comments'], $cp_s_comments);
		        update_post_meta($post->ID, 'cp_general_name', $_POST['cp_general_name'], $cp_general_name);
		        update_post_meta($post->ID, 'cp_singular_name', $_POST['cp_singular_name'], $cp_singular_name);
		        update_post_meta($post->ID, 'cp_add_new', $_POST['cp_add_new'], $cp_add_new);
		        update_post_meta($post->ID, 'cp_add_new_item', $_POST['cp_add_new_item'], $cp_add_new_item);
		        update_post_meta($post->ID, 'cp_edit_item', $_POST['cp_edit_item'], $cp_edit_item);
		        update_post_meta($post->ID, 'cp_new_item', $_POST['cp_new_item'], $cp_new_item);
		        update_post_meta($post->ID, 'cp_all_items', $_POST['cp_all_items'], $cp_all_items);
		        update_post_meta($post->ID, 'cp_view_item', $_POST['cp_view_item'], $cp_view_item);
		        update_post_meta($post->ID, 'cp_search_items', $_POST['cp_search_items'], $cp_search_items);
		        update_post_meta($post->ID, 'cp_not_found', $_POST['cp_not_found'], $cp_not_found);
		        update_post_meta($post->ID, 'cp_not_found_in_trash', $_POST['cp_not_found_in_trash'], $cp_not_found_in_trash);
		        update_post_meta($post->ID, 'cp_parent_item_colon', $_POST['cp_parent_item_colon'], $cp_parent_item_colon);
		    }
		}
		
		function cpt_inner_custom_box() {
		    global $post;
		
		    $cp_public = get_post_meta($post->ID, 'cp_public', true);
		    $cp_publicly_queryable = get_post_meta($post->ID, 'cp_publicly_queryable', true);
		    $cp_show_ui = get_post_meta($post->ID, 'cp_show_ui', true);
		    $cp_show_in_menu = get_post_meta($post->ID, 'cp_show_in_menu', true); 
		    $cp_query_var = get_post_meta($post->ID, 'cp_query_var', true); 
		    $cp_rewrite = get_post_meta($post->ID, 'cp_rewrite', true); 
		    $cp_has_archive = get_post_meta($post->ID, 'cp_has_archive', true); 
		    $cp_hierarchical = get_post_meta($post->ID, 'cp_hierarchical', true);
		    $cp_capability_type = get_post_meta($post->ID, 'cp_capability_type', true);
		    $cp_menu_position = get_post_meta($post->ID, 'cp_menu_position', true);
		    $cp_s_title = get_post_meta($post->ID, 'cp_s_title', true);
		    $cp_s_editor = get_post_meta($post->ID, 'cp_s_editor', true);
		    $cp_s_author = get_post_meta($post->ID, 'cp_s_author', true);
		    $cp_s_thumbnail = get_post_meta($post->ID, 'cp_s_thumbnail', true);
		    $cp_s_excerpt = get_post_meta($post->ID, 'cp_s_excerpt', true);
		    $cp_s_comments = get_post_meta($post->ID, 'cp_s_comments', true);
		    $cp_general_name = get_post_meta($post->ID, 'cp_general_name', true);
		    $cp_singular_name = get_post_meta($post->ID, 'cp_singular_name', true);
		    $cp_add_new = get_post_meta($post->ID, 'cp_add_new', true);
		    $cp_add_new_item = get_post_meta($post->ID, 'cp_add_new_item', true);
		    $cp_edit_item = get_post_meta($post->ID, 'cp_edit_item', true);
		    $cp_new_item = get_post_meta($post->ID, 'cp_new_item', true);
		    $cp_all_items = get_post_meta($post->ID, 'cp_all_items', true);
		    $cp_view_item = get_post_meta($post->ID, 'cp_view_item', true);
		    $cp_search_items = get_post_meta($post->ID, 'cp_search_items', true);
		    $cp_not_found = get_post_meta($post->ID, 'cp_not_found', true);
		    $cp_not_found_in_trash = get_post_meta($post->ID, 'cp_not_found_in_trash', true);
		    $cp_parent_item_colon = get_post_meta($post->ID, 'cp_parent_item_colon', true);
		    ?>
		    <h4>Main Settings:</h4>
		    <table width="100%">
		        <tr>
		            <td><input type="checkbox" <?php
		    if ($cp_public == "on") {
		        echo "checked";
		    }
		    ?> name="cp_public" /> Public </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_publicly_queryable == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_publicly_queryable" /> Publicly Queryable </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_show_ui == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_show_ui" /> Show UI </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_show_in_menu == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_show_in_menu" /> Show in Menu </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_query_var == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_query_var" /> Query Var </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_rewrite == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_rewrite" /> Rewrite </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_has_archive == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_has_archive" /> Has Archive </td>
		            <td><input type="checkbox" <?php
		                   if ($cp_hierarchical == "on") {
		                       echo "checked";
		                   }
		    ?> name="cp_hierarchical" /> Hierarchical </td>
		        </tr>
		    </table>
		    <br/>
		    <table>
		        <tr>
		            <td>Capability Type:<br/><select name="cp_capability_type">
		                    <option value="5" <?php
		                   if ($cp_capability_type == "5") {
		                       echo "selected";
		                   }
		    ?>>below Posts</option>
		                    <option value="10" <?php
		                        if ($cp_capability_type == "10") {
		                            echo "selected";
		                        }
		    ?>>below Media</option>
		                    <option value="15" <?php
		                        if ($cp_capability_type == "15") {
		                            echo "selected";
		                        }
		    ?>>below Links</option>
		                    <option value="20" <?php
		                        if ($cp_capability_type == "20") {
		                            echo "selected";
		                        }
		    ?>>below Pages</option>
		                    <option value="25" <?php
		                        if ($cp_capability_type == "25") {
		                            echo "selected";
		                        }
		    ?>>below comments</option>
		                    <option value="60" <?php
		                        if ($cp_capability_type == "60") {
		                            echo "selected";
		                        }
		    ?>>below first separator</option>
		                    <option value="65" <?php
		                        if ($cp_capability_type == "65") {
		                            echo "selected";
		                        }
		    ?>>below Plugins</option>
		                    <option value="70" <?php
		                        if ($cp_capability_type == "70") {
		                            echo "selected";
		                        }
		    ?>>below Users</option>
		                    <option value="75" <?php
		                        if ($cp_capability_type == "75") {
		                            echo "selected";
		                        }
		    ?>>below Tools</option>
		                    <option value="80" <?php
		                        if ($cp_capability_type == "80") {
		                            echo "selected";
		                        }
		    ?>>below Settings</option>
		                    <option value="100" <?php
		                        if ($cp_capability_type == "100") {
		                            echo "selected";
		                        }
		    ?>>below second separator</option>
		
		                </select></td>
		            <td>Menu Position:<br/><select name="cp_menu_position">
		                    <option value="post" <?php
		                        if ($cp_menu_position == "post") {
		                            echo "selected";
		                        }
		    ?>>Post</option>
		                    <option value="page" <?php
		                        if ($cp_menu_position == "page") {
		                            echo "selected";
		                        }
		    ?>>Page</option>
		                </select></td>
		        </tr>
		    </table>
		    <h4>Supports:</h4>
		    <table width="100%">
		        <tr>
		            <td><input type="checkbox" name="cp_s_title" <?php
		                        if ($cp_s_title == "on") {
		                            echo "checked";
		                        }
		    ?>/> Title </td>
		            <td><input type="checkbox" name="cp_s_editor" <?php
		                   if ($cp_s_editor == "on") {
		                       echo "checked";
		                   }
		    ?>/> Editor  </td>
		            <td><input type="checkbox" name="cp_s_author" <?php
		                   if ($cp_s_author == "on") {
		                       echo "checked";
		                   }
		    ?>/> Author </td> 
		            <td><input type="checkbox" name="cp_s_thumbnail" <?php
		                   if ($cp_s_thumbnail == "on") {
		                       echo "checked";
		                   }
		    ?>/> Thumbnail  </td>
		            <td><input type="checkbox" name="cp_s_excerpt" <?php
		                   if ($cp_s_excerpt == "on") {
		                       echo "checked";
		                   }
		    ?>/> Excerpt  </td>
		            <td><input type="checkbox" name="cp_s_comments" <?php
		                   if ($cp_s_comments == "on") {
		                       echo "checked";
		                   }
		    ?>/> Comments  </td>
		        </tr>
		    </table>
		    <h4>labels:</h4>
		    <table width="100%">
		        <tr>
		            <td>General name:<br/> <input type="text" name="cp_general_name" value="<?php echo $cp_general_name; ?>"/></td>
		            <td>Singular name:<br/> <input type="text" name="cp_singular_name" value="<?php echo $cp_singular_name; ?>"/></td>
		            <td>Add new:<br/> <input type="text" name="cp_add_new" value="<?php echo $cp_add_new; ?>"/></td>
		        </tr>
		        <tr>
		            <td>Add new item:<br/> <input type="text" name="cp_add_new_item" value="<?php echo $cp_add_new_item; ?>"/></td>
		            <td>Edit Item:<br/> <input type="text" name="cp_edit_item" value="<?php echo $cp_edit_item; ?>"/></td>
		            <td>New Item:<br/> <input type="text" name="cp_new_item" value="<?php echo $cp_new_item; ?>"/></td>
		        </tr>
		        <tr>
		            <td>All Items:<br/> <input type="text" name="cp_all_items" value="<?php echo $cp_all_items; ?>"/></td>
		            <td>View Item:<br/> <input type="text" name="cp_view_item" value="<?php echo $cp_view_item; ?>"/></td>
		            <td>Search Items:<br/> <input type="text" name="cp_search_items" value="<?php echo $cp_search_items; ?>"/></td>
		        </tr>
		        <tr>
		            <td>Not Found:<br/> <input type="text" name="cp_not_found" value="<?php echo $cp_not_found; ?>"/></td>
		            <td>Not Found in Trash:<br/> <input type="text" name="cp_not_found_in_trash" value="<?php echo $cp_not_found_in_trash; ?>"/></td>
		            <td>Parent item Column:<br/> <input type="text" name="cp_parent_item_colon" value="<?php echo $cp_parent_item_colon; ?>"/></td>
		        </tr>
		    </table>
		    <input type="hidden" name="cpt-hidd" value="true" />
		    <?php
		}
		
		function init_custom_post_types() {
		    $labels = array(
		        'name' => _x('CPT', 'post type general name'),
		        'singular_name' => _x('CPT', 'post type singular name'),
		        'add_new' => _x('Add New CPT', 'CPT'),
		        'add_new_item' => __('Add New Post type'),
		        'edit_item' => __('Edit CPT'),
		        'new_item' => __('New CPT'),
		        'all_items' => __('All CPT'),
		        'view_item' => __('View CPT'),
		        'search_items' => __('Search CPT'),
		        'not_found' => __('No CPT found'),
		        'not_found_in_trash' => __('No CPT found in Trash'),
		        'parent_item_colon' => '',
		        'menu_name' => __('CPT')
		    );
		    $args = array(
		        'labels' => $labels,
		        'public' => true,
		        'publicly_queryable' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'query_var' => true,
		        'rewrite' => true,
		        'capability_type' => 'post',
		        'has_archive' => true,
		        'hierarchical' => false,
		        'menu_position' => null,
		        'supports' => array('title')
		    );
		    register_post_type('CPT', $args);
		
		    $the_query = new WP_Query(array('post_type' => array('CPT')));
		    while ($the_query->have_posts()) : $the_query->the_post();
		        global $post;
		        //*************************get the values
		        $cp_public = get_post_meta($post->ID, 'cp_public', true);
		        if ($cp_public == "on") {
		            $cp_public = true;
		        } else {
		            $cp_public = false;
		        }
		        $cp_publicly_queryable = get_post_meta($post->ID, 'cp_publicly_queryable', true);
		        if ($cp_publicly_queryable == "on") {
		            $cp_publicly_queryable = true;
		        } else {
		            $cp_publicly_queryable = false;
		        }
		        $cp_show_ui = get_post_meta($post->ID, 'cp_show_ui', true);
		        if ($cp_show_ui == "on") {
		            $cp_show_ui = true;
		        } else {
		            $cp_show_ui = false;
		        }
		        $cp_show_in_menu = get_post_meta($post->ID, 'cp_show_in_menu', true); //
		        if ($cp_show_in_menu == "on") {
		            $cp_show_in_menu = true;
		        } else {
		            $cp_show_in_menu = false;
		        }
		        $cp_query_var = get_post_meta($post->ID, 'cp_query_var', true); //
		        if ($cp_query_var == "on") {
		            $cp_query_var = true;
		        } else {
		            $cp_query_var = false;
		        }
		        $cp_rewrite = get_post_meta($post->ID, 'cp_rewrite', true); //
		        if ($cp_rewrite == "on") {
		            $cp_rewrite = true;
		        } else {
		            $cp_rewrite = false;
		        }
		        $cp_has_archive = get_post_meta($post->ID, 'cp_has_archive', true); //
		        if ($cp_has_archive == "on") {
		            $cp_has_archive = true;
		        } else {
		            $cp_has_archive = false;
		        }
		        $cp_hierarchical = get_post_meta($post->ID, 'cp_hierarchical', true);
		        if ($cp_hierarchical == "on") {
		            $cp_hierarchical = true;
		        } else {
		            $cp_hierarchical = false;
		        }
		        $cp_capability_type = get_post_meta($post->ID, 'cp_capability_type', true);
		        $cp_menu_position = get_post_meta($post->ID, 'cp_menu_position', true);
		        $cp_s_title = get_post_meta($post->ID, 'cp_s_title', true);
		        if ($cp_s_title == "on") {
		            $cp_s[] = 'title';
		        }
		        $cp_s_editor = get_post_meta($post->ID, 'cp_s_editor', true);
		        if ($cp_s_editor == "on") {
		            $cp_s[] = 'editor';
		        }
		        $cp_s_author = get_post_meta($post->ID, 'cp_s_author', true);
		        if ($cp_s_author == "on") {
		            $cp_s[] = 'author';
		        }
		        $cp_s_thumbnail = get_post_meta($post->ID, 'cp_s_thumbnail', true);
		        if ($cp_s_thumbnail == "on") {
		            $cp_s[] = 'thumbnail';
		        }
		        $cp_s_excerpt = get_post_meta($post->ID, 'cp_s_excerpt', true);
		        if ($cp_s_excerpt == "on") {
		            array_push($cp_s, 'excerpt');
		        }
		        $cp_s_comments = get_post_meta($post->ID, 'cp_s_comments', true);
		        if ($cp_s_comments == "on") {
		            array_push($cp_s, 'comments');
		        }
		        $cp_general_name = get_post_meta($post->ID, 'cp_general_name', true);
		        $cp_singular_name = get_post_meta($post->ID, 'cp_singular_name', true);
		        $cp_add_new = get_post_meta($post->ID, 'cp_add_new', true);
		        $cp_add_new_item = get_post_meta($post->ID, 'cp_add_new_item', true);
		        $cp_edit_item = get_post_meta($post->ID, 'cp_edit_item', true);
		        $cp_new_item = get_post_meta($post->ID, 'cp_new_item', true);
		        $cp_all_items = get_post_meta($post->ID, 'cp_all_items', true);
		        $cp_view_item = get_post_meta($post->ID, 'cp_view_item', true);
		        $cp_search_items = get_post_meta($post->ID, 'cp_search_items', true);
		        $cp_not_found = get_post_meta($post->ID, 'cp_not_found', true);
		        $cp_not_found_in_trash = get_post_meta($post->ID, 'cp_not_found_in_trash', true);
		        $cp_parent_item_colon = get_post_meta($post->ID, 'cp_parent_item_colon', true);
		
		        $labels = array(
		            'name' => _x(get_the_title($post->ID), 'post type general name'),
		            'singular_name' => _x($cp_singular_name, 'post type singular name'),
		            'add_new' => _x($cp_add_new, get_the_title($post->ID)),
		            'add_new_item' => __($cp_add_new_item),
		            'edit_item' => __($cp_edit_item),
		            'new_item' => __($cp_new_item),
		            'all_items' => __($cp_all_items),
		            'view_item' => __($cp_view_item),
		            'search_items' => __($cp_search_items),
		            'not_found' => __($cp_not_found),
		            'not_found_in_trash' => __($cp_not_found_in_trash),
		            'parent_item_colon' => __($cp_parent_item_colon),
		            'menu_name' => __(get_the_title($post->ID))
		        );
		        $args = array(
		            'labels' => $labels,
		            'public' => $cp_public,
		            'publicly_queryable' => $cp_publicly_queryable,
		            'show_ui' => $cp_show_ui,
		            'show_in_menu' => $cp_show_in_menu,
		            'query_var' => $cp_query_var,
		            'rewrite' => $cp_rewrite,
		            'capability_type' => 'post',
		            'has_archive' => $cp_has_archive,
		            'hierarchical' => $cp_hierarchical,
		            'menu_position' => $cp_menu_position,
		            'supports' => $cp_s
		        );
		        register_post_type(get_the_title($post->ID), $args);
		
		    endwhile;
		}
		
		function cpt_add_meta_boxes() {
		    add_meta_box('cpt_meta_id', 'Custom Post Type Settings', 'cpt_inner_custom_box', 'CPT', 'normal');
		}
		
		add_action('save_post', 'cpt_save_postdata');
		add_action('add_meta_boxes', 'cpt_add_meta_boxes');
		add_action('init', 'init_custom_post_types');

/**
 * Generates the tabs that are used in the options menu
 */

function optionsframework_tabs() {

	$optionsframework_settings = get_option('optionsframework');
	$options = optionsframework_options();
	$menu = '';

	foreach ($options as $value) {
		// Heading for Navigation
		if ($value['type'] == "heading") {
			$jquery_click_hook = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['name']) );
			$jquery_click_hook = "of-option-" . $jquery_click_hook;
			$menu .= '<a id="'.  esc_attr( $jquery_click_hook ) . '-tab" class="nav-tab" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#'.  $jquery_click_hook ) . '">' . esc_html( $value['name'] ) . '</a>';
		}
	}

	return $menu;
}

/**
 * Generates the options fields that are used in the form.
 */

function optionsframework_fields() {

	global $allowedtags;
	$optionsframework_settings = get_option('optionsframework');

	// Gets the unique option id
	if ( isset( $optionsframework_settings['id'] ) ) {
		$option_name = $optionsframework_settings['id'];
	}
	else {
		$option_name = 'optionsframework';
	};

	$settings = get_option($option_name);
	$options = optionsframework_options();

	$counter = 0;
	$menu = '';

	foreach ( $options as $value ) {

		$counter++;
		$val = '';
		$select_value = '';
		$checked = '';
		$output = '';

		// Wrap all options
		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) ) {

			// Keep all ids lowercase with no spaces
			$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );

			$id = 'section-' . $value['id'];

			$class = 'section ';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<div id="' . esc_attr( $id ) .'" class="' . esc_attr( $class ) . '">'."\n";
			if ( isset( $value['name'] ) ) {
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			}
			if ( $value['type'] != 'editor' ) {
				$output .= '<div class="option">' . "\n" . '<div class="controls">' . "\n";
			}
			else {
				$output .= '<div class="option">' . "\n" . '<div>' . "\n";
			}
		}

		// Set default value to $val
		if ( isset( $value['std'] ) ) {
			$val = $value['std'];
		}

		// If the option is already saved, ovveride $val
		if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info') ) {
			if ( isset( $settings[($value['id'])]) ) {
				$val = $settings[($value['id'])];
				// Striping slashes of non-array options
				if ( !is_array($val) ) {
					$val = stripslashes( $val );
				}
			}
		}

		// If there is a description save it for labels
		$explain_value = '';
		if ( isset( $value['desc'] ) ) {
			$explain_value = $value['desc'];
		}

		switch ( $value['type'] ) {

		// Basic text input
		case 'text':
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
			break;

		// Textarea
		case 'textarea':
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
				$custom_rows = $value['settings']['rows'];
				if ( is_numeric( $custom_rows ) ) {
					$rows = $custom_rows;
				}
			}

			$val = stripslashes( $val );
			$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
			break;

		// Select Box
		case ($value['type'] == 'select'):
			$output .= '<select class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

			foreach ($value['options'] as $key => $option ) {
				$selected = '';
				if ( $val != '' ) {
					if ( $val == $key) { $selected = ' selected="selected"';}
				}
				$output .= '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			}
			$output .= '</select>';
			break;


		// Radio Box
		case "radio":
			$name = $option_name .'['. $value['id'] .']';
			foreach ($value['options'] as $key => $option) {
				$id = $option_name . '-' . $value['id'] .'-'. $key;
				$output .= '<input class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' /><label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label>';
			}
			break;

		// Image Selectors
		case "images":
			$name = $option_name .'['. $value['id'] .']';
			foreach ( $value['options'] as $key => $option ) {
				$selected = '';
				$checked = '';
				if ( $val != '' ) {
					if ( $val == $key ) {
						$selected = ' of-radio-img-selected';
						$checked = ' checked="checked"';
					}
				}
				$output .= '<input type="radio" id="' . esc_attr( $value['id'] .'_'. $key) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" '. $checked .' />';
				$output .= '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
				$output .= '<img src="' . esc_url( $option ) . '" alt="' . $option .'" class="of-radio-img-img' . $selected .'" onclick="document.getElementById(\''. esc_attr($value['id'] .'_'. $key) .'\').checked=true;" />';
			}
			break;

		// Checkbox
		case "checkbox":
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' />';
			$output .= '<label class="explain" for="' . esc_attr( $value['id'] ) . '">' . wp_kses( $explain_value, $allowedtags) . '</label>';
			break;

		// Multicheck
		case "multicheck":
			foreach ($value['options'] as $key => $option) {
				$checked = '';
				$label = $option;
				$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

				$id = $option_name . '-' . $value['id'] . '-'. $option;
				$name = $option_name . '[' . $value['id'] . '][' . $option .']';

				if ( isset($val[$option]) ) {
					$checked = checked($val[$option], 1, false);
				}

				$output .= '<input id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			}
			break;

		// Color picker
		case "color":
			$output .= '<div id="' . esc_attr( $value['id'] . '_picker' ) . '" class="colorSelector"><div style="' . esc_attr( 'background-color:' . $val ) . '"></div></div>';
			$output .= '<input class="of-color" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" type="text" value="' . esc_attr( $val ) . '" />';
			break;

		// Uploader
		case "upload":
			$output .= optionsframework_medialibrary_uploader( $value['id'], $val, null );
			break;

			// Typography
		case 'typography':
		
			unset( $font_size, $font_style, $font_face, $font_color );
		
			$typography_defaults = array(
				'size' => '',
				'face' => '',
				'style' => '',
				'color' => ''
			);
			
			$typography_stored = wp_parse_args( $val, $typography_defaults );
			
			$typography_options = array(
				'sizes' => of_recognized_font_sizes(),
				'faces' => of_recognized_font_faces(),
				'styles' => of_recognized_font_styles(),
				'color' => true
			);
			
			if ( isset( $value['options'] ) ) {
				$typography_options = wp_parse_args( $value['options'], $typography_options );
			}

			// Font Size
			if ( $typography_options['sizes'] ) {
				$font_size = '<select class="of-typography of-typography-size" name="' . esc_attr( $option_name . '[' . $value['id'] . '][size]' ) . '" id="' . esc_attr( $value['id'] . '_size' ) . '">';
				$sizes = $typography_options['sizes'];
				foreach ( $sizes as $i ) {
					$size = $i . 'px';
					$font_size .= '<option value="' . esc_attr( $size ) . '" ' . selected( $typography_stored['size'], $size, false ) . '>' . esc_html( $size ) . '</option>';
				}
				$font_size .= '</select>';
			}

			// Font Face
			if ( $typography_options['faces'] ) {
				$font_face = '<select class="of-typography of-typography-face" name="' . esc_attr( $option_name . '[' . $value['id'] . '][face]' ) . '" id="' . esc_attr( $value['id'] . '_face' ) . '">';
				$faces = $typography_options['faces'];
				foreach ( $faces as $key => $face ) {
					$font_face .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>';
				}
				$font_face .= '</select>';
			}

			// Font Styles
			if ( $typography_options['styles'] ) {
				$font_style = '<select class="of-typography of-typography-style" name="'.$option_name.'['.$value['id'].'][style]" id="'. $value['id'].'_style">';
				$styles = $typography_options['styles'];
				foreach ( $styles as $key => $style ) {
					$font_style .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
				}
				$font_style .= '</select>';
			}

			// Font Color
			if ( $typography_options['color'] ) {
				$font_color = '<div id="' . esc_attr( $value['id'] ) . '_color_picker" class="colorSelector"><div style="' . esc_attr( 'background-color:' . $typography_stored['color'] ) . '"></div></div>';
				$font_color .= '<input class="of-color of-typography of-typography-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $typography_stored['color'] ) . '" />';
			}
	
			// Allow modification/injection of typography fields
			$typography_fields = compact( 'font_size', 'font_face', 'font_style', 'font_color' );
			$typography_fields = apply_filters( 'of_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
			$output .= implode( '', $typography_fields );
			
			break;

		// Background
		case 'background':

			$background = $val;

			// Background Color
			$output .= '<div id="' . esc_attr( $value['id'] ) . '_color_picker" class="colorSelector"><div style="' . esc_attr( 'background-color:' . $background['color'] ) . '"></div></div>';
			$output .= '<input class="of-color of-background of-background-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $background['color'] ) . '" />';

			// Background Image - New AJAX Uploader using Media Library
			if (!isset($background['image'])) {
				$background['image'] = '';
			}

			$output .= optionsframework_medialibrary_uploader( $value['id'], $background['image'], null, '',0,'image');
			$class = 'of-background-properties';
			if ( '' == $background['image'] ) {
				$class .= ' hide';
			}
			$output .= '<div class="' . esc_attr( $class ) . '">';

			// Background Repeat
			$output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
			$repeats = of_recognized_background_repeat();

			foreach ($repeats as $key => $repeat) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
			}
			$output .= '</select>';

			// Background Position
			$output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
			$positions = of_recognized_background_position();

			foreach ($positions as $key=>$position) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
			}
			$output .= '</select>';

			// Background Attachment
			$output .= '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
			$attachments = of_recognized_background_attachment();

			foreach ($attachments as $key => $attachment) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';

			break;

		// Editor
		case 'editor':
			$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags) . '</div>'."\n";
			echo $output;
			$textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
			$default_editor_settings = array(
				'textarea_name' => $textarea_name,
				'media_buttons' => false,
				'tinymce' => array( 'plugins' => 'wordpress' )
			);
			$editor_settings = array();
			if ( isset( $value['settings'] ) ) {
				$editor_settings = $value['settings'];
			}
			$editor_settings = array_merge($editor_settings, $default_editor_settings);
			wp_editor( $val, $value['id'], $editor_settings );
			$output = '';
			break;
			
		//CUSTOM POST TYPES
		case "custom_post_types": 
			$output .= '<div class="constrainer">';
			$output .= '<h4 class="heading">Post Type Name</h4>';
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input post-type-title" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
					   
			$output .= '<h4 class="heading">Main Settings</h4>';
		        $output .= '<ul class="custom-post-types settings">';
		        		$output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_public == "on") ? "checked"   : "").' name="cp_public" /> Public';
		    			$output .= '</li>';
		            $output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_publicly_queryable== "on") ? "checked"   : "").' name="cp_publicly_queryable" /> Publicly Queryable'; 
		    		   $output .= '</li>';
		           $output .= ' <li>';
		            	$output .= '<input type="checkbox" '.( ($cp_show_ui== "on") ? "checked"   : "").' name="cp_show_ui" /> Show UI'; 
		    			$output .= '</li>';
		            $output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_show_in_menu== "on") ? "checked"   : "").' name="cp_show_in_menu" /> Show in Menu';
		    			$output .= '</li>';
		            $output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_query_var== "on") ? "checked"   : "").' name="cp_query_var" /> Query Var'; 
		    			$output .= '</li>';
		            $output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_rewrite== "on") ? "checked"   : "").' name="cp_rewrite" /> Rewrite';
		    			$output .= '</li>';
		    			$output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_has_archive== "on") ? "checked"   : "").' name="cp_has_archive" /> Has Archive'; 
		    			$output .= '</li>';
		            $output .= '<li>';
		            	$output .= '<input type="checkbox" '.( ($cp_hierarchical== "on") ? "checked"   : "").' name="cp_hierarchical" /> Hierarchical';
		    	 		$output .= '</li>';
		        $output .= '</ul>';
		        $output .= '<div class="clear"></div>';

			    $output .= '<ul class="custom-post-types capabilities">';
			       $output .= ' <li>';
			           $output .= ' <h4 class="heading">Capability Type:</h4>';
		            		$output .= '<select name="cp_capability_type">';
		                    $output .= '<option value="5" '.( ($cp_capability_type== "5") ? "selected"   : "").'>below Posts</option>';
		                    $output .= '<option value="10" '.( ($cp_capability_type== "10") ? "selected"   : "").'>below Media</option>';
		                    $output .= '<option value="15" '.( ($cp_capability_type== "15") ? "selected"   : "").'>below Links</option>';
		                    $output .= '<option value="20" '.( ($cp_capability_type== "20") ? "selected"   : "").'>below Pages</option>';
		                    $output .= '<option value="25" '.( ($cp_capability_type== "25") ? "selected"   : "").'>below comments</option>';
		                    $output .= '<option value="60" '.( ($cp_capability_type== "60") ? "selected"   : "").'>below first separator</option>';
		                    $output .= '<option value="65" '.( ($cp_capability_type== "65") ? "selected"   : "").'>below Plugins</option>';
		                    $output .= '<option value="70" '.( ($cp_capability_type== "70") ? "selected"   : "").'>below Users</option>';
		                    $output .= '<option value="75" '.( ($cp_capability_type== "75") ? "selected"   : "").'>below Tools</option>';
		                    $output .= '<option value="80" '.( ($cp_capability_type== "80") ? "selected"   : "").'>below Settings</option>';
		                    $output .= '<option value="100" '.( ($cp_capability_type== "100") ? "selected"   : "").'>below second separator</option>';
								$output .= '</select>';
					$output .= '</li>';
					$output .= '<li>';
		            $output .= '<h4 class="heading">Menu Position:</h4>';
		            		$output .= '<select name="cp_menu_position">';
		                    $output .= '<option value="post" '.( ($cp_menu_position== "post") ? "selected"   : "").'>Post</option>';
		                    $output .= '<option value="page" '.( ($cp_menu_position== "page") ? "selected"   : "").'>Page</option>';
		                $output .= '</select>';
			      $output .= '</li>';
			    $output .= '</ul>';
			    $output .= '<div class="clear"></div>';
		    
		    $output .= '<h4 class="heading">Supports:</h4>';
		        $output .= '<ul class="custom-post-types support">';
		            $output .= '<li><input type="checkbox" name="cp_s_title" '.( ($cp_s_title== "on") ? "checked"   : "").' /> Title </li>';
		            $output .= '<li><input type="checkbox" name="cp_s_editor" '.( ($ccp_s_editor== "on") ? "checked"   : "").' /> Editor  </li>';
		            $output .= '<li><input type="checkbox" name="cp_s_author" '.( ($cp_s_author== "on") ? "checked"   : "").' /> Author </li> ';
		            $output .= '<li><input type="checkbox" name="cp_s_thumbnail" '.( ($cp_s_thumbnail== "on") ? "checked"   : "").' /> Thumbnail  </li>';
		            $output .= '<li><input type="checkbox" name="cp_s_excerpt" '.( ($cp_s_excerpt== "on") ? "checked"   : "").' /> Excerpt  </li>';
		            $output .= '<li><input type="checkbox" name="cp_s_comments" '.( ($cp_s_comments== "on") ? "checked"   : "").' /> Comments  </li>';
		        $output .= '</ul>';
		        $output .= '<div class="clear"></div>';
		    
		   $output .= '<h4 class="heading">Labels:</h4>';
		        $output .= '<ul class="custom-post-types labels">';
		            $output .= '<li>General name:<br/> <input type="text" name="cp_general_name" value="'. $cp_general_name .'"/></li>';
		            $output .= '<li>Singular name:<br/> <input type="text" name="cp_singular_name" value="'. $cp_singular_name .'"/></li>';
		            $output .= '<li>Add new:<br/> <input type="text" name="cp_add_new" value="'. $cp_add_new .'"/></li>';
		            $output .= '<li>Add new item:<br/> <input type="text" name="cp_add_new_item" value="'. $cp_add_new_item .'"/></li>';
		            $output .= '<li>Edit Item:<br/> <input type="text" name="cp_edit_item" value="'. $cp_edit_item .'"/></li>';
		            $output .= '<li>New Item:<br/> <input type="text" name="cp_new_item" value="'. $cp_new_item .'"/></li>';
		            $output .= '<li>All Items:<br/> <input type="text" name="cp_all_items" value="'. $cp_all_items .'"/></li>';
		            $output .= '<li>View Item:<br/> <input type="text" name="cp_view_item" value="'. $cp_view_item .'"/></li>';
		            $output .= '<li>Search Items:<br/> <input type="text" name="cp_search_items" value="'. $cp_search_items .'"/></li>';
		            $output .= '<li>Not Found:<br/> <input type="text" name="cp_not_found" value="'. $cp_not_found .'"/></li>';
		            $output .= '<li>Not Found in Trash:<br/> <input type="text" name="cp_not_found_in_trash" value="'. $cp_not_found_in_trash .'"/></li>';
		            $output .= '<li>Parent item Column:<br/> <input type="text" name="cp_parent_item_colon" value="'. $cp_parent_item_colon .'"/></li>';
		        $output .= '</ul>';
				  $output .= '<div class="clear"></div>';
				  
				  
				  $output .= '<ul class="custom-post-types finals">';
				  		$output .= '<li>';
				  		$output .= '<h4 class="heading">Clone template from:</h4>';
				  			 $dir = get_template_directory();
								$output .= listFolderFiles($dir,array('functions','images','css','inc','fonts','js','license.txt','style.css','header.php','footer.php','sidebar.php','functions.php','options.php','screenshot.png')); 
				  		$output .='</li>';
				  		$output .='<li>';
				 		 $output .= '<h4 class="heading">Taxonomies:</h4>';
				  		 $output .= '<input type="checkbox" name="cp_s_taxonomy" '.( ($cp_s_taxonomy== "on") ? "checked"   : "").' /> Enable taxonomies for this post type</li>';
				  $output .= '</ul>';
				  $output .= '<div class="clear"></div>';
				  
				  $output .= '</div>';
				  
				  
				  $output .= '<div class="active-post-types">';
				  	 $output .= '<h4 class="heading">Active Custom Post Types:</h4>';
				  	 $output .= '<table class="wp-list-table widefat fixed pages" cellspacing="0">';
						$output .= '<thead>';
						$output .= '<tr>';
							$output .= '<th scope="col" id="title" class="manage-column column-title sortable desc" style=""><a href=""><span>Title</span><span class="sorting-indicator"></span></a></th></tr>';
						$output .= '</thead>';
					
						$output .= '<tfoot>';
						$output .= '<tr>';
							$output .= '<th scope="col" class="manage-column column-title sortable desc" style=""><a href=""><span>Title</span><span class="sorting-indicator"></span></a></th></tr>';
						$output .= '</tfoot>';
					
						$output .= '<tbody id="the-list">';
						$output .= '<tr id="post-2" class="post-2 page type-page status-publish hentry alternate iedit author-self" valign="top">';
								$output .= '<td class="post-title page-title column-title"><strong><a class="row-title" href="" title="Edit "Sample Post Type”">Sample Post Type</a></strong>';
									$output .= '<div class="row-actions"><span class="edit"><a href="" title="Edit this item">Edit</a> | </span><span class="trash"><a class="submitdelete" title="Move this item to the Trash" href="">Trash</a></div>';
									$output .= '<div class="hidden" id="inline_2">';
										$output .= '<div class="post_title">Sample Page</div>';
										$output .= '<div class="post_name">sample-page</div>';
										$output .= '<div class="post_author">1</div>';
										$output .= '<div class="comment_status">open</div>';
										$output .= '<div class="ping_status">open</div>';
										$output .= '<div class="_status">publish</div>';
										$output .= '<div class="jj">12</div>';
										$output .= '<div class="mm">11</div>';
										$output .= '<div class="aa">2012</div>';
										$output .= '<div class="hh">13</div>';
										$output .= '<div class="mn">49</div>';
										$output .= '<div class="ss">59</div>';
								$output .= '</td>';
							$output .= '</tr>';
						$output .= '</tbody>';
					$output .= '</table>';

				  $output .= '</div>';
		

		break;

		// Info
		case "info":
			$class = 'section';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<div class="' . esc_attr( $class ) . '">' . "\n";
			if ( isset($value['name']) ) {
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			}
			if ( $value['desc'] ) {
				$output .= apply_filters('of_sanitize_info', $value['desc'] ) . "\n";
			}
			$output .= '</div>' . "\n";
			break;

		// Heading for Navigation
		case "heading":
			if ($counter >= 2) {
				$output .= '</div>'."\n";
			}
			$jquery_click_hook = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['name']) );
			$jquery_click_hook = "of-option-" . $jquery_click_hook;
			$menu .= '<a id="'.  esc_attr( $jquery_click_hook ) . '-tab" class="nav-tab" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#'.  $jquery_click_hook ) . '">' . esc_html( $value['name'] ) . '</a>';
			$output .= '<div class="group" id="' . esc_attr( $jquery_click_hook ) . '">';
			$output .= '<h3>' . esc_html( $value['name'] ) . '</h3>' . "\n";
			break;
			
		
		}

		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) ) {
			$output .= '</div>';
			if ( ( $value['type'] != "checkbox" ) && ( $value['type'] != "editor" ) ) {
				$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags) . '</div>'."\n";
			}
			$output .= '</div></div>'."\n";
		}

		echo $output;
	}
	echo '</div>';
}