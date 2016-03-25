<?php
/***
 * Template Name: Site Search Results
 */


global $post;

$baseURL = get_permalink();

$cloud_search = new FulltextSearch();
$params = array();
$params_without_sorting = array();
$is_download = false;
if (isset($_GET['download'])) {
    $is_download = true;
    unset($_GET['download']);
}
if ($_GET) {
    foreach ($_GET AS $k => $v) {
        $params[] = urlencode($k) . '=' . urlencode($v);
        if ($k != 'orderby' && $k != 'order') {
            $params_without_sorting[] = urlencode($k) . '=' . urlencode($v);
        }
    }
}

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$page = get_query_var('paged') ? get_query_var('paged') : 1;
$_GET['page'] = $page;
$results = $cloud_search->search($_GET);

if ($is_download) {
    $results = $cloud_search->search($_GET, true);
    generate_and_download_site($results);
}

get_header();

?>
<div class="content container" id="search">
    <div class="column search-results">
        <form name="form_filter" id="form_filter" action="/search-results/" method="get">
            <div class="search-result-form">
                <div class="searchform">
                    <input type="text" name="q" id="q" class="keyword"
                           value="<?php echo isset($_GET['q']) ? htmlspecialchars(trim($_GET['q'])) : '' ?>"
                           placeholder="Search" autocomplete="off"/>
                    <input type="submit" id="search_test_suite_submit" class="search-button" value=""/>
                </div>
            </div>
            <div class="search-results-filter">
                <h4 class="filter-head">Filter By:</h4>

                <div class="search-filters-box">
                    <ul class="search-filters-list clearfix">
                        <li class="first">
                            <label for="content-type-filter">Type</label>
                            <select name="post_type" id="content-type-filter" class="select">
                                <option>All</option>
                                <?php if (is_array($results['facets']['post_type']['buckets'])): ?>
                                    <?php sort($results['facets']['post_type']['buckets']); ?>
                                    <?php foreach ($results['facets']['post_type']['buckets'] AS $v): ?>
                                        <option
                                            value="<?php echo $v['value']; ?>" <?php if (isset($_GET['post_type']) && $_GET['post_type'] == $v['value']): ?> selected="selected" <?php endif; ?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </li>
                        <li>
                            <label for="community-filter">Community</label>
                            <select name="community" id="community-filter" class="select">
                                <option>All</option>
                                <?php if (is_array($results['facets']['community']['buckets'])): ?>
                                    <?php foreach ($results['facets']['community']['buckets'] AS $v): ?>
                                        <?php if ($v['value'] == 'All') continue; ?>
                                        <option
                                            value="<?php echo $v['value']; ?>" <?php if (isset($_GET['community']) && $_GET['community'] == $v['value']): ?> selected="selected" <?php endif; ?>><?php echo $v['value']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </li>
                        <li>
                            <label>Last Update</label>

                            <div class="date-filter filter-from">
                                <input type="text" class="input datepicker" placeholder="From" readonly="true"
                                       name="date_from" <?php if (isset($_GET['date_from'])): ?> value="<?php echo htmlspecialchars($_GET['date_from']); ?>" <?php endif; ?>/>
                            </div>
                            <div class="date-filter filter-to">
                                <input type="text" class="input datepicker" placeholder="To" readonly="true"
                                       name="date_to" <?php if (isset($_GET['date_to'])): ?> value="<?php echo htmlspecialchars($_GET['date_to']); ?>" <?php endif; ?>/>
                            </div>
                        </li>
                    </ul>
                    <div class="search-filter-actions">
                        <a class="action-btn process-btn submit-btn" href="#"><span class="p"></span><span class="t">Confirm</span></a>
                        <a href="<?php echo get_permalink(); ?>" class="action-btn clear-btn"
                           id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear</span></a>
                    </div>
                </div>
            </div>
            <input type="hidden" name="page"/>
        </form>
        <?php if ($results['hits']['found'] > 0): ?>
            <div class="search-results-count clearfix">
                <p class="search-results-count-label">
                    Showing <?php echo $results['hits']['start'] + 1; ?>
                    - <?php echo $results['hits']['start'] + count($results['hits']['hit']) ?> of
                    <b><?php echo $results['hits']['found'] ?></b> Results
                    <?php if (isset($_GET['q']) && !empty($_GET['q'])) { ?> for "
                        <b><?php echo $_GET['q'] ?></b>" <?php } ?>
                </p>
                <?php if ($results['hits']['found'] > 0): ?>
                    <a href="<?php echo add_query_arg('download', '1'); ?>"
                       class="action-btn download-btn query_download">
                        <span class="p"></span>
                        <span class="t">Download Results</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-data">No result found!</p>
        <?php endif; ?>

        <?php if ($results['hits']['found'] > 0): ?>
            <div class="search-result-list-wrapper">
                <table class="search-result-list site-search-result-list">
                    <thead>
                    <tr>
                        <th class="first row-title">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=post_title&order=<?php echo $orderby == 'post_title' && $order == 'asc' ? 'desc' : 'asc' ?>">Title</a>

                            <div class="sorting-box">
                                <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=post_title&order=asc"
                                   class="sorting-link asc<?php if ($orderby == 'post_title' && $order == 'asc') { ?> current<?php } ?>"><span
                                        class="sort"></span></a>
                                <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=post_title&order=desc"
                                   class="sorting-link desc<?php if ($orderby == 'post_title' && $order == 'desc') { ?> current<?php } ?>"><span
                                        class="sort"></span></a>
                            </div>
                        </th>
                        <th class="row-description">Description</th>
                        <th>
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=post_type&order=<?php echo $orderby == 'post_type' && $order == 'asc' ? 'desc' : 'asc' ?>">Type</a>

                            <div class="sorting-box">
                                <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=post_type&order=asc"
                                   class="sorting-link asc<?php if ($orderby == 'post_type' && $order == 'asc') { ?> current<?php } ?>"><span
                                        class="sort"></span></a>
                                <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=post_type&order=desc"
                                   class="sorting-link desc<?php if ($orderby == 'post_type' && $order == 'desc') { ?> current<?php } ?>"><span
                                        class="sort"></span></a>
                            </div>
                        <th>Community</th>
                        <th class="last row-date">
                            <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=last_updated_date&order=<?php echo $orderby == 'last_updated_date' && $order == 'asc' ? 'desc' : 'asc' ?>">Last
                                Update</a>

                            <div class="sorting-box">
                                <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=last_updated_date&order=asc"
                                   class="sorting-link asc<?php if ($orderby == 'last_updated_date' && $order == 'asc') { ?> current<?php } ?>"><span
                                        class="sort"></span></a>
                                <a href="<?php echo get_permalink() ?>?<?php echo implode("&", $params_without_sorting) ?>&orderby=last_updated_date&order=desc"
                                   class="sorting-link desc<?php if ($orderby == 'last_updated_date' && $order == 'desc') { ?> current<?php } ?>"><span
                                        class="sort"></span></a>
                            </div>
                        </th>
                        <?php if (is_super_admin()): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($results['hits']['found'] > 0): ?>
                        <?php foreach ($results['hits']['hit'] AS $row): ?>
                            <?php
                            $id = $row['id'];
                            $row = $row['fields'];
                            ?>
                            <tr>
                                <td class="first">
                                    <?php if ($row['post_type'][0] == 'Test Scenario'): ?>
                                        <?php
                                        $post_meta = get_post_meta($row['post_id'][0]);
                                        $post_name = $post_meta['ts_name'][0] . ' v' . $post_meta['ts_version_major'][0] . '.' . $post_meta['ts_version_minor'][0];
                                        if ($post_meta['ts_version_patch'][0] != '0') {
                                            $post_name .= $post_meta['ts_version_patch'][0];
                                        }
                                        ?>
                                        <a href="<?php echo $row['link'][0]; ?>"><?php echo $row['post_title'][0] . '<br>( ' . $post_name . ' )'; ?></a>
                                    <?php elseif ($row['post_type'][0] == 'Test Case'): ?>
                                        <?php
                                        $post_meta = get_post_meta($row['post_id'][0]);
                                        $post_name = get_the_title($post_meta['test_suite'][0]);
                                        ?>

                                        <a href="<?php echo $row['link'][0]; ?>"><?php echo $row['post_title'][0];
                                            if (!empty($post_name)) echo '<br>( ' . $post_name . ' )'; ?></a>
                                    <?php else: ?>
                                        <a href="<?php echo $row['link'][0]; ?>"><?php echo $row['post_title'][0]; ?></a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p class="short-description"><?php echo strlen(strip_tags($row['post_content'][0])) > 400 ? substr(strip_tags($row['post_content'][0]), 0, 400) . '...' : strip_tags($row['post_content'][0]); ?></p>
                                </td>
                                <td><?php echo $row['post_type'][0]; ?></td>
                                <td>
                                    <?php if (!empty($row['community']) && is_array($row['community'])): ?>
                                        <?php echo implode('<br>', $row['community']); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="last"><?php echo date('Y-m-d', strtotime($row['last_updated_date'][0])); ?></td>
                                <?php if (is_super_admin()): ?>
                                    <td class="remove_entry"><a
                                            href="/?_psnonce=<?php echo wp_create_nonce('get-delete-search-entry'); ?>&id=<?php echo $id; ?>&t=2"
                                            rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1
                                            class="action-btn delete-btn icon-btn delete_search_entry has-tooltip"><span
                                                class="simple_tooltip radius6"
                                                style="margin-left: -90px; width: 170px;">Delete <?php echo $row['post_title'][0]; ?>
                                                <span></span></span><span class="p"></span></a></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
                <script>
                    jQuery(document).ready(function ($) {
                        var rowHeight = 0,
                            agreementFirstRows = jQuery('.search-result-list .agreement-row');

                        agreementFirstRows.each(function () {
                            if ($(this).height() > rowHeight) {
                                rowHeight = $(this).height();
                            }
                        });
                        agreementFirstRows.height(rowHeight);
                    });
                </script>
                <div class="pagination-wrapper flat-pagination">
                    <div class="pagination">
                        <?php
                        $args = array(
                            'base' => get_permalink() . '%_%?',
                            'format' => 'page/%#%',
                            'total' => ceil($results['hits']['found'] / SEARCH_RESULTS_LIMIT),
                            'current' => $page,
                            'show_all' => False,
                            'end_size' => 5,
                            'mid_size' => 5,
                            'prev_next' => True,
                            'prev_text' => __('Previous'),
                            'next_text' => __('Next'),
                            'type' => 'plain',
                            'add_args' => false,
                            'add_fragment' => (count($params) > 0 ? '&' : '') . implode('&', $params)
                        );

                        echo paginate_links($args);
                        ?>
                    </div>
                </div>
            </div> <!-- end search_result_block -->
        <?php endif; ?>
    </div>
</div>
<?php get_footer() ?>
