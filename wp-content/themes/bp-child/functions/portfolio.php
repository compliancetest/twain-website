<?php
//******************************* CUSTOM POST TYPE - PORTFOLIO ********************************//
add_image_size('portfolio', 418, 230, true);
add_image_size('portfolio-two', 459, 250, true);
add_image_size('portfolio-three', 293, 250, true);
add_image_size('portfolio-four', 210, 210, true);
add_image_size('portfolio-single', 946, 0, false);


 
//******************************* CUSTOM POST TYPE - PORTFOLIO *******************************//

add_action('init', 'create_portfolio');

function create_portfolio() {
    $portfolio_args = array(
        'label' => __('Portfolio', 'theblog'),
        'singular_label' => __('Portfolio', 'theblog'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => true,
		'menu_position' => 20,
		'menu_icon' => get_stylesheet_directory_uri() . '/images/portfolio_pt.png',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments')
		);
    register_post_type('portfolio', $portfolio_args);
	
}

add_action('init', 'services_rendered', 0);

function services_rendered() {
    register_taxonomy(
            'services_rendered',
            'portfolio',
            array(
                'hierarchical' => true,
                'label' => 'Portfolio categories',
                'query_var' => true,
                'rewrite' => true
            )
    );
}

?>