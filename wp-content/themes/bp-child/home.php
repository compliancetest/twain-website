<?php
    wp_enqueue_script( 'home', get_stylesheet_directory_uri() . '/js/home.js', array('jquery'), '0.1');
    wp_enqueue_script( 'lemmon_slider', get_stylesheet_directory_uri() . '/js/lemmon-slider.js', array('jquery'), '0.2');

    get_header('home');
?>
<div class="homepage">
    <div class="home-collage">
        <div class="wrapper">
            <div class="home-collage-content">
                <h2 class="home-collage-title">B2B Interoperability</h2>
                <h3 class="home-collage-subtitle">Made Easy</h3>
                <p class="home-collage-text">ComplianceTest supports end-to-end automation of B2B processes by providing:</p>
                <ul class="home-collage-features">
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
        <div id="search_bar">
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
            <li><a href="#software-vendors-panel"><span class="tab-icon software-vendors-icon"></span>Software Vendors: <em>Certify your Products</em></a></li>
            <li><a href="#organisations-panel"><span class="tab-icon organisations-icon"></span>Organisations: <em>Test Your Implementation</em></a></li>
            <li><a href="#standard-issuers-panel"><span class="tab-icon standard-issuers-icon"></span>Standards Issuers: <em>Create Test Suites</em></a></li>
        </ul>
        <div class="tabs-content">
            <div class="wrapper">
                <div id="software-vendors-panel">
                    <div class="tab-content-text">
                        <h2>Confidence for your Customers</h2>
                        <p>How can you provide confidence to your customers that your product or service is interoperable with others in your industry?  Despite your best efforts to interpret and implement a standard, there are often too many options and questions that make interoperability very uncertain. Test your product against the ComplianceTest reference implementation and:</p>
                        <ul>
                            <li>Increase interoperability confidence because you are testing against the samesystem as others.</li>
                            <li>Save costs because it’s far cheaper to re-use our service than build your ownstubs and test cases</li>
                        </ul>
                        <br>
                        <a href="#" class="view-sample-btn">View Sample</a>
                    </div>
                    <div class="tab-content-image">
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/images/home/diagram.png" alt=""/>
                    </div>
                </div>
                <div id="organisations-panel">
                    <div class="tab-content-text">
                        <h2>Test Your Implementation</h2>
                        <p>How can you provide confidence to your customers that your product or service is interoperable with others in your industry?  Despite your best efforts to interpret and implement a standard, there are often too many options and questions that make interoperability very uncertain. Test your product against the ComplianceTest reference implementation and:</p>
                        <ul>
                            <li>Increase interoperability confidence because you are testing against the samesystem as others.</li>
                            <li>Save costs because it’s far cheaper to re-use our service than build your ownstubs and test cases</li>
                        </ul>
                        <br>
                        <a href="#" class="view-sample-btn">View Sample</a>
                    </div>
                    <div class="tab-content-image">
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/images/home/diagram.png" alt=""/>
                    </div>
                </div>
                <div id="standard-issuers-panel">
                    <div class="tab-content-text">
                        <h2>Create Test Suites</h2>
                        <p>How can you provide confidence to your customers that your product or service is interoperable with others in your industry?  Despite your best efforts to interpret and implement a standard, there are often too many options and questions that make interoperability very uncertain. Test your product against the ComplianceTest reference implementation and:</p>
                        <ul>
                            <li>Increase interoperability confidence because you are testing against the samesystem as others.</li>
                            <li>Save costs because it’s far cheaper to re-use our service than build your ownstubs and test cases</li>
                        </ul>
                        <br>
                        <a href="#" class="view-sample-btn">View Sample</a>
                    </div>
                    <div class="tab-content-image">
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/images/home/diagram.png" alt=""/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">

        <div class="getting-started-box">
            <div class="home-box-title">Getting Started</div>
            <div class="getting-started-description">
                <a href="#" class="action-btn red-btn">View User Guide</a>
                <p><strong>ComplianceTest Provides a fully automated self-service testing framework.</strong><br> Just follow the steps below to get confidence that your product or service will be interoperable with others.</p>
            </div>
            <ul class="getting-started-steps clearfix">
                <li class="first-step">
                    <div class="step-title">Register and Join Community</div>
                    <div class="step-description">
                        <p>It's FREE – get access to the community forum and a wealth of implementation information</p>
                    </div>
                </li>
                <li class="step_2">
                    <div class="step-title">Subscribe and Make Test Plan</div>
                    <div class="step-description">
                        <p>Pick a monthly subscription plan and then we will generate a test plan for you.</p>
                    </div>
                </li>
                <li class="step_3">
                    <div class="step-title">Setup Your Test Data</div>
                    <div class="step-description">
                        <p>Download our test data profiles and configure your system</p>
                    </div>
                </li>
                <li class="step_4">
                    <div class="step-title">Run Tests and Fix Issues</div>
                    <div class="step-description">
                        <p>Run through the test plan. See all your transactions and detailed error messages</p>
                    </div>
                </li>
                <li class="step_5">
                    <div class="step-title">Make a verified Claim</div>
                    <div class="step-description">
                        <p>Once everything works, make a claim.  We’ll generate a certificate with full audit trail.</p>
                    </div>
                </li>
                <li class="last-step">
                    <div class="step-title">Ongoing Testing of New Versions</div>
                    <div class="step-description">
                        <p>Don’t stop there!<br>
                          Keep testing as you release new versions of your product or sevice</p>
                    </div>
                </li>
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
                                <div class="community-description"><?php bp_group_description_excerpt(); ?></div>
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
<?php get_footer('home'); ?>