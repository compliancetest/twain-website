<?php
/**
* Archive Page  
*/
$postType = get_post_type();

$communities = ct_get_blog_communities($postType, $year);
$years = ct_get_blog_years($postType);

$community_id = isset($_GET['community_id']) ? $_GET['community_id'] : null;
$year = isset($_GET['y']) ? $_GET['y'] : null;

if($community_id)
    $community = groups_get_group(array('group_id' => $community_id));

get_header();
?>

    <div class="content container news-events-wrapper"><!-- Start Content Container-->                    
        <div class="page-title-block column">
            <h2 class="nomarginbottom left">
                <a href="<?php echo get_post_type_archive_link($postType); ?>"><?php echo $wp_post_types[$postType]->labels->name?></a>
                <?php if($community_id){ ?>
                for <a href="<?php echo bp_get_group_permalink($community)?>"><?php echo bp_get_group_name($community); ?></a>
                <?php } ?>
            </h2>            
            <div class="clear"></div>
        </div>        
        <div class="content_inner">
            <div class="four_fifths right">
                <div class="column nopaddingtop">
                <?php
                    if($year)                    
                    {
                        ?>
                        <h3><?php echo $year ?></h3>
                        <?php
                    }else{
                        ?>
                        <br />
                        <?php
                    }
                       
                    ct_display_blog_articles($postType, $community_id, $year, -1);
                    
                ?>
                </div>
            </div>
            <div class="fifth left">
                <div class="column">                    
                    <?php
                        get_sidebar('newsevents');
                    ?>                    
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>        
    </div> <!--End content container-->    

<?php
get_footer();
?>

