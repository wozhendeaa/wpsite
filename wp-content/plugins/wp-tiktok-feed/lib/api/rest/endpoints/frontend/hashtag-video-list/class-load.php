<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Frontend\Hashtag_Video_List;

use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;

/**
 * API_Rest_Frontend_Hashtag_Video_List Class
 */

class Load extends Base {

	protected static $rest_route = 'frontend/hashtag-video-list';

	public function callback( \WP_REST_Request $request ) {

		$response = array(
			'code'    => '412',
			'message' => esc_html__( 'Hashtag is a premium feature.', 'wp-tiktok-feed' ),
		);

		return $this->handle_response( $response );
	}

	public static function get_rest_args() {
		return array();
	}

	public static function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}

	public function get_rest_permission() {
		return true;
	}
}
