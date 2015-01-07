<?php
/**
* Site Custom Tools
*/

add_action("admin_menu", "ct_cloudsearch_menu");

function ct_cloudsearch_menu()
{
    add_management_page("CloudSearch", "CloudSearch", "manage_options", "cloud_search", "ct_cloud_search");
}

function ct_cloud_search()
{
    global $wpdb;
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
    ?>
    <div class="wrap">
        <h2>AWS CloudSearch</h2>
            <h2>Upload Registry Data</h2>
            <div>
                <form action="" method="post">
                    <input type="hidden" name="action" value="<?php echo wp_create_nonce('registry_upload')?>" />
                    <table>
                        <tr>
                            <td>
                                <input type="submit" class="button button-primary" value="Upload" />
                            </td>
                        </tr>
                        <?php if (wp_verify_nonce($action, 'registry_upload')): ?>
                            <tr>
                                <td>
                                    <i>
                                        <?php
                                            $cloudSearch = new CloudSearch();
                                            $cloudSearch->_initial_upload();
                                        ?>
                                    </i>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </form>
            </div>

            <h2>Delete Registry Data</h2>
            <div>
                <form action="" method="post">
                    <input type="hidden" name="action" value="<?php echo wp_create_nonce('registry_delete')?>" />
                    <table>
                        <tr>
                            <td>
                                <input type="submit" class="button button-primary" value="Delete" />
                            </td>
                        </tr>
                        <?php if (wp_verify_nonce($action, 'registry_delete')): ?>
                            <tr>
                                <td>
                                    <i>
                                        <?php
                                            $cloudSearch = new CloudSearch();
                                            $cloudSearch->_delete_all_items();
                                        ?>
                                    </i>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </form>
            </div>

            <h2>Upload Static Data</h2>
            <div>
                <form action="" method="post">
                    <input type="hidden" name="action" value="<?php echo wp_create_nonce('static_upload')?>" />
                    <table>
                        <tr>
                            <td>
                                <input type="submit" class="button button-primary" value="Upload" />
                            </td>
                        </tr>
                        <?php if (wp_verify_nonce($action, 'static_upload')): ?>
                            <tr>
                                <td>
                                    <i>
                                        <?php
                                            $cloudSearch = new FulltextSearch();
                                            $cloudSearch->fullUpload();
                                        ?>
                                    </i>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </form>
            </div>

            <h2>Delete Static Data</h2>
            <div>
                <form action="" method="post">
                    <input type="hidden" name="action" value="<?php echo wp_create_nonce('static_delete')?>" />
                    <table>
                        <tr>
                            <td>
                                <input type="submit" class="button button-primary" value="Delete" />
                            </td>
                        </tr>
                        <?php if (wp_verify_nonce($action, 'static_delete')): ?>
                            <tr>
                                <td>
                                    <i>
                                        <?php
                                            $cloudSearch = new FulltextSearch();
                                            $cloudSearch->fullDelete();
                                        ?>
                                    </i>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </form>
            </div>
    </div>
    <?php
}

add_action( 'save_post', 'fulltext_search_save_post', 10, 3 );
function fulltext_search_save_post( $post_id, $post, $update )
{
    $cloud_search = new FulltextSearch();
    $cloud_search->fullUpload( $post_id );
}

add_action( 'delete_post', 'fulltext_search_delete_post', 10, 3 );
function fulltext_search_delete_post( $post_id, $post, $update )
{
    $cloud_search = new FulltextSearch();
    $cloud_search->fullDelete( $post_id );
}

