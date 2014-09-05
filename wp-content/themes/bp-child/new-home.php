<?php
/*
 * Template Name: New Homepage Design
 */
    wp_enqueue_style( 'home', get_stylesheet_directory_uri() . '/css/home.css', '', '0.1');

    get_header('home');
?>
		<div class="homepage">
            <div class="home-collage">
                <div class="wrapper">
                    <div class="home-collage-content">
                        <h2 class="home-collage-title">B2B Interoperability</h2>
                        <h3 class="home-collage-subtitle">Made Easy</h3>
                        <p class="home-collage-text">ComplianceTest supports end-to-end automation of B2B processes by providing:</p>
                        <ul class="home-collage-fetures">
                            <li>A community-centric approach to standards compliance</li>
                            <li>An automated self-service test harness</li>
                            <li>A register of certified products and service end-points</li>
                        </ul>
                    </div>
                    <div class="home-collage-video">
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/images/temp/video.jpg" alt=""/>
                    </div>
                </div>
            </div>
            <div class="search-box">
                <div id="seasrch_bar">
                    <form role="search" method="get" id="searchform" action="<?php get_bloginfo('url'); ?>/search">
                        <input id="q" name="q" value="" type="text" placeholder="Search the Registy" autocomplete="off" class="inactive_s">
                        <div class="search_select_div">
                            <div class="search_select">
                                <ul>
                                    <li>
                                        <a id="choose_one" class="current_chosen">Test Suites</a>
                                        <ul>
                                            <li><a id="communities">Communities</a></li>
                                            <li><a id="test-suite">Test Suites</a></li>
                                            <li><a id="product-service">Products</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <input type="hidden" value="test-suite" id="hidden_value"/>
                        </div>
                        <input type="submit" id="searchsubmit" value="SEARCH">
                        <div class="clear"></div>
                    </form>
                </div>
            </div>


            <div class="customer-types-section">
                <ul class="tabs-header">
                    <li><a href="#software-vendors-panel"><span class="icon"></span>Software Vendors: <em>Certify your Products</em></a></li>
                    <li><a href="#organisations-panel"><span class="icon"></span>Organisations: <em>Test Your Implementation</em></a></li>
                    <li><a href="#standart-issuers-panel"><span class="icon"></span>Standards Issuers: <em>Create Test Suites</em></a></li>
                </ul>
                <div class="tabs-content">
                    <div id="software-vendors-panel">Content 1</div>
                    <div id="organisations-panel">Content 2</div>
                    <div id="standart-issuers-panel">Content 3</div>
                </div>
            </div>


            <div class="wrapper">
                <?php $groups_count = groups_get_total_group_count(); ?>
                <?php if ( bp_has_groups() ) : ?>
                    <ul id="groups-list" class="item-list">
                        <?php while ( bp_groups() ) : bp_the_group(); ?>
                            <li>
                                <div class="item-avatar">
                                    <?php
                                        $args   = array(
                                            'width'  => 150,
                                            'height' => 150
                                        );
                                    ?>
                                    <a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar($args); ?></a>
                                </div>
                                <div class="item">
                                    <div class="item-title"><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a></div>

                                    <div class="item-desc"><?php bp_group_description_excerpt() ?></div>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php while ($groups_count > 0): ?>
                        <div class="<?php echo $groups_count; ?>"></div>
                        <?php $groups_count--; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
    </div>
<?php get_footer('home'); ?>