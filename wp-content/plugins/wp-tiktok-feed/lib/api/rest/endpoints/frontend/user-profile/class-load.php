<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Frontend\User_Profile;

use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;
use QuadLayers\TTF\Models\Accounts as Models_Account;
use QuadLayers\TTF\Api\Fetch\User_Profile\Get as API_Fetch_User_Profile;
use QuadLayers\TTF\Utils\Cache;

/**
 * API_Rest_Frontend_User_Profile Class
 */
class Load extends Base {

	/**
	 * Constant $rest_route - defines endpoint route.
	 *
	 * @var string
	 */
	protected static $rest_route = 'frontend/user-profile';
	protected $profile_cache_engine;
	protected $profile_cache_key = 'profile';

	/**
	 * Function callback() - executes callback for and rest endpoint.
	 *
	 * @param \WP_REST_Request $request
	 * @return object
	 */
	public function callback( \WP_REST_Request $request ) {

		$body = json_decode( $request->get_body(), true );

		if ( ! isset( $body['feedSettings'] ) ) {
			$message = array(
				'message' => esc_html__( 'Bad Request, feed settings not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		if ( ! isset( $body['feedSettings']['open_id'] ) ) {
			$message = array(
				'message' => esc_html__( 'Bad Request, feed settings not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		$open_id = trim( $body['feedSettings']['open_id'] );

		$profile_complete_prefix = "{$this->profile_cache_key}_{$open_id}";

		$this->profile_cache_engine = new Cache( 6, true, $profile_complete_prefix );

		$response = $this->profile_cache_engine->get( $profile_complete_prefix );

		if ( ! empty( $response['response'] ) ) {
			return $response['response'];
		}

		$models_account = new Models_Account();
		$account        = $models_account->get_account( $open_id );

		if ( ! isset( $account['access_token'] ) ) {
			return $this->handle_response(
				array(
					'code'    => 412,
					'message' => sprintf( esc_html__( 'Account id %s not found', 'wp-tiktok-feed' ), $open_id ),
				)
			);
		}

		$access_token = $account['access_token'];

		$fetch_user_profile = new API_Fetch_User_Profile();

		$args = array(
			'open_id'      => $open_id,
			'access_token' => $access_token,
			'fields'       => array(
				'username',
				'profile_deep_link',
				'display_name',
				'bio_description',
				'avatar_url_100',
			),
		);

		$response = $fetch_user_profile->get_data( $args );

		if ( isset( $response['message'] ) && isset( $response['code'] ) ) {
			return $this->handle_response( $response );
		}

		if ( ! QLTTF_DEVELOPER ) {
			$this->profile_cache_engine->update( $profile_complete_prefix, $response );
		}

		return $this->handle_response( $response );
	}

	/**
	 * Function get_rest_args() - returns rest args.
	 *
	 * @return object
	 */
	public static function get_rest_args() {
		return array();
	}

	/**
	 * Function get_rest_method() - returns rest method.
	 *
	 * @return string
	 */
	public static function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}
	public function get_rest_permission() {
		return true;
	}
}
