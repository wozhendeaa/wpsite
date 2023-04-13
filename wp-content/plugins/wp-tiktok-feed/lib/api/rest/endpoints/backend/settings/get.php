<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Settings;

use QUADLAYERS\TIKTOK\Models\Settings as Models_Settings;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;

/**
 * API_Rest_Setting_Get Class
 */

class Get extends Base {

	protected static $rest_route = 'settings';

	public function callback( \WP_REST_Request $request ) {

		$models_settings = new Models_Settings();

		$settings = $models_settings->get();

		if ( null === $settings || 0 === count( $settings ) ) {
			$response = array(
				'code'    => 500,
				'message' => esc_html__( 'Unknown error', 'wp-tiktok-feed' ),
			);	
			return $this->handle_response( $response );			
		}

		return $this->handle_response( $settings );
	}

	public static function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}
	public function get_rest_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		return true;
	}
}
