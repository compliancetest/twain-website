<?php
/**
* Customize Buddy Press
*/

//Customize Join Group Button
add_filter('bp_get_group_join_button', 'cp_bp_get_group_join_button_filter');

function cp_bp_get_group_join_button_filter($button)
{
    $button['link_class'] .= " popup button button_medium button_red white_txt radius6";
    $button['link_text'] = "Join Community";
    $button['link_title'] = "Join Community";
    
    return $button;
}


//Customize Option Navs
add_filter('bp_get_options_nav_home', 'cp_bp_get_options_nav_home', 10, 2);
function cp_bp_get_options_nav_home($li, $item)
{
    if ( $item['slug'] == bp_current_action() ) {
        $selected = ' class="active"';
        $selected1 = 'selected';
    } else {
        $selected = '';
        $selected1 = '';
    }
    return '<li ' . $selected . '><a rel="tab1" class="defaulttab ' . $selected1 . '" href="' . $item['link'] . '"><span class="left icon" id="icon_test_suites"></span><span class="right text">TEST SUITES</span><div class="tabactive"></div><span class="clear"></span>' . $item['$item'] . '</a></li>';
}
add_filter('bp_get_options_nav_nav-wiki', 'cp_bp_get_options_nav_wiki', 10, 2);
function cp_bp_get_options_nav_wiki($li, $item)
{
    if ( $item['slug'] == bp_current_action() ) {
        $selected = ' class="active"';
        $selected1 = 'selected';
    } else {
        $selected = '';
        $selected1 = '';
    }
    return '<li ' . $selected . '><a rel="tab1" class="' . $selected1 . '" href="' . $item['link'] . '"><span class="left icon" id="icon_wiki"></span><span class="right text">WIKI</span><div class="tabactive"></div><span class="clear"></span>' . $item['$item'] . '</a></li>';
}
add_filter('bp_get_options_nav_admin', 'cp_bp_get_options_nav_admin', 10, 2);
function cp_bp_get_options_nav_admin($li, $item)
{
    if ( $item['slug'] == bp_current_action() ) {
        $selected = ' class="active"';
        $selected1 = 'selected';
    } else {
        $selected = '';
        $selected1 = '';
    }
    return '<li ' . $selected . '><a rel="tab1" class="' . $selected1 . '" href="' . $item['link'] . '"><span class="left icon" id="icon_admin"></span><span class="right text">ADMIN</span><div class="tabactive"></div><span class="clear"></span>' . $item['$item'] . '</a></li>';
}
