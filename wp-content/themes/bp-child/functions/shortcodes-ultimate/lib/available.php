<?php

	/**
	 * List of available shortcodes
	 */
	function su_shortcodes( $shortcode = false ) {
		$shortcodes = array(
			# heading
			'heading' => array(
				'name' => 'Heading',
				'type' => 'wrap',
				'atts' => array( ),
				'usage' => '[heading] Content [/heading]',
				'content' => __( 'Heading', 'care' ),
				'desc' => __( 'Styled heading', 'care' )
			),
			# register_form
			'register_form' => array(
				'name' => 'Registration Form',
				'type' => 'wrap',
				'atts' => array( ),
				'usage' => '[register_form] register [/register_form]',
				'content' => __( '', 'care' ),
				'desc' => __( 'Registration Form', 'care' )
			),
			# frame
			'frame' => array(
				'name' => 'Image frame',
				'type' => 'wrap',
				'atts' => array(
					'align' => array(
						'values' => array(
							'left',
							'center',
							'none',
							'right'
						),
						'desc' => __( 'Frame align', 'care' )
					)
				),
				'usage' => '[frame align="center"] <img src="image.jpg" alt="" /> [/frame]',
				'content' => __( 'Image tag', 'care' ),
				'desc' => __( 'Styled image frame', 'care' )
			),
			# tabs
			'tabs' => array(
				'name' => 'Tabs',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Tabs style', 'care' )
					)
				),
				'usage' => '[tabs style="1"] [tab title="Tab name"] Tab content [/tab] [/tabs]',
				'desc' => __( 'Tabs container', 'care' )
			),
			# tab
			'tab' => array(
				'name' => 'Tab',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Title', 'care' ),
						'desc' => __( 'Tab title', 'care' )
					)
				),
				'usage' => '[tabs style="1"] [tab title="Tab name"] Tab content [/tab] [/tabs]',
				'content' => __( 'Tab content', 'care' ),
				'desc' => __( 'Single tab', 'care' )
			),
			
			# content-tab
			'content_tab' => array(
				'name' => 'Content Tab',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Title', 'care' ),
						'desc' => __( 'Tab title', 'care' )
					)
				),
				'usage' => '[content_tab title="Tab name"] Tab content [/content_tab]',
				'content' => __( 'Tab content', 'care' ),
				'desc' => __( 'Content tab', 'care' )
			),
			
					
			# spoiler
			'spoiler' => array(
				'name' => 'Spoiler',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Spoiler title', 'care' ),
						'desc' => __( 'Spoiler title', 'care' )
					)
				),
				'usage' => '[spoiler title="Spoiler title"] Hidden text [/spoiler]',
				'content' => __( 'Hidden content', 'care' ),
				'desc' => __( 'Hidden text', 'care' )
			),
			# divider
			'divider' => array(
				'name' => 'Divider',
				'type' => 'single',
				'atts' => array(
					'top' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Show TOP link', 'care' )
					),
					
					'style' => array(
						'values' => array(
							'shadow',
							'solid'
						),
						'default' => 'solid',
						'desc' => __( 'Divider style', 'care' )
					)
				),
				'usage' => '[divider top="1"]',
				'desc' => __( 'Divider with optional TOP link', 'care' )
			),
			# spacer
			'spacer' => array(
				'name' => 'Spacer',
				'type' => 'single',
				'atts' => array(
					'size' => array(
						'values' => array( ),
						'default' => '20',
						'desc' => __( 'Spacer height in pixels', 'care' )
					)
				),
				'usage' => '[spacer size="20"]',
				'desc' => __( 'Empty space with adjustable height', 'care' )
			),
			# quote
			'quote' => array(
				'name' => 'Quote',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Quote style', 'care' )
					)
				),
				'usage' => '[quote style="1"] Content [/quote]',
				'content' => __( 'Quote', 'care' ),
				'desc' => __( 'Blockquote alternative', 'care' )
			),
			# pullquote
			'pullquote' => array(
				'name' => 'Pullquote',
				'type' => 'wrap',
				'atts' => array(
					'align' => array(
						'values' => array(
							'left',
							'right'
						),
						'default' => 'left',
						'desc' => __( 'Pullquote alignment', 'care' )
					)
				),
				'usage' => '[pullquote align="left"] Content [/pullquote]',
				'content' => __( 'Pullquote', 'care' ),
				'desc' => __( 'Pullquote', 'care' )
			),
			
			# blog posts
			'blog' => array(
				'name' => 'Blog posts',
				'type' => 'single',
				'atts' => array(
					'titlesize' => array(
						'values' => array( ),
						'default' => '16',
						'desc' => __( 'Title font size in px', 'care' )
					),
					'cat' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Category IDs to show<br/>leave blank to show all', 'care' )
					),
					'num' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Post count', 'care' )
					),
					'type' => array(
						'values' => array(
							'full',
							'excerpt'
						),
						'default' => 'excerpt',
						'desc' => __( 'Post style', 'care' )
					),
					'meta' => array(
						'values' => array(
							'yes',
							'no'
						),
						'default' => 'yes',
						'desc' => __( 'Post meta', 'care' )
					),
					'orderby' => array(
						'values' => array(
							'post_date',
							'comment_count',
							'post_author',
							'post_title',
							'post_category'
							
						),
						'default' => 'post_date',
						'desc' => __( 'Order by', 'care' )
					),
					'order' => array(
						'values' => array(
							'DESC',
							'ASC'
						),
						'default' => 'DESC',
						'desc' => __( 'Order', 'care' )
					),
					
					'thumbnail' => array(
						'values' => array(
							'yes',
							'no'
						),
						'default' => 'yes',
						'desc' => __( 'Show thumbnails?', 'care' )
					),
				
					'background' => array(
						'values' => array(
							'no',
							'yes'
						),
						'default' => 'no',
						'desc' => __( 'Background', 'care' )
					),
					'bg_color' => array(
						'values' => array( ),
						'default' => '#e4e8ea',
						'desc' => __( 'Background color', 'care' ),
						'type' => 'color'
					)
				),
				'usage' => '[blog]',
				'desc' => __( 'Blog posts', 'care' )
			),
			
			# staff
			'staff' => array(
				'name' => 'Staff',
				'type' => 'wrap',
				'atts' => array(
					'name' => array(
						'values' => array( ),
						'default' => 'Full Name',
						'desc' => __( 'Full Name', 'care' )
					),				
					'position' => array(
						'values' => array( ),
						'default' => 'Staff',
						'desc' => __( 'Job position', 'care' )
					),
					'img' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Image url', 'care' )
					)
				),
				
				'usage' => '[staff name="Full Name" position="job position"] Content [/staff]',
				'content' => __( 'Description abaut staff member', 'care' ),
				'desc' => __( 'Staff', 'care' )
			),
			
			# highlight
			'highlight' => array(
				'name' => 'Highlight',
				'type' => 'wrap',
				'atts' => array(
					'bg' => array(
						'values' => array( ),
						'default' => '#DDFF99',
						'desc' => __( 'Background color', 'care' ),
						'type' => 'color'
					),
					'color' => array(
						'values' => array( ),
						'default' => '#000000',
						'desc' => __( 'Text color', 'care' ),
						'type' => 'color'
					)
				),
				'usage' => '[highlight bg="#fc0" color="#000"] Content [/highlight]',
				'content' => __( 'Highlighted text', 'care' ),
				'desc' => __( 'Highlighted text', 'care' )
			),
			# bloginfo
			'bloginfo' => array(
				'name' => 'Bloginfo',
				'type' => 'single',
				'atts' => array(
					'option' => array(
						'values' => array(
							'name',
							'description',
							'url',
							'admin_email',
							'charset',
							'version',
							'html_type',
							'text_direction',
							'language',
							'template_url',
							'pingback_url',
							'rss2_url'
						),
						'default' => 'left',
						'desc' => __( 'Option name', 'care' )
					)
				),
				'usage' => '[bloginfo option="name"]',
				'desc' => __( 'Blog info', 'care' )
			),
			# permalink
			'permalink' => array(
				'name' => 'Permalink',
				'type' => 'mixed',
				'atts' => array(
					'p' => array(
						'values' => array( ),
						'default' => '1',
						'desc' => __( 'Post/page ID', 'care' )
					),
					'target' => array(
						'values' => array(
							'self',
							'blank'
						),
						'default' => 'self',
						'desc' => __( 'Link target', 'care' )
					),
				),
				'usage' => '[permalink p=52]<br/>[permalink p="52" target="blank"] Content [/permalink]',
				'content' => __( 'Permalink text', 'care' ),
				'desc' => __( 'Permalink to specified post/page', 'care' )
			),
			# button
			'button' => array(
				'name' => 'Button',
				'type' => 'wrap',
				'atts' => array(
					'link' => array(
						'values' => array( ),
						'default' => '#',
						'desc' => __( 'Button link', 'care' )
					),
					'color' => array(
						'values' => array( ),
						'default' => '#AAAAAA',
						'desc' => __( 'Button background color', 'care' ),
						'type' => 'color'
					),
					'size' => array(
						'values' => array(
							'1',
							'2',
							'3',
							'4',
							'5',
							'6',
							'7',
							'8',
							'9',
							'10',
							'11',
							'12'
						),
						'default' => '3',
						'desc' => __( 'Button size', 'care' )
					),
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3',
							'4'
						),
						'default' => '1',
						'desc' => __( 'Button background style', 'care' )
					),
					'dark' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Dark text color', 'care' )
					),
					'square' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Disable rounded corners', 'care' )
					),
					'icon' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Button icon', 'care' )
					),
					'class' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Button class', 'care' )
					),
					'target' => array(
						'values' => array(
							'self',
							'blank'
						),
						'default' => 'self',
						'desc' => __( 'Button link target', 'care' )
					)
				),
				'usage' => '[button link="#" color="#b00" size="3" style="3" dark="1" square="1" icon="image.png"] Button text [/button]',
				'content' => __( 'Button text', 'care' ),
				'desc' => __( 'Styled button', 'care' )
			),
			# fancy_link
			'fancy_link' => array(
				'name' => 'Fancy link',
				'type' => 'wrap',
				'atts' => array(
						'color' => array(
						'values' => array( ),
						'default' => '#AAAAAA',
						'desc' => __( 'Button background color', 'care' ),
						'type' => 'color'
					),
				
					'link' => array(
						'values' => array( ),
						'default' => '#',
						'desc' => __( 'URL', 'care' )
					)
					
				),
				'usage' => '[fancy_link color="black" link="http://example.com/"] Read more [/fancy_link]',
				'content' => __( 'Link text', 'care' ),
				'desc' => __( 'Fancy link', 'care' )
			),
			# service
			'service' => array(
				'name' => 'Service',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Service title', 'care' ),
						'desc' => __( 'Service title', 'care' )
					),
					'icon' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Service icon', 'care' )
					),
					'size' => array(
						'values' => array(
							'24',
							'32',
							'48',
							'66'
						),
						'default' => '32',
						'desc' => __( 'Icon size', 'care' )
					)
				),
				'usage' => '[service title="Service title" icon="service.png" size="32"] Service description [/service]',
				'content' => __( 'Service description', 'care' ),
				'desc' => __( 'Service box with title', 'care' )
			),
			# members
			'members' => array(
				'name' => 'Members',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'0',
							'1',
							'2'
						),
						'default' => '1',
						'desc' => __( 'Box style', 'care' )
					)
				),
				'usage' => '[members style="2"] Content for logged users [/members]',
				'content' => __( 'Contnt for logged members', 'care' ),
				'desc' => __( 'Content for logged in members only', 'care' )
			),
			# box
			'box' => array(
				'name' => 'Box',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Box title', 'care' ),
						'desc' => __( 'Box title', 'care' )
					),
					'color' => array(
						'values' => array( ),
						'default' => '#333333',
						'desc' => __( 'Box color', 'care' ),
						'type' => 'color'
					)
				),
				'usage' => '[box title="Box title" color="#f00"] Content [/box]',
				'content' => __( 'Box content', 'care' ),
				'desc' => __( 'Colored box with caption', 'care' )
			),
			# note
			'note' => array(
				'name' => 'Note',
				'type' => 'wrap',
				'atts' => array(
					'color' => array(
						'values' => array( ),
						'default' => '#FFCC00',
						'desc' => __( 'Note color', 'care' ),
						'type' => 'color'
					)
				),
				'usage' => '[note color="#FFCC00"] Content [/note]',
				'content' => __( 'Note text', 'care' ),
				'desc' => __( 'Colored note box', 'care' )
			),
			# callout
			'callout' => array(
				'name' => 'Call-out',
				'type' => 'wrap',
				'atts' => array(
					'bg' => array(
						'values' => array( ),
						'default' => '#00c5dc',
						'desc' => __( 'Background color', 'care' ),
						'type' => 'color'
					),
					'color' => array(
						'values' => array( ),
						'default' => '#FFFFFF',
						'desc' => __( 'Text color', 'care' ),
						'type' => 'color'
					),
					'padding' => array(
						'values' => array( ),
						'default' => __( '25', 'care' ),
						'desc' => __( 'Padding in px', 'care' )
					)
				),
				'usage' => '[callout color="#FFCC00"] Content [/callout]',
				'content' => __( 'Call out text', 'care' ),
				'desc' => __( 'Call out box', 'care' )
			),
			# pricing box
			'pricing' => array(
				'name' => 'Pricing',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'This is title', 'care' ),
						'desc' => __( 'Box title', 'care' )
					),
					'currency' => array(
						'values' => array( ),
						'default' => __( '$', 'care' ),
						'desc' => __( 'Offer currency', 'care' )
					),
					'price' => array(
						'values' => array( ),
						'default' => __( '10', 'care' ),
						'desc' => __( 'Offer price', 'care' )
					),
					'period' => array(
						'values' => array( ),
						'default' => __( '/mo', 'care' ),
						'desc' => __( 'Offer period', 'care' )
					),
					'slug' => array(
						'values' => array( ),
						'default' => __( '', 'care' ),
						'desc' => __( 'Slug under price', 'care' )
					),
					'link' => array(
						'values' => array( ),
						'default' => __( '', 'care' ),
						'desc' => __( 'Link to offer page', 'care' )
					),
					'bg' => array(
						'values' => array(
							'yes',
							'no'
						),
						'default' => 'yes',
						'desc' => __( 'Use background', 'care' )
					),
					'color' => array(
						'values' => array( ),
						'default' => '#2b2b2b',
						'desc' => __( 'Background color', 'care' ),
						'type' => 'color'
					),	
					'text' => array(
						'values' => array( ),
						'default' => '#ffffff',
						'desc' => __( 'Text color', 'care' ),
						'type' => 'color'
					)
				),
				'usage' => '[pricing] Content [/pricing]',
				'content' => __( 'Box content', 'care' ),
				'desc' => __( 'Pricing box', 'care' )
			),
			# private
			'private' => array(
				'name' => 'Private',
				'type' => 'wrap',
				'atts' => array( ),
				'usage' => '[private] Private content [/private]',
				'content' => __( 'Private note text', 'care' ),
				'desc' => __( 'Private note for post authors', 'care' )
			),
			# list
			'list' => array(
				'name' => 'List',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'star',
							'arrow',
							'check',
							'cross',
							'thumbs',
							'gear',
							'time',
							'note',
							'plus',
							'guard',
							'event',
							'idea',
							'link',
							'settings',
							'home',
							'phone',
							'email',
							'user',
							'twitter',
							'skype',
							'white-bullet',
							'orange-bullet',
							'black-bullet',
							'red-bullet',
							'yellow-bullet',
							'green-bullet',
							'blue-bullet',
						),
						'default' => 'star',
						'desc' => __( 'List style', 'care' )
					)
				),
				'usage' => '[list style="check"] <ul> <li> List item </li> </ul> [/list]',
				'content' => '<ul><li>' . __( 'List item ', 'care' ) . '</li></ul>',
				'desc' => __( 'Styled unordered list', 'care' )
			),
			# feed
			'feed' => array(
				'name' => 'Feed',
				'type' => 'single',
				'atts' => array(
					'url' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Feed URL', 'care' )
					),
					'limit' => array(
						'values' => array(
							'1',
							'3',
							'5',
							'7',
							'10'
						),
						'default' => '3',
						'desc' => __( 'Number of item to show', 'care' )
					)
				),
				'usage' => '[feed url="http://rss1.smashingmagazine.com/feed/" limit="5"]',
				'desc' => __( 'Feed grabber', 'care' )
			),
			# menu
			'menu' => array(
				'name' => 'Menu',
				'type' => 'single',
				'atts' => array(
					'name' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Custom menu name', 'care' )
					),
					'style' => array(
						'values' => array(
							'1',
							'2'
						),
						'default' => '1',
						'desc' => __( 'Menu style', 'care' )
					)
				),
				'usage' => '[menu name="Main menu"]',
				'desc' => __( 'Custom menu by name', 'care' )
			),
			# subpages
			'subpages' => array(
				'name' => 'Sub pages',
				'type' => 'single',
				'atts' => array(
					'depth' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Depth level', 'care' )
					),
					'p' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Parent page ID', 'care' )
					)
				),
				'usage' => '[subpages]<br/>[subpages depth="2" p="122"]',
				'desc' => __( 'Page childrens', 'care' )
			),
			# siblings
			'siblings' => array(
				'name' => 'Siblings',
				'type' => 'single',
				'atts' => array(
					'depth' => array(
						'values' => array(
							'1',
							'2',
							'3',
							'4'
						),
						'default' => '1',
						'desc' => __( 'Depth level', 'care' )
					)
				),
				'usage' => '[siblings]<br/>[siblings depth="2"]',
				'desc' => __( 'Page siblings', 'care' )
			),
			# column
			'column' => array(
				'name' => 'Column',
				'type' => 'wrap',
				'atts' => array(
					'size' => array(
						'values' => array(
							'1-2',
							'1-3',
							'1-4',
							'1-5',
							'1-6',
							'2-3',
							'2-5',
							'3-4',
							'3-5',
							'4-5',
							'5-6'
						),
						'default' => '1-2',
						'desc' => __( 'Column width', 'care' )
					),
					'last' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Last column', 'care' )
					)
				),
				'usage' => '[column size="1-2"] Content [/column]<br/>[column size="1-2" last="1"] Content [/column]',
				'content' => __( 'Column content', 'care' ),
				'desc' => __( 'Flexible columns', 'care' )
			),
			# dropcap
			'dropcap' => array(
				'name' => 'Dropcap',
				'type' => 'wrap',
				'atts' => array(
					'color' => array(
						'values' => array(
							'black',
							'gray',
							'blue',
							'red',
							'green',
							'yellow',
							'orange',
							'pink-orange',
							'purple'
						),
						'default' => 'black',
						'desc' => __( 'Dropcap color', 'care' )
					)
				),
				'usage' => '[dropcap style=""] [/dropcap]',
				'content' => __( 'Content', 'care' ),
				'desc' => __( 'Dropcaps', 'care' )
			),
			# column_title
			'column_title' => array(
				'name' => 'Column Title',
				'type' => 'wrap',
				'atts' => array(
					'type' => array(
						'values' => array(
							'sliver',
							'gold',
							'platinum',
						),
						'default' => 'silver',
						'desc' => __( 'Column Title', 'care' )
					)
				),
				'usage' => '[column_title style=""] [/column_title]',
				'content' => __( 'Content', 'care' ),
				'desc' => __( 'Column Titles', 'care' )
			),
			# service_price
			'service_price' => array(
				'name' => 'Service Price',
				'type' => 'wrap',
				'atts' => array(
					'type' => array(
						'values' => array(
							'simple',
							'best-value',
							'most-popular',
						),
						'default' => 'silver',
						'desc' => __( 'Star Type', 'care' )
					)
				),
				'usage' => '[service_price style=""] [/service_price]',
				'content' => __( 'Content', 'care' ),
				'desc' => __( 'Service Price', 'care' )
			),
			# table
			'table' => array(
				'name' => 'Table',
				'type' => 'mixed',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Table style', 'care' )
					),
					'file' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Create table from CSV', 'care' )
					)
				),
				'usage' => '[table style="1"] <table> … <table> [/table]<br/>[table style="1" file="http://example.com/file.csv"] [/table]',
				'content' => '<table><tr><td></td></tr></table>',
				'desc' => __( 'Styled table from HTML or CSV file', 'care' )
			),
			# media
			'media' => array(
				'name' => 'Media',
				'type' => 'single',
				'atts' => array(
					'url' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Media URL', 'care' )
					),
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Width', 'care' )
					),
					'height' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Height', 'care' )
					)
				),
				'usage' => '[media url="http://www.youtube.com/watch?v=2c2EEacfC1M"]<br/>[media url="http://vimeo.com/15069551"]<br/>[media url="video.mp4"]<br/>[media url="video.flv"]<br/>[media url="audio.mp3"]<br/>[media url="image.jpg"]',
				'desc' => __( 'YouTube video, Vimeo video, .mp4/.flv video, .mp3 file or images', 'care' )
			),
			# document
			'document' => array(
				'name' => 'Document',
				'type' => 'single',
				'atts' => array(
					'file' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Document URL', 'care' )
					),
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Width', 'care' )
					),
					'height' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Height', 'care' )
					)
				),
				'usage' => '[document file="file.doc" width="600" height="400"]',
				'desc' => __( '.doc, .xls, .pdf viewer by Google', 'care' )
			),
			# gmap
			'gmap' => array(
				'name' => 'Gmap',
				'type' => 'single',
				'atts' => array(
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Width', 'care' )
					),
					'height' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Height', 'care' )
					),
					'address' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Marker address', 'care' )
					),
				),
				'usage' => '[gmap width="600" height="400" address="Russia, Moscow"]',
				'desc' => __( 'Maps by Google', 'care' )
			),
			# nivo_slider
			'nivo_slider' => array(
				'name' => 'Nivo slider',
				'type' => 'single',
				'atts' => array(
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Slider width', 'care' )
					),
					'height' => array(
						'values' => false,
						'default' => '300',
						'desc' => __( 'Slider height', 'care' )
					),
					'link' => array(
						'values' => array(
							'none',
							'file',
							'attachment',
							'caption'
						),
						'default' => 'none',
						'desc' => __( 'Slides links', 'care' )
					),
					'speed' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Animation speed (1000 = 1 second)', 'care' )
					),
					'delay' => array(
						'values' => false,
						'default' => '3000',
						'desc' => __( 'Animation delay (1000 = 1 second)', 'care' )
					),
					'p' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Post/page ID', 'care' )
					),
					'effect' => array(
						'values' => array(
							'random',
							'boxRandom',
							'fold',
							'fade'
						),
						'default' => 'random',
						'desc' => __( 'Animation effect', 'care' )
					)
				),
				'usage' => '[nivo_slider]<br/>[nivo_slider width="600" height="300" link="file" effect="boxRandom"]',
				'desc' => __( 'Nivo slider by attached to post images', 'care' )
			),
			# jcarousel
			'jcarousel' => array(
				'name' => 'jCarousel',
				'type' => 'single',
				'atts' => array(
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Carousel width', 'care' )
					),
					'height' => array(
						'values' => false,
						'default' => '130',
						'desc' => __( 'Carousel height', 'care' )
					),
					'bg' => array(
						'values' => false,
						'default' => '#EEEEEE',
						'desc' => __( 'Carousel background', 'care' ),
						'type' => 'color'
					),
					'items' => array(
						'values' => array(
							'3',
							'4',
							'5'
						),
						'default' => '3',
						'desc' => __( 'Number of items to show', 'care' )
					),
					'margin' => array(
						'values' => array(
							'5',
							'10',
							'15'
						),
						'default' => '10',
						'desc' => __( 'Space between items in pixels', 'care' )
					),
					'link' => array(
						'values' => array(
							'none',
							'file',
							'attachment',
							'caption'
						),
						'default' => 'none',
						'desc' => __( 'Items links', 'care' )
					),
					'speed' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Animation speed (1000 = 1 second)', 'care' )
					),
					'p' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Post/page ID', 'care' )
					)
				),
				'usage' => '[jcarousel]<br/>[jcarousel width="600" height="130" link="file" items="5" bg="#EEEEEE" speed="400"]',
				'desc' => __( 'jCarousel by attached to post images', 'care' )
			),
			# tweets
			'tweets' => array(
				'name' => 'Tweets',
				'type' => 'single',
				'atts' => array(
					'username' => array(
						'values' => array( ),
						'default' => 'twitter',
						'desc' => __( 'Twitter username', 'care' )
					),
					'limit' => array(
						'values' => array(
							'1',
							'3',
							'5',
							'7',
							'10'
						),
						'default' => '3',
						'desc' => __( 'Number of tweets to show', 'care' )
					),
					'style' => array(
						'values' => array(
							'1',
							'2'
						),
						'default' => '1',
						'desc' => __( 'Tweets style', 'care' )
					),
					'show_time' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '1',
						'desc' => __( 'Show relative time', 'care' )
					)
				),
				'usage' => '[tweets username="gn_themes" limit="3" style="1" format="teaser"]',
				'desc' => __( 'Recent tweets', 'care' )
			),
			
			# chart
			'chart' => array(
				'name' => 'Chart',
				'type' => 'single',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => 'Chart title',
						'desc' => __( 'Chart title', 'care' )
					),				
					'data' => array(
						'values' => array( ),
						'default' => '70,25,20.01,4.99',
						'desc' => __( 'Data', 'care' )
					),
					'labels' => array(
						'values' => array( ),
						'default' => 'Reffering+sites|Google|Yahoo|Other',
						'desc' => __( 'Lables', 'care' )
					),
					'colors' => array(
						'values' => array( ),
						'default' => '058DC7,50B432,ED561B,EDEF00',
						'desc' => __( 'Colors', 'care' )
					),
					'size' => array(
						'values' => array( ),
						'default' => '630x250',
						'desc' => __( 'Size', 'care' )
					),
					'advanced' => array(
						'values' => array( ),
						'default' => '&chdl=Reffering+sites|Google|Yahoo|Other',
						'desc' => __( 'Advanced (optional)', 'care' )
					),
					'type' => array(
						'values' => array(
							'xyline',
							'sparkline',
							'scatter',
							'venn',
							'line',
							'pie',
							'pie2d'
						),
						'default' => 'pie2d',
						'desc' => __( 'Chart type', 'care' )
					)
				),
				
				'usage' => '[chart]',
				'desc' => __( 'Google Chart', 'care' )
			),
			
			# style div
			'div' => array(
				'name' => 'Div',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array( ),
						'default' => __( '', 'care' ),
						'desc' => __( 'CSS style', 'care' )
					),
					'class' => array(
						'values' => array( ),
						'default' => __( '', 'care' ),
						'desc' => __( 'Your div class', 'care' )
					)
				),
				'usage' => '[div style=""] [/div]',
				'content' => __( 'Div content', 'care' ),
				'desc' => __( 'Your custom DIV', 'care' )
			),
			# clear
			'clear' => array(
				'name' => 'Clear floats',
				'type' => 'single',
				'usage' => '[clear]',
				'desc' => __( 'Clear div floats', 'care' )
			),
		);

		if ( $shortcode )
			return $shortcodes[$shortcode];
		else
			return $shortcodes;
	}

?>