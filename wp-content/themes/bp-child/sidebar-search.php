<?php
/***
 * Search Form for Test Suite and Products & Services
 */
global $filterParams;

?>
<form role="search" method="get" id="searchform" action="<?php echo get_permalink() ?>" class="searchform">
    <input type="text" name="q" id="q" class="keyword"
           value="<?php echo htmlspecialchars(trim(isset($_GET['q']) ? $_GET['q'] : '')) ?>" placeholder="Search Term"
           autocomplete="off"/>

    <div class="search_select_div" style="display:none;">
        <div class="search_select">
            <ul>
                <li><a id="test-suite" class="current_chosen">Test Suites</a>
                    <ul class="">
                        <li><a id="test-suite">Test Suites</a></li>
                        <li><a id="product-service">Products</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <?php
    foreach ($filterParams as $k => $v) {
        ?>
        <input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>"/>
        <?php
    }
    ?>
    <input type="submit" id="search_test_suite_submit" class="search-button" value=""/>
</form>
<script>
    // Search autofocus
    jQuery(document).ready(function () {
        var inputSearch = jQuery('#q');
        var searchTerm = inputSearch.val();
        if (searchTerm != '') {
            inputSearch.focus().val('').val(searchTerm);
        } else {
            inputSearch.focus();
        }
    });
</script>