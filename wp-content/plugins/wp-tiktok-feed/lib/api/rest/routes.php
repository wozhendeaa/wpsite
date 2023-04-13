<?php
namespace QUADLAYERS\TIKTOK\Api\Rest;

use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\RouteInterface as RouteInterface;

use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Accounts\Get as API_Rest_Accounts_Get;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Accounts\Delete as API_Rest_Accounts_Delete;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Accounts\Create as API_Rest_Accounts_Create;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\Get as API_Rest_Feeds_Get;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\Create as API_Rest_Feeds_Create;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\Edit as API_Rest_Feeds_Edit;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\Delete as API_Rest_Feeds_Delete;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds\ClearCache as API_Rest_Feeds_Clear_Cache;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Settings\Get as API_Rest_Setting_Get;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Settings\Save as API_Rest_Setting_Save;

use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\UserProfile\Load as API_Rest_Frontend_User_Profile;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\UserVideoList\Load as API_Rest_Frontend_User_VideoList;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\HashtagVideoList\Load as API_Rest_Frontend_Hashtag_VideoList;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\TrendingVideoList\Load as API_Rest_Frontend_Trending_VideoList;

class Routes {

	protected static $instance;
	protected $routes = array();

	private static $rest_namespace = 'quadlayers/tiktok';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, '_rest_init' ) );
	}

	public static function get_namespace() {
		return self::$rest_namespace;
	}

	public function get_routes( $rest_route = null ) {
		if ( ! $rest_route ) {
			return $this->routes;
		}
		if ( isset( $this->routes[ $rest_route ] ) ) {
			return $this->routes[ $rest_route ];
		}
	}

	public function register( RouteInterface $instance ) {
		$this->routes[ $instance::get_name() ] = $instance;
	}

	public function _rest_init() {
		// Backend
		new API_Rest_Accounts_Get();
		new API_Rest_Accounts_Delete();
		new API_Rest_Accounts_Create();
		new API_Rest_Feeds_Get();
		new API_Rest_Feeds_Create();
		new API_Rest_Feeds_Edit();
		new API_Rest_Feeds_Delete();
		new API_Rest_Feeds_Clear_Cache();
		new API_Rest_Setting_Get();
		new API_Rest_Setting_Save();
		// Frontend
		new API_Rest_Frontend_User_Profile();
		new API_Rest_Frontend_User_VideoList();
		new API_Rest_Frontend_Hashtag_VideoList();
		new API_Rest_Frontend_Trending_VideoList();
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
