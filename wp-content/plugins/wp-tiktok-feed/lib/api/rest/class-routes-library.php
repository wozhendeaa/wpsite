<?php
namespace QuadLayers\TTF\Api\Rest;

use QuadLayers\TTF\Api\Rest\Endpoints\Route as Route_Interface;

use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Accounts\Get as API_Rest_Accounts_Get;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Accounts\Delete as API_Rest_Accounts_Delete;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Accounts\Create as API_Rest_Accounts_Create;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds\Get as API_Rest_Feeds_Get;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds\Create as API_Rest_Feeds_Create;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds\Edit as API_Rest_Feeds_Edit;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds\Delete as API_Rest_Feeds_Delete;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds\Clear_Cache as API_Rest_Feeds_Clear_Cache;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Settings\Get as API_Rest_Setting_Get;
use QuadLayers\TTF\Api\Rest\Endpoints\Backend\Settings\Save as API_Rest_Setting_Save;

use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\User_Profile\Load as API_Rest_Frontend_User_Profile;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\User_Video_List\Load as API_Rest_Frontend_User_Video_List;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\Hashtag_Video_List\Load as API_Rest_Frontend_Hashtag_Video_List;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\Trending_Video_List\Load as API_Rest_Frontend_Trending_Video_List;

class Routes_Library {

	protected static $instance;
	protected $routes = array();

	private static $rest_namespace = 'quadlayers/tiktok';

	private function __construct() {
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

	public function register( Route_Interface $instance ) {
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
		new API_Rest_Frontend_User_Video_List();
		new API_Rest_Frontend_Hashtag_Video_List();
		new API_Rest_Frontend_Trending_Video_List();
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
