<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds;

use QuadLayers\TTF\Models\Feeds as Models_Feed;
use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;
use QuadLayers\TTF\Utils\Cache as Cache;

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

		return $this->handle_response( $feeds );
	}

	public static function get_rest_args() {
		return array();
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
