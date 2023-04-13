<?php

namespace QUADLAYERS\TIKTOK\Backend;

use QUADLAYERS\TIKTOK\Utils\Helpers as Helpers;
use QUADLAYERS\TIKTOK\Models\Accounts as Models_Account;
use QUADLAYERS\TIKTOK\Models\Feeds as Models_Feed;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\UserProfile\Load as API_Rest_User_Profile;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Accounts\Get as API_Rest_Accounts_Get;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\Get as API_Rest_Feeds_Get;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\ClearCache as API_Rest_Feeds_Clear_Cache;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Settings\Get as API_Rest_Setting_Get;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\ExternalUserProfile\Load as API_Rest_Frontend_External_Profile;

/**
 * Backend Class
 */
class Load {

	protected static $instance;
	protected static $menu_slug = QLTTF_DOMAIN . '_backend';

	public function __construct() {
		add_action( 'admin_init', array( self::class, 'init_add_account' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_footer', array( __CLASS__, 'add_premium_css' ) );
	}

	public static function get_menu_slug() {
		return self::$menu_slug;
	}

	function add_menu() {
		$menu_slug = self::get_menu_slug();
		add_menu_page(
			QLTTF_PLUGIN_NAME,
			QLTTF_PLUGIN_NAME,
			'edit_posts',
			$menu_slug,
			'__return_null',
			plugins_url( '/assets/backend/img/tiktok-white.svg', QLTTF_PLUGIN_FILE )
		);
		add_submenu_page(
			$menu_slug,
			esc_html__( 'Welcome', 'wp-tiktok-feed' ),
			esc_html__( 'Welcome', 'wp-tiktok-feed' ),
			'edit_posts',
			$menu_slug,
			'__return_null'
		);
		add_submenu_page(
			$menu_slug,
			esc_html__( 'Accounts', 'wp-tiktok-feed' ),
			esc_html__( 'Accounts', 'wp-tiktok-feed' ),
			'manage_options',
			"{$menu_slug}&tab=accounts",
			'__return_null'
		);
		add_submenu_page(
			$menu_slug,
			esc_html__( 'Feeds', 'wp-tiktok-feed' ),
			esc_html__( 'Feeds', 'wp-tiktok-feed' ),
			'manage_options',
			"{$menu_slug}&tab=feeds",
			'__return_null'
		);
		add_submenu_page(
			$menu_slug,
			esc_html__( 'Settings', 'wp-tiktok-feed' ),
			esc_html__( 'Settings', 'wp-tiktok-feed' ),
			'manage_options',
			"{$menu_slug}&tab=settings",
			'__return_null'
		);
		add_submenu_page(
			$menu_slug,
			esc_html__( 'Premium', 'wp-tiktok-feed' ),
			sprintf(
				'%s <i class="dashicons dashicons-awards"></i>',
				esc_html__( 'Premium', 'wp-tiktok-feed' )
			),
			'edit_posts',
			"{$menu_slug}&tab=premium",
			'__return_null'
		);

	}


	function enqueue_scripts() {

		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== self::get_menu_slug() ) {
			return;
		}

		$backend = include_once QLTTF_PLUGIN_DIR . 'build/backend/js/index.asset.php';

		wp_enqueue_style(
			'qlttf-backend',
			plugins_url( '/build/backend/css/style.css', QLTTF_PLUGIN_FILE ),
			array(
				'media-views',
				'wp-components',
				'wp-editor',
			),
			QLTTF_PLUGIN_VERSION
		);

		wp_enqueue_media();

		wp_enqueue_script(
			'qlttf-backend',
			plugins_url( '/build/backend/js/index.js', QLTTF_PLUGIN_FILE ),
			$backend['dependencies'],
			$backend['version'],
			true
		);

		$models_feeds = new Models_Feed();

		wp_localize_script(
			'qlttf-backend',
			'qlttf_backend',
			array(
				'plugin_url'                      => plugins_url( '/', QLTTF_PLUGIN_FILE ),
				'QLTTF_PLUGIN_NAME'               => QLTTF_PLUGIN_NAME,
				'QLTTF_PLUGIN_VERSION'            => QLTTF_PLUGIN_VERSION,
				'QLTTF_PLUGIN_FILE'               => QLTTF_PLUGIN_FILE,
				'QLTTF_PLUGIN_DIR'                => QLTTF_PLUGIN_DIR,
				'QLTTF_DOMAIN'                    => QLTTF_DOMAIN,
				'QLTTF_PREFIX'                    => QLTTF_PREFIX,
				'QLTTF_WORDPRESS_URL'             => QLTTF_WORDPRESS_URL,
				'QLTTF_REVIEW_URL'                => QLTTF_REVIEW_URL,
				'QLTTF_DEMO_URL'                  => QLTTF_DEMO_URL,
				'QLTTF_PURCHASE_URL'              => QLTTF_PURCHASE_URL,
				'QLTTF_SUPPORT_URL'               => QLTTF_SUPPORT_URL,
				'QLTTF_DOCUMENTATION_URL'         => QLTTF_DOCUMENTATION_URL,
				'QLTTF_DOCUMENTATION_API_URL'     => QLTTF_DOCUMENTATION_API_URL,
				'QLTTF_DOCUMENTATION_ACCOUNT_URL' => QLTTF_DOCUMENTATION_ACCOUNT_URL,
				'QLTTF_GROUP_URL'                 => QLTTF_GROUP_URL,
				'QLTTF_DEVELOPER'                 => QLTTF_DEVELOPER,
				'QLTTF_TIKTOK_URL'                => QLTTF_TIKTOK_URL,
				'QLTTF_ACCESS_TOKEN_LINK'         => Helpers::get_access_token_link(),
				'QLTTF_FEED_MODEL'                => $models_feeds->get_args(),
				'QLTTF_REST_ROUTES'               => array(
					'account_profile' => API_Rest_User_Profile::get_rest_path(),
					'username_profile' => API_Rest_Frontend_External_Profile::get_rest_path(),
					'accounts'    => API_Rest_Accounts_Get::get_rest_path(),
					'feeds'       => API_Rest_Feeds_Get::get_rest_path(),
					'settings'    => API_Rest_Setting_Get::get_rest_path(),
					'cache'       => API_Rest_Feeds_Clear_Cache::get_rest_path(),
				),
			)
		);

	}

	public static function init_add_account() {

		if ( isset(
			$_REQUEST['accounts'][0]['open_id'],
			$_REQUEST['accounts'][0]['access_token'],
			$_REQUEST['accounts'][0]['access_token_expires_in'],
			$_REQUEST['accounts'][0]['refresh_token'],
			$_REQUEST['accounts'][0]['refresh_token_expires_in']
		) ) {

			$models_account = new Models_Account();

			$sanitized_response = array_map(
				function( $value ) {
					return sanitize_text_field( base64_decode( $value ) );
				},
				$_REQUEST['accounts'][0]
			);

			$open_id                       = $sanitized_response['open_id'];
			$access_token                  = $sanitized_response['access_token'];
			$access_token_expires_in       = $sanitized_response['access_token_expires_in'];
			$refresh_token                 = $sanitized_response['refresh_token'];
			$refresh_token_expires_in      = $sanitized_response['refresh_token_expires_in'];
			$access_token_expiration_date  = $models_account->calculate_expiration_date( $sanitized_response['access_token_expires_in'] );
			$refresh_token_expiration_date = $models_account->calculate_expiration_date( $sanitized_response['refresh_token_expires_in'] );

			$models_account->add_account(
				array(
					'open_id'                       => $open_id,
					'access_token'                  => $access_token,
					'access_token_expires_in'       => $access_token_expires_in,
					'refresh_token'                 => $refresh_token,
					'refresh_token_expires_in'      => $refresh_token_expires_in,
					'access_token_expiration_date'  => $access_token_expiration_date,
					'refresh_token_expiration_date' => $refresh_token_expiration_date,
				)
			);

			if ( wp_safe_redirect( QLTTF_ACCOUNT_URL ) ) {
				exit;
			}
		}

	}

	public static function add_premium_css() {
		?>
			<style>
				.qlttf-premium-field {
					opacity: 0.5;
					pointer-events: none;
				}
				.qlttf-premium-field input, 
				.qlttf-premium-field textarea, 
				.qlttf-premium-field select {					
					background-color: #eee;
				}
				.qlttf-premium-field .description {
					display: inline-block !important;
				}
			</style>
		<?php
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
