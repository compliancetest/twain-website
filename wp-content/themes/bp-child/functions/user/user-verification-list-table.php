<?php
/**
 * Users List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class CT_User_Verification_List_Table extends WP_List_Table {

    function __construct( $args = array() ) {
        parent::__construct( array(
            'singular' => 'user',
            'plural'   => 'users',
            'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
        ) );
    }

    function prepare_items() {
        global $role, $usersearch, $wpdb;

        $usersearch = isset( $_REQUEST['s'] ) ? trim( $_REQUEST['s'] ) : '';

        $role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

        $per_page = 'users_per_page';
        $users_per_page = $this->get_items_per_page( $per_page );

        $paged = $this->get_pagenum();

        $args = array(
            'number' => $users_per_page,
            'offset' => ( $paged-1 ) * $users_per_page,
            'role' => $role,
            'search' => $usersearch,
            'fields' => 'all_with_meta'
        );

        if ( '' !== $args['search'] )
            $args['search'] = '*' . $args['search'] . '*';

        if ( $this->is_site_users )
            $args['blog_id'] = $this->site_id;

        if ( isset( $_REQUEST['orderby'] ) )
            $args['orderby'] = $_REQUEST['orderby'];

        if ( isset( $_REQUEST['order'] ) )
            $args['order'] = $_REQUEST['order'];
            
        // Query the user IDs for this page
        $wp_user_search = new WP_User_Query( $args );
        $wp_user_search->query_from .= " LEFT OUTER JOIN " . $wpdb->prefix . "users_changes uc ON uc.user_id = ID";
        $wp_user_search->query_where .= ' AND (wp_users.user_status = 3 OR uc.email_changed != \'\')';
        $wp_user_search->query_fields .= ', IFNULL(uc.updated_date, wp_users.user_registered) as created_date';
        if ($args['orderby'] == 'created_date') {
            $wp_user_search->query_orderby = 'ORDER BY ' . $args['orderby'] . ' ' . $args['order'];
        }
        
        $wp_user_search->query();
        
        $this->items = $wp_user_search->get_results();
        
        $this->set_pagination_args( array(
            'total_items' => $wp_user_search->get_total(),
            'per_page' => $users_per_page,
        ) );
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($args['orderby']);          
        $this->_column_headers = array($columns, $hidden, $sortable);
    }

    function no_items() {
        _e( 'No matching users were found.' );
    }

    function get_bulk_actions() {
        $actions = array();

        if ( !is_multisite() ) {
            if ( current_user_can( 'delete_users' ) ) {
                $actions['verify'] = __( 'Verified' );
                $actions['cancelled'] = __( 'Cancelled' );
            }
        }

        return $actions;
    }

    function extra_tablenav( $which ) {
        
    }

    function current_action() {
        if ( isset($_REQUEST['changeit']) && !empty($_REQUEST['new_role']) )
            return 'promote';

        return parent::current_action();
    }

    function get_columns() {
        $c = array(
            'cb'       => '<input type="checkbox" />',
            'username' => __( 'Username' ),
            'name'     => __( 'Name' ),
            'email'    => __( 'E-mail' ),
            'email_new'    => __( 'New E-mail' ),
            'created_date'    => __( 'Created Date' ),
            'role'     => __( 'Role' )
        );

        return $c;
    }

    function get_sortable_columns($orderby) {
        $c = array(
            "username" => array("login", $orderby == 'login'),
            "name" => array("name", $orderby == 'name'),
            "email" => array("email", $orderby == 'email'),
            "created_date" => array("created_date", $orderby == 'created_date')
        );

        return $c;
    }

    function display_rows() {
        // Query the post counts for this page
        $post_counts = count_many_users_posts( array_keys( $this->items ) );

        $editable_roles = array_keys( get_editable_roles() );
        
        $style = '';
        foreach ( $this->items as $userid => $user_object ) {
            if ( count( $user_object->roles ) <= 1 ) {
                $role = reset( $user_object->roles );
            } elseif ( $roles = array_intersect( array_values( $user_object->roles ), $editable_roles ) ) {
                $role = reset( $roles );
            } else {
                $role = reset( $user_object->roles );
            }

            if ( is_multisite() && empty( $user_object->allcaps ) )
                continue;

            $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
            echo "\n\t" . $this->single_row( $user_object, $style, $role, isset( $post_counts ) ? $post_counts[ $userid ] : 0 );
        }
    }

    
    function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
        global $wpdb, $wp_roles;

        if ( !( is_object( $user_object ) && is_a( $user_object, 'WP_User' ) ) )
            $user_object = get_userdata( (int) $user_object );
        $user_object->filter = 'display';
        $email = $user_object->user_email;
        $created_date = $user_object->user_registered;
        
        $row = $wpdb->get_row("SELECT email_changed, updated_date FROM $wpdb->prefix" . "users_changes WHERE user_id=" . $user_object->ID . " LIMIT 1");
        if (!empty($row)) {
            $email_new = $row['email_changed'];
            $created_date = $row['updated_date'];
        } else {
            $email_new = '';
        }

        $url = 'users.php?';

        $checkbox = '';
        // Check if the user for this row is editable
        if ( current_user_can( 'list_users' ) ) {
            // Set up the user editing link
            $edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_object->ID ) ) );

            // Set up the hover actions for this user
            $actions = array();

            $edit = "<strong>$user_object->user_login</strong><br />";

            if ( !is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) ) {
                $actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?page=user_email_verifications&action=verify&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Verify' ) . "</a>";
                $actions['cancel'] = "<a class='submitcancel' href='" . wp_nonce_url( "users.php?page=user_email_verifications&action=cancelled&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Cancel' ) . "</a>";
            }
            $actions = apply_filters( 'user_row_actions', $actions, $user_object );
            $edit .= $this->row_actions( $actions );

            // Set up the checkbox ( because the user is editable, otherwise it's empty )
            $checkbox = '<label class="screen-reader-text" for="cb-select-' . $user_object->ID . '">' . sprintf( __( 'Select %s' ), $user_object->user_login ) . '</label>'
                        . "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' />";

        } else {
            $edit = '<strong>' . $user_object->user_login . '</strong>';
        }
        $role_name = isset( $wp_roles->role_names[$role] ) ? translate_user_role( $wp_roles->role_names[$role] ) : __( 'None' );
        $avatar = get_avatar( $user_object->ID, 32 );

        $r = "<tr id='user-$user_object->ID'$style>";

        list( $columns, $hidden ) = $this->get_column_info();

        foreach ( $columns as $column_name => $column_display_name ) {
            $class = "class=\"$column_name column-$column_name\"";

            $style = '';
            if ( in_array( $column_name, $hidden ) )
                $style = ' style="display:none;"';

            $attributes = "$class$style";

            switch ( $column_name ) {
                case 'cb':
                    $r .= "<th scope='row' class='check-column'>$checkbox</th>";
                    break;
                case 'username':
                    $r .= "<td $attributes>$avatar $edit</td>";
                    break;
                case 'name':
                    $r .= "<td $attributes>$user_object->first_name $user_object->last_name</td>";
                    break;
                case 'email': 
                    $r .= "<td $attributes><a href='mailto:$email' title='" . esc_attr( sprintf( __( 'E-mail: %s' ), $email ) ) . "'>$email</a></td>";
                    break;
                case 'email_new':
                    $r .= "<td $attributes><a href='mailto:$email_new' title='" . esc_attr( sprintf( __( 'New E-mail: %s' ), $email_new ) ) . "'>$email_new</a></td>";
                    break;
                case 'created_date':
                    $r .= "<td $attributes>$created_date</td>";
                    break;
                case 'role':
                    $r .= "<td $attributes>$role_name</td>";
                    break;
                case 'posts':
                    $attributes = 'class="posts column-posts num"' . $style;
                    $r .= "<td $attributes>";
                    if ( $numposts > 0 ) {
                        $r .= "<a href='edit.php?author=$user_object->ID' title='" . esc_attr__( 'View posts by this author' ) . "' class='edit'>";
                        $r .= $numposts;
                        $r .= '</a>';
                    } else {
                        $r .= 0;
                    }
                    $r .= "</td>";
                    break;
                default:
                    $r .= "<td $attributes>";
                    $r .= apply_filters( 'manage_users_custom_column', '', $column_name, $user_object->ID );
                    $r .= "</td>";
            }
        }
        $r .= '</tr>';
        

        return $r;
    }
}
