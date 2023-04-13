<?php

/**
 * Plugin Name:       Feed for TikTok
 * Plugin URI:        https://sgmedia.ro/tik-tok-feed
 * Description:       -
 * Version:           1.0.2
 * Author:            Sabin Mehedin
 * Author URI:        https://sgmedia.ro
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tik-tok-feed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once __DIR__ . '/vendor/autoload.php';

use TikTokFeed\Includes\TikTokFeedActivator;
use TikTokFeed\Includes\TikTokFeedDeactivator;
use TikTokFeed\Includes\TikTokFeed;

define('TIK_TOK_FEED_VERSION', '1.0.0');
define('PLUGIN_TIK_TOK_FEED_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_TIK_TOK_FEED_URL', plugin_dir_url(__FILE__));
define('PLUGIN_TIK_TOK_FEED_SLUG', plugin_basename(__FILE__));

function create_tik_tok_feed_plugin_environment_type()
{
    define('PLUGIN_TIK_TOK_FEED_ENVIRONMENT_TYPE', wp_get_environment_type());
}
add_action('init', 'create_tik_tok_feed_plugin_environment_type');

function tik_tok_feed_activate_plugin()
{
    TikTokFeedActivator::activate();
}

function tik_tok_feed_deactivate_plugin()
{
    TikTokFeedDeactivator::deactivate();
}

register_activation_hook(__FILE__, 'tik_tok_feed_activate_plugin');
register_deactivation_hook(__FILE__, 'tik_tok_feed_deactivate_plugin');

function tik_tok_feed_run_plugin()
{
    (new TikTokFeed())->run();
}
tik_tok_feed_run_plugin();
