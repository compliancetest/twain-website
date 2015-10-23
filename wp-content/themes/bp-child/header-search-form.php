<div class="header-search">
    <form action="/search-results/">
        <div class="header-search-box">
            <input type="text" name="q" class="header-search-field" value="<?php echo htmlspecialchars($_GET['q']) ?>" placeholder="Search" />
            <div class="header-search-type">
                <div class="header-search-type-inner">
                    <select class="header-search-type-field">
                        <option value="site" <?php if (is_page_template( 'search-site-content.php' )) { ?>selected="selected" <?php } ?>>Site</option>
                        <option value="registry" <?php if (is_page_template( 'search-productservice.php' )) { ?>selected="selected" <?php } ?>>Registry</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="header-search-submit"><span class="header-search-submit-icon">Search</span></button>
        </div>
    </form>
</div>
<script>
    jQuery(document).ready(function($) {
        $('.header-search-type-field').change(function(){
            var form = $(this).parents('form');
            if($(this).val() == 'site'){
                form.attr('action', '/search-results/')
            } else{
                form.attr('action', '/products-and-services/')
            }
        });
    });
</script>