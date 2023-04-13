<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds;

use QUADLAYERS\TIKTOK\Models\Feeds as Models_Feed;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\TIKTOK\Utils\Cache as Cache;

/**
 * API_Rest_Feeds_Edit Class
 */
class Edit extends Base {

	protected static $rest_route = 'feeds';

	public function callback( \WP_REST_Request $request ) {

		$body = json_decode( $request->get_body(), true );

		if ( empty( $body['feed'] ) ) {
			$response = array(
				'code'    => 412,
				'message' => esc_html__( 'Feed not setted', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		$feed = $body['feed'];

		$models_feeds = new Models_Feed();

		$feeds = $models_feeds->edit( $feed );

		if ( ! $feeds ) {
			$response = array(
				'code'    => 412,
				'message' => esc_html__( 'Feed can not be updated', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		$cache_key = "feed_{$feed['id']}";

		$cache_engine = new Cache( 6, true, $cache_key );

		if ( 'account' === $feed['source'] ) {
			$cache_engine->delete( $cache_key );
		} else {
			$cache_engine->delete_key( $cache_key );
		}

		return $this->handle_response( $feeds );
	}

	public static function get_rest_args() {
		return array(
			'feed' => array(
				'required' => true,
			),
		);
	}

	public static function get_rest_method() {
		return \WP_REST_Server::EDITABLE;
	}

	public function get_rest_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		return true;
	}
}
