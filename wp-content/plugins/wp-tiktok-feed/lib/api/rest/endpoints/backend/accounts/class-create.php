<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Backend\Accounts;

use QuadLayers\TTF\Models\Accounts as Models_Account;
use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;

/**
 * API_Rest_Accounts_Create Class
 */
class Create extends Base {

	protected static $rest_route = 'accounts';

	public function callback( \WP_REST_Request $request ) {

		$body = json_decode( $request->get_body() );

		if ( empty( $body->refresh_token ) ) {
			$response = array(
				'code'    => 412,
				'message' => esc_html__( 'refresh_token not setted.', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		$refresh_token = $body->refresh_token;

		$models_account = new Models_Account();

		$account_data = array(
			'refresh_token' => $refresh_token,
		);

		$account = $models_account->add_account( $account_data );

		if ( ! isset( $account['open_id'] ) ) {
			$response = array(
				'code'    => $account['error'],
				'message' => $account['message'],
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $account );
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
