<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Backend\Accounts;

use QUADLAYERS\TIKTOK\Models\Accounts as Models_Account;
use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\TIKTOK\Utils\Cache as Cache;

/**
 * API_Rest_Accounts_Delete Class
 */
class Delete extends Base {

	protected static $rest_route = 'accounts';

	protected $profile_cache_key = 'profile';

	public function callback( \WP_REST_Request $request ) {

		$open_id = trim( $request->get_param( 'open_id' ) );

		$models_account = new Models_Account();

		$success = $models_account->delete_account( $open_id );

		if ( ! $success ) {
			$response = array(
				'code'    => 404,
				'message' => esc_html__( 'Can\'t delete account, open_id not found', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		$cache_key = "{$this->profile_cache_key}_{$open_id}";

		$cache_engine = new Cache( 6, true, $cache_key );

		$cache_engine->delete( $cache_engine );

		return $this->handle_response( $success );
	}

	public static function get_rest_args() {
		return array(
			'open_id' => array(
				'required' => true,
				'type'     => 'string',
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
