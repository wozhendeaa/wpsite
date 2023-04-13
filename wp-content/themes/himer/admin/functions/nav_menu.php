<?php register_nav_menus(array(
	'wpqa_explore'        => 'Left Menu - Unlogged Users',
	'wpqa_explore_login'  => 'Left Menu - Logged Users',
	'header_menu'         => 'Main Menu - Unlogged Users',
	'header_menu_login'   => 'Main Menu - Logged Users',
	'header_2_menu'       => 'Secondary Header Menu - Unlogged Users',
	'header_2_menu_login' => 'Secondary Header Menu - Logged Users',
));
function himer_nav_fallback() {
	echo '<div class="menu-alert">'.esc_html__('You can use WP menu builder to build menus',"himer").'</div>';
}
function himer_empty_fallback() {
	echo '';
}?>