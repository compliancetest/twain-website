<?php
/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override dotted_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 */
 
function dotted_widgets_init() {
	// Area 0, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'care' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Area 1, located at the top of the content.
	register_sidebar( array(
		'name' => __( 'Top Widget Area', 'care' ),
		'id' => 'top-widget-area',
		'description' => __( 'The top widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<li class="widget-title">',
		'after_title' => '</li>',
	) );
	
	// Area 2, located befores the content. Empty by default.
	register_sidebar( array(
		'name' => __( 'Before Content Widget Area', 'care' ),
		'id' => 'content-widget-area',
		'description' => __( 'Before content widget area', 'care' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

		
	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget Area 1', 'care' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget Area 2', 'care' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget Area 3', 'care' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget Area 4', 'care' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	
	// Area 7, located in the sliding-footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Sliding Sidebar Widget Area 1', 'care' ),
		'id' => 'first-sliding-widget-area',
		'description' => __( 'The first sliding widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	// Area 8, located in the sliding-sliding. Empty by default.
	register_sidebar( array(
		'name' => __( 'Sliding Sidebar Widget Area 2', 'care' ),
		'id' => 'second-sliding-widget-area',
		'description' => __( 'The second sliding widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	// Area 9, located in the sliding-sliding. Empty by default.
	register_sidebar( array(
		'name' => __( 'Sliding Sidebar Widget Area 3', 'care' ),
		'id' => 'third-sliding-widget-area',
		'description' => __( 'The third sliding widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
	
	// Area 10, located in the sliding-sliding. Empty by default.
	register_sidebar( array(
		'name' => __( 'Sliding Sidebar Widget Area 4', 'care' ),
		'id' => 'fourth-sliding-widget-area',
		'description' => __( 'The fourth sliding widget area', 'care' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
}

/** Register sidebars by running dotted_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'dotted_widgets_init' );
?>