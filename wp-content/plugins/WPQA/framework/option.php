<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Save default options */
if (!get_option(wpqa_options)) {
	$wpqa_admin_options = new wpqa_admin_options;
	$default_options = $wpqa_admin_options->get_default_values();
	add_option(wpqa_options,$default_options);
}?>