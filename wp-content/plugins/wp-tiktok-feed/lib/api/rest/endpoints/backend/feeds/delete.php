<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds;

use QUADLAYERS\TIKTOK\Models\Feeds as Models_Feed;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\TIKTOK\Utils\Cache as Cache;

/**
 * API_Rest_Feeds_Delete Class
 */
class Delete extends Base {

	protected static $rest_route = 'feeds';

	public function callback( \WP_REST_Request $request ) {

		$feed_id = $request->get_param( 'feed_id' );

		$models_feeds = new Models_Feed();
		$feed         = $models_feeds->get_by_id( $feed_id );

		$success = $models_feeds->delete( $feed_id );

		if ( ! $success ) {
			$response = array(
				'code'    => 404,
				'message' => esc_html__( 'Can\'t delete feed, feed_id not found', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		$cache_key = "feed_{$feed_id}";

		$cache_engine = new Cache( 6, true, $cache_key );

		if ( 'account' === $feed['source'] ) {
			$cache_engine->delete( $cache_key );
		} else {
			$cache_engine->delete_key( $cache_key );
		}

		return $this->handle_response( $success );
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
		return \WP_REST_Server::DELETABLE;
	}

	public function get_rest_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		return true;
	}

}
