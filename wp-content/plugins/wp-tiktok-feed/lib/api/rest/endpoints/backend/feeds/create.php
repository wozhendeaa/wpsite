<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Feeds;

use QUADLAYERS\TIKTOK\Models\Feeds as Models_Feed;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;

/**
 * API_Rest_Feeds_Create Class
 */
class Create extends Base {

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

		$models_feeds = new Models_Feed();

		$feed = $models_feeds->add( $body['feed'] );

		if ( ! $feed ) {
			$response = array(
				'code'    => 500,
				'message' => esc_html__( 'Unknown error', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $feed );
	}

	public static function get_rest_args() {
		return array(
			'feed' => array(
				'required' => true,
			),
		);
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
