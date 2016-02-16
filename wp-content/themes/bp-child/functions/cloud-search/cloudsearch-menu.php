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

        <h2>Create Registry Domain(note it takes up to 20 minutes to do this)</h2>

        <div>
            <form action="" method="post">
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('registry_domain_create') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Create"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'registry_domain_create')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    _trace(CloudSearch::createDomain());
                                    ?>
                                </i>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>

        <h2>Populate index fields</h2>

        <div>
            <form action="" method="post">
                <input type="hidden" name="action"
                       value="<?php echo wp_create_nonce('registry_domain_populate_indexes') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Create"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'registry_domain_populate_indexes')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    _trace(CloudSearch::configureFields());
                                    ?>
                                </i>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>

        <h2>Upload Registry Data</h2>

        <div>
            <form action="" method="post">
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('registry_upload') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Upload"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'registry_upload')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    $cloudSearch = new CloudSearch();
                                    _trace($cloudSearch->_initial_upload());
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
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('registry_delete') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Delete"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'registry_delete')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    $cloudSearch = new CloudSearch();
                                    _trace($cloudSearch->_delete_all_items());
                                    ?>
                                </i>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>

        <h2>Create FullText Domain(note it takes up to 20 minutes to do this)</h2>

        <div>
            <form action="" method="post">
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('fulltext_domain_create') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Create"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'fulltext_domain_create')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    _trace(FulltextSearch::createDomain());
                                    ?>
                                </i>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </form>
        </div>

        <h2>Populate Fulltext search domain index fields</h2>

        <div>
            <form action="" method="post">
                <input type="hidden" name="action"
                       value="<?php echo wp_create_nonce('fulltext_domain_populate_indexes') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Create"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'fulltext_domain_populate_indexes')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    _trace(FulltextSearch::configureFields());
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
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('static_upload') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Upload"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'static_upload')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    $cloudSearch = new FulltextSearch();
                                    _trace($cloudSearch->fullUpload());
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
                <input type="hidden" name="action" value="<?php echo wp_create_nonce('static_delete') ?>"/>
                <table>
                    <tr>
                        <td>
                            <input type="submit" class="button button-primary" value="Delete"/>
                        </td>
                    </tr>
                    <?php if (wp_verify_nonce($action, 'static_delete')): ?>
                        <tr>
                            <td>
                                <i>
                                    <?php
                                    $cloudSearch = new FulltextSearch();
                                    _trace($cloudSearch->fullDelete());
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

add_action('save_post', 'fulltext_search_save_post', 10, 3);
function fulltext_search_save_post($post_id, $post = false, $update = false)
{
    $cloud_search = new FulltextSearch();
    if (isset($post->post_status) && $post->post_status != 'publish') {
        $cloud_search->fullDelete($post_id);
    }
    $cloud_search->fullUpload($post_id);
}

add_action('delete_post', 'fulltext_search_delete_post', 10, 3);
function fulltext_search_delete_post($post_id, $post = false, $update = false)
{
    $cloud_search = new FulltextSearch();
    $cloud_search->fullDelete($post_id);
}

