<div class="header-search">
    <form action="/search-results/">
        <div class="header-search-box">
            <input type="text" name="q" class="header-search-field" value="<?php echo $_GET['q'] ?>" placeholder="Search" />
            <div class="header-search-type">
                <div class="header-search-type-inner">
                    <select class="header-search-type-field">
                        <option value="site">Site</option>
                        <option value="registry">Registry</option>
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