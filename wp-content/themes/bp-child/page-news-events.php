<?php
/**
* Template Name: News&Events
*/
get_header();

$community_id = isset($_GET['community_id']) ? $_GET['community_id'] : null;
$year = isset($_GET['y']) ? $_GET['y'] : null;

if($community_id)
    $community = groups_get_group(array('group_id' => $community_id));

$postType = null;

?>
<div class="content container news-events-wrapper">      
    <div class="content container">
        <?php if (have_posts()) while (have_posts()) : the_post(); ?>
        <div class="page-title-block column">
            <h2 class="left">
                <?php if(isset($community)){ ?>
                <a href="<?php the_permalink()?>">News &amp; Events</a> for <a href="<?php echo bp_get_group_permalink($community)?>"><?php echo bp_get_group_name($community); ?></a>
                <?php }else{ ?>
                <a href="<?php the_permalink()?>">News &amp; Events</a>
                <?php } ?>
            </h2>
            <!--<a href="<?php echo addPrintParams(get_permalink(), 'static')?>" class="action-btn print-btn icon-btn print-page-btn" id="print-page-btn"><span class="p"></span></a>-->
            <div class="clear"></div>
        </div>
        <div class="content_inner">
            <div class="four_fifths right">
                <?php
                    if($year){
                        ?>
                        <h3><?php echo $year?></h3>
                        <?php
                    }
                ?>
                <?php                    
                    foreach($postTypes as $ptype)
                    {                        
                        ct_display_blog_articles($ptype, $community_id, $year, 5, true);
                    }
                ?>
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
        <?php endwhile; ?>
    </div>
</div>
<div class="clear"></div>
<?php get_footer() ?>