<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Backend\Accounts;

use QuadLayers\TTF\Models\Accounts as Models_Account;
use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;

/**
 * API_Rest_Accounts_Get Class
 */
class Get extends Base {

	protected static $rest_route = 'accounts';

	public function callback( \WP_REST_Request $request ) {

		$models_account = new Models_Account();

		$open_id = trim( $request->get_param( 'open_id' ) );

		if ( ! $open_id ) {

			$accounts = $models_account->get();

			if ( null !== $accounts && 0 !== count( $accounts ) ) {
				return $this->handle_response( $accounts );
			}

			$response = array(
				'code'    => 404,
				'message' => esc_html__( 'Accounts empty', 'wp-tiktok-feed' ),
			);
		}

		$account = $models_account->get_account( $open_id );

		if ( ! $account ) {
			$response = array(
				'code'    => 404,
				'message' => sprintf( esc_html__( 'Account %s not found', 'wp-tiktok-feed' ), $open_id ),
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $account );

	}

	public static function get_rest_args() {
		return array(
			'open_id' => array(
				'required' => false,
			),
		);
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
