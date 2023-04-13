<?php add_action('after_setup_theme','himer_child_theme_locale');
function himer_child_theme_locale() {
	load_child_theme_textdomain('himer-child',get_stylesheet_directory().'/languages');
}
?>