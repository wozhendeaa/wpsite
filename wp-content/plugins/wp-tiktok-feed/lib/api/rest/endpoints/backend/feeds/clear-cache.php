<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds;

use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\TIKTOK\Models\Feeds as Models_Feed;
use QUADLAYERS\TIKTOK\Utils\Cache as Cache;

/**
 * API_Rest_Feeds_Clear_Cache Class
 */
class ClearCache extends Base {

	protected static $rest_route = 'feeds/clear-cache';

	public function callback( \WP_REST_Request $request ) {

		$models_feeds = new Models_Feed();

		$feed_id = trim( $request->get_param( 'feed_id' ) );

		$feed = $models_feeds->get_by_id( $feed_id );

		$open_id = $feed['open_id'];

		if ( null === $feed ) {
			$response = array(
				'code'    => 404,
				'message' => esc_html__( 'feed_id not found in table, the cache is not refresh ', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );

		}

		$cache_key = "feed_{$feed_id}";

		$cache_engine = new Cache( 6, true, $cache_key );

		$cache_engine->delete( $cache_key );

		return $this->handle_response( $feed );
	}

	public static function get_rest_args() {
		return array(
			'feed_id' => array(
				'required'          => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
		);
	}

	public static function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}

	public function get_rest_permission() {
		/*
		 if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		} */
		return true;
	}
}
