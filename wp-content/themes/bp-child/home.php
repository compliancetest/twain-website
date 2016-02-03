<?php
    wp_enqueue_script( 'home', get_stylesheet_directory_uri() . '/js/home.js', array('jquery'), '0.1');
    wp_enqueue_script( 'lemmon_slider', get_stylesheet_directory_uri() . '/js/lemmon-slider.js', array('jquery'), '0.2');

    get_header();

    $home_settings = get_option('home-settings');
?>
<div class="homepage">
    <div class="home-collage">
        <div class="wrapper">
            <div class="home-collage-content">
                <h2 class="home-collage-title"><?php echo $home_settings['top_banner_title']; ?></h2>
                <h3 class="home-collage-subtitle"><?php echo $home_settings['top_banner_subtitle']; ?></h3>
                <?php echo $home_settings['top_banner_description']; ?>
            </div>
            <div class="home-collage-video">
                <?php /* if($home_settings['top_banner_video']): ?>
                    <?php echo stripcslashes($home_settings['top_banner_video']); ?>
                <?php else:  ?>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/images/temp/video.jpg" alt=""/>
                <?php endif; */ ?>
            </div>
            <img src="<?php echo get_stylesheet_directory_uri() ?>/images/video-twain.png" alt=""/>
        </div>
    </div>
    <div class="search-box">
        <div id="search_bar">
            <form role="search" method="get" id="searchform" action="<?php get_bloginfo('url'); ?>/search">
                <input id="q" name="q" value="" type="text" placeholder="Search the Registry" autocomplete="off" class="inactive_s">
                <input type="hidden" value="product-service" id="hidden_value"/>
                <input type="submit" id="searchsubmit" value="SEARCH">
                <div class="clear"></div>
            </form>
        </div>
    </div>
    <div class="customer-types-section">
        <ul class="tabs-header">
            <li><a href="#software-vendors-panel"><span class="tab-icon software-vendors-icon"></span><?php echo $home_settings['tab_1_title']; ?> <em><?php echo $home_settings['tab_1_subtitle']; ?></em></a></li>
            <li><a href="#organisations-panel"><span class="tab-icon organisations-icon"></span><?php echo $home_settings['tab_2_title']; ?> <em><?php echo $home_settings['tab_2_subtitle']; ?></em></a></li>
        </ul>
        <div class="tabs-content">
            <div class="wrapper">
                <div id="software-vendors-panel">
                    <div class="tab-content-text">
                        <?php echo $home_settings['tab_1_content']; ?>
                        <br>
                        <a href="<?php echo $home_settings['tab_1_button_link']; ?>" class="view-sample-btn">View Sample</a>
                    </div>
                    <div class="tab-content-image">
                        <img src="<?php echo $home_settings['tab_1_image']; ?>" alt=""/>
                    </div>
                </div>
                <div id="organisations-panel">
                    <div class="tab-content-text">
                        <?php echo $home_settings['tab_2_content']; ?>
                        <br>
                        <a href="<?php echo $home_settings['tab_2_button_link']; ?>" class="view-sample-btn">View Sample</a>
                    </div>
                    <div class="tab-content-image">
                        <img src="<?php echo $home_settings['tab_2_image']; ?>" alt=""/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wrapper">
        <div class="getting-started-box">
            <div class="home-box-title">Getting Started</div>
            <div class="getting-started-description clearfix">
                <a href="<?php echo $home_settings['user_guide_link']; ?>" class="action-btn red-btn">View User Guide</a>
                <?php echo $home_settings['getting_started_steps_description']; ?>
            </div>
            <ul class="getting-started-steps clearfix">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <li class="step_<?php echo $i; ?>">
                        <div class="step-title"><?php echo $home_settings['step_'. $i .'_title']; ?></div>
                        <?php if ($home_settings['step_'. $i .'_hyperlink']): ?>
                            <a href="<?php echo $home_settings['step_'. $i .'_hyperlink']; ?>" class="step-description">
                                <span class="inner-description"><?php echo $home_settings['step_'. $i .'_description']; ?></span>
                            </a>
                        <?php else: ?>
                            <div class="step-description">
                                <p class="inner-description"><?php echo $home_settings['step_'. $i .'_description']; ?></p>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
        <div class="our-communities-box">
            <div class="home-box-title">Our Communities</div>
            <?php $groups_count = groups_get_total_group_count(); ?>
            <?php if ( bp_has_groups() ) : ?>
                <ul class="communities-list clearfix">
                    <?php while ( bp_groups() ) : bp_the_group(); ?>
                        <li class="community-item">
                            <div class="community-avatar">
                                <?php
                                    $args   = array(
                                        'width'  => 150
                                    );
                                ?>
                                <a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar($args); ?></a>
                            </div>
                            <div class="community-content">
                                <div class="community-title"><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a></div>
                                <div class="community-description"><?php bp_group_description(); ?></div>
                                <a href="<?php bp_group_permalink() ?>" class="action-btn red-btn">Community Homepage</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <ul class="community-pager">
                    <?php $pager = 1 ?>
                    <?php while ($pager <= $groups_count): ?>
                        <li data-community-index="<?php echo $pager; ?>"><?php echo $pager; ?></li>
                        <?php $pager++; ?>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="our-customers">
        <div class="wrapper">
            <div class="home-box-title">Our Customers</div>
            <?php
                $args = array( 'post_type' => 'customer', 'posts_per_page' => -1);
                $loop = new WP_Query( $args );
            ?>
            <div class="customers-carousel-container">
                <div class="customers-carousel">
                    <ul>
                        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                            <li class="customer-item">
                                <div class="customer-logo">
                                    <?php the_post_thumbnail(); ?>
                                </div>
                                <div class="customer-name"><?php the_title(); ?></div>
                            </li>
                        <?php endwhile; ?>
                        <?php wp_reset_query(); ?>
                    </ul>
                </div>
                <div class="controls">
                    <a href="#" class="prev-slide">Prev Slide</a>
                    <a href="#" class="next-slide">Next Slide</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>