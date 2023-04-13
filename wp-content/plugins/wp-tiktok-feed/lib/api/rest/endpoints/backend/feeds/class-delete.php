<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds;

use QuadLayers\TTF\Models\Feeds as Models_Feed;
use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;
use QuadLayers\TTF\Utils\Cache as Cache;

/**
 * API_Rest_Feeds_Delete Class
 */
class Delete extends Base {

	protected static $rest_route = 'feeds';

	public function callback( \WP_REST_Request $request ) {

		$feed = json_decode( wp_json_encode( $request->get_param( 'feed_settings' ) ), true );

		if ( ! isset( $feed['id'] ) ) {
			$message = array(
				'message' => esc_html__( 'Feed id not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}
		$feed_id = $feed['id'];

		$models_feeds = new Models_Feed();
		$success      = $models_feeds->delete( $feed_id );

		if ( ! $success ) {
			$response = array(
				'code'    => 404,
				'message' => esc_html__( 'Can\'t delete feed, feed_id not found', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}
		$feed_md5  = md5( wp_json_encode( $feed ) );
		$cache_key = "feed_{$feed_md5}";

		$cache_engine = new Cache( 6, true, $cache_key );

		if ( ! isset( $feed['source'] ) ) {
			$message = array(
				'message' => esc_html__( 'Feed source not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		if ( 'account' === $feed['source'] ) {
			$cache_engine->delete( $cache_key );
		} else {
			$cache_engine->delete_key( $cache_key );
		}

		if ( 'username' === $feed['source'] ) {

			if ( ! isset( $feed['username'] ) ) {
				$message = array(
					'message' => esc_html__( 'Feed username not found.', 'wp-tiktok-feed' ),
					'code'    => '400',
				);
				return $this->handle_response( $message );
			}

			$feed_profile_cache   = "profile_{$feed['username']}";
			$profile_cache_engine = new Cache( 6, true, $feed_profile_cache );
			$profile_cache_engine->delete( $feed_profile_cache );
		}

		return $this->handle_response( $success );
	}

	public static function get_rest_args() {
		return array(
			'feed_settings' => array(
				'required'          => true,
				'validate_callback' => function( $param ) {
					return is_array( json_decode( wp_json_encode( $param ), true ) );
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
