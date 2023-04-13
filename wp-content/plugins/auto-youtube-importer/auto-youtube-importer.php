<?php
/**
 * Plugin Name:       Auto YouTube Importer
 * Description:       A simple YouTube video importer plugin with automatic / ongoing YouTube sync features.
 * Version:           1.0.5
 * Author:            SecondLineThemes
 * Author URI:        https://secondlinethemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       auto-youtube-importer
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) )
	die;

define( 'YOUTUBE_IMPORTER_SECONDLINE_VERSION', '1.0.5' );
define( "YOUTUBE_IMPORTER_SECONDLINE_BASE_FILE_PATH", __FILE__ );
define( "YOUTUBE_IMPORTER_SECONDLINE_BASE_PATH", dirname( YOUTUBE_IMPORTER_SECONDLINE_BASE_FILE_PATH ) );
define( "YOUTUBE_IMPORTER_SECONDLINE_PLUGIN_IDENTIFIER", ltrim( str_ireplace( dirname( YOUTUBE_IMPORTER_SECONDLINE_BASE_PATH ), '', YOUTUBE_IMPORTER_SECONDLINE_BASE_FILE_PATH ), '/' ) );

define( "YOUTUBE_IMPORTER_SECONDLINE_EXAMPLE_CHANNEL_ID", "UCxCxlu6_VkHBqql706Mmc3A" );

require_once YOUTUBE_IMPORTER_SECONDLINE_BASE_PATH . "/autoload.php";
require_once YOUTUBE_IMPORTER_SECONDLINE_BASE_PATH . "/definitions.php";
require_once YOUTUBE_IMPORTER_SECONDLINE_BASE_PATH . "/functions.php";
require_once YOUTUBE_IMPORTER_SECONDLINE_BASE_PATH . '/lib/action-scheduler/action-scheduler.php';

YoutubeImporterSecondLine\ActionScheduler::instance()->setup();

// Various Hooks & Additions.
YoutubeImporterSecondLine\Hooks::instance()->setup();

// Post Types
add_action( 'init', [ YoutubeImporterSecondLine\PostTypes::instance(), 'setup' ] );

// RestAPI
add_action( 'rest_api_init', [ YoutubeImporterSecondLine\RestAPI::instance(), 'setup' ] );

// General Functionality
add_action( 'plugins_loaded', [ YoutubeImporterSecondLine\Controller::instance(), 'setup' ] );

if ( is_admin() ) {
  add_action( 'admin_menu', [ YoutubeImporterSecondLine\AdminMenu::instance(), 'setup' ] );
  add_action( 'admin_enqueue_scripts', [ YoutubeImporterSecondLine\AdminAssets::instance(), 'setup' ] );
}

register_deactivation_hook( __FILE__, function() {
  as_unschedule_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feeds_sync' );
} );