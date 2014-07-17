<?php
/*
 * Template Name: My Organisation
 */

if (!is_organisation_admin()) {
    
    wp_redirect(home_url());
    exit;
}
get_header();
?>

<div class="content" id="organisation-container">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="column">
            <div id="item-nav">
                <ul class="tabs">
                    <li class="active">
                        <a href="#" rel="organisation_profile" class="selected">
                            <span class="left icon" id="icon_admin"></span>
                            <span class="right text">Profile</span>
                            <span class="tabactive"></span>
                            <span class="clear"></span>
                        </a>
                    </li>
                </ul>
                <div class="clear"></div>
            </div>
            <div id="item-body">
                <div id="organisation_profile" class="tab-content white_bcg column">
                    <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
                    <?php 
                        include(dirname(__FILE__) . '/content/profile-organisation-details.php');
                    ?>            
                    <div class="clear"></div>
                </div>
            </div>
            
            <div class="clear"></div>            
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->

<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#cards-list'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
})
</script>
<?php
get_footer();
?>