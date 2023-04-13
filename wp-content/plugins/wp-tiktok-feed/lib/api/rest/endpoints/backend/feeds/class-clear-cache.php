<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds;

use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;
use QuadLayers\TTF\Models\Feeds as Models_Feed;
use QuadLayers\TTF\Utils\Cache as Cache;

/**
 * API_Rest_Feeds_Clear_Cache Class
 */
class Clear_Cache extends Base {

	protected static $rest_route = 'feeds/clear-cache';

	public function callback( \WP_REST_Request $request ) {

		$body = json_decode( $request->get_body(), true );

		if ( ! isset( $body['feedSettings'] ) ) {
			$message = array(
				'message' => esc_html__( 'Bad Request, feed settings not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		$feed = $body['feedSettings'];

		$feed_md5     = md5( wp_json_encode( $feed ) );
		$cache_key    = "feed_{$feed_md5}";
		$cache_engine = new Cache( 6, true, $cache_key );

		$cache_engine->delete( $cache_key );

		if ( ! isset( $feed['source'] ) ) {
			$message = array(
				'message' => esc_html__( 'Feed source not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
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

		return $this->handle_response( $feed );
	}

	public static function get_rest_args() {
		return array();
	}

	public static function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}

	public function get_rest_permission() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		return true;
	}
}
