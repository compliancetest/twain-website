<?php
/*
  Plugin Name: Dynamic Custom Post Types
  Description: A custom built plugin that helps user generate dinamic custom post types.
  Author: Ciprian Turcu
  Version: 1.0
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
    <h4>Lables:</h4>
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
?>