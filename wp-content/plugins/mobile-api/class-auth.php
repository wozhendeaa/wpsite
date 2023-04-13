<?php
/**
 * Setup mobile-api.
 *
 * @package mobile-api
 */

namespace MOBILEAPIAuth;

use Exception;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use Firebase\MOBILEAPIAUT\MOBILEAPIAUT;

/**
 * The public-facing functionality of the plugin.
 */
class MOBILEAPIAuthe {
	/**
	 * The namespace to add to the api calls.
	 *
	 * @var string The namespace to add to the api call
	 */
	private $namespace;

	/**
	 * Store errors to display if the MOBILEAPIAUT is wrong
	 *
	 * @var WP_REST_Response
	 */
	private $mobile_api_error = null;

	/**
	 * Collection of translate-able messages.
	 *
	 * @var array
	 */
	private $messages = array();

	/**
	 * The REST API slug.
	 *
	 * @var string
	 */
	private $rest_api_slug = 'wp-json';

	/**
	 * Setup action & filter hooks.
	 */
	public function __construct() {
		$this->namespace = mobile_api_base.'/v1';

		$this->messages = array(
			'mobile_api_no_auth_header'  => esc_html__( 'Authorization header not found.', 'mobile-api' ),
			'mobile_api_bad_auth_header' => esc_html__( 'Authorization header malformed.', 'mobile-api' ),
		);
	}

	/**
	 * Add the endpoints to the API
	 */
	public function register_rest_routes() {
		register_rest_route(
			$this->namespace,
			'token',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'get_token' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'token/validate',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'validate_token' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);
	}

	/**
	 * Check permission
	 */
	public function check_permission() {
		return true;
	}

	/**
	 * Add CORs suppot to the request.
	 */
	public function add_cors_support() {
		$enable_cors = defined( 'MOBILE_API_CORS_ENABLE' ) ? MOBILE_API_CORS_ENABLE : false;

		if ( $enable_cors ) {
			$headers = apply_filters( 'mobile_api_auth_cors_allow_headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization' );

			header( sprintf( 'Access-Control-Allow-Headers: %s', $headers ) );
		}
	}

	/**
	 * Authenticate user either via wp_authenticate or custom auth (e.g: OTP).
	 *
	 * @param string $username The username.
	 * @param string $password The password.
	 * @param mixed  $custom_auth The custom auth data (if any).
	 *
	 * @return WP_User|WP_Error $user Returns WP_User object if success, or WP_Error if failed.
	 */
	public function authenticate_user( $username, $password, $custom_auth = '' ) {
		// If using custom authentication.
		if ( $custom_auth ) {
			$custom_auth_error = new WP_Error( 'mobile_api_custom_auth_failed', esc_html__( 'Custom authentication failed.', 'mobile-api' ) );

			/**
			 * Do your own custom authentication and return the result through this filter.
			 * It should return either WP_User or WP_Error.
			 */
			$user = apply_filters( 'mobile_api_auth_do_custom_auth', $custom_auth_error, $username, $password, $custom_auth );
		} else {
			$user = wp_authenticate( $username, $password );
		}

		return $user;
	}

	/**
	 * Get token by sending POST request to api/v1/token.
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response The response.
	 */
	public function get_token( WP_REST_Request $request ) {
		$secret_key = defined( 'MOBILE_API_SECRET_KEY' ) ? MOBILE_API_SECRET_KEY : false;

		$username    = $request->get_param( 'username' );
		$password    = $request->get_param( 'password' );
		$custom_auth = $request->get_param( 'custom_auth' );

		// First thing, check the secret key if not exist return a error.
		if ( ! $secret_key ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'mobile_api_bad_config',
					'message'    => esc_html__( 'MOBILEAPIAUT is not configurated properly.', 'mobile-api' ),
					'data'       => array(),
				)
			);
		}

		$user = $this->authenticate_user( $username, $password, $custom_auth );

		// If the authentication is failed return error response.
		if ( is_wp_error( $user ) ) {
			$error_code = $user->get_error_code();

			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => $error_code,
					'message'    => strip_tags( $user->get_error_message( $error_code ) ),
					'data'       => array(),
				)
			);
		}

		// Valid credentials, the user exists, let's generate the token.
		return $this->generate_token( $user, false );
	}

	/**
	 * Generate token
	 *
	 * @param WP_User $user The WP_User object.
	 * @param bool    $return_raw Whether or not to return as raw token string.
	 *
	 * @return WP_REST_Response|string Return as raw token string or as a formatted WP_REST_Response.
	 */
	public function generate_token( $user, $return_raw = true ) {

		$mobile_time_login = mobile_api_options("mobile_time_login");
		$mobile_metric = "days";
		$expire_time = DAY_IN_SECONDS;
		if ($mobile_time_login == "hours") {
			$mobile_metric = "hours";
			$expire_time = HOUR_IN_SECONDS;
		}else if ($mobile_time_login == "minutes") {
			$mobile_metric = "minutes";
			$expire_time = MINUTE_IN_SECONDS;
		}else if ($mobile_time_login == "seconds") {
			$mobile_metric = "seconds";
			$expire_time = 1;
		}
		$mobile_time_login_time = (int)(mobile_api_options("mobile_time_login_".$mobile_metric)?mobile_api_options("mobile_time_login_".$mobile_metric):7);
		$mobile_time_login_time = ($mobile_time_login_time > 0?$mobile_time_login_time:7);
		$expire_time = $expire_time * $mobile_time_login_time;

		$secret_key = defined( 'MOBILE_API_SECRET_KEY' ) ? MOBILE_API_SECRET_KEY : false;
		$issued_at  = time();
		$not_before = $issued_at;
		$not_before = apply_filters( 'mobile_api_auth_not_before', $not_before, $issued_at );
		$expire     = $issued_at + ( $expire_time );
		$expire     = apply_filters( 'mobile_api_auth_expire', $expire, $issued_at );

		$payload = array(
			'iss'  => $this->get_iss(),
			'iat'  => $issued_at,
			'nbf'  => $not_before,
			'exp'  => $expire,
			'data' => array(
				'user' => array(
					'id' => (isset($user->ID)?$user->ID:0),
				),
			),
		);

		$alg = $this->get_alg();

		// Let the user modify the token data before the sign.
		$token = MOBILEAPIAUT::encode( apply_filters( 'mobile_api_auth_payload', $payload, $user ), $secret_key, $alg );

		// If return as raw token string.
		if ( $return_raw ) {
			return $token;
		}

		// The token is signed, now create object with basic info of the user.
		$response = array(
			'success'    => true,
			'statusCode' => 200,
			'code'       => 'mobile_api_valid_credential',
			'message'    => esc_html__('Credential is valid','mobile-api'),
			"token"      => $token,
			"user"       => mobile_api_user_info((isset($user->ID)?$user->ID:0),"login")
		);

		// Let the user modify the data before send it back.
		return apply_filters( 'mobile_api_auth_valid_credential_response', $response, $user );
	}

	/**
	 * Get the token issuer.
	 *
	 * @return string The token issuer (iss).
	 */
	public function get_iss() {
		return apply_filters( 'mobile_api_auth_iss', get_bloginfo( 'url' ) );
	}

	/**
	 * Get the supported auth signing algorithm.
	 *
	 * @see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
	 *
	 * @return string $alg
	 */
	public function get_alg() {
		return apply_filters( 'mobile_api_auth_alg', 'HS256' );
	}

	/**
	 * Determine if given response is an error response.
	 *
	 * @param mixed $response The response.
	 * @return boolean
	 */
	public function is_error_response( $response ) {
		if ( ! empty( $response ) && property_exists( $response, 'data' ) && is_array( $response->data ) ) {
			if ( ! isset( $response->data['success'] ) || ! $response->data['success'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Main validation function, this function try to get the Autentication
	 * headers and decoded.
	 *
	 * @param bool $output Whether to only return the payload or not.
	 *
	 * @return WP_REST_Response | Array Returns WP_REST_Response or token's $payload.
	 */
	public function validate_token( $output = true ) {
		/**
		 * Looking for the HTTP_AUTHORIZATION header, if not present just
		 * return the user.
		 */
		$auth = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? $_SERVER['HTTP_AUTHORIZATION'] : false;

		// Double check for different auth header string (server dependent).
		if ( ! $auth ) {
			$auth = isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
		}

		if ( ! $auth ) {
			$auth = isset( $_SERVER['code'] ) && $_SERVER['code'] == "mobile_api_valid_credential" ? true : false;
		}

		if ( ! $auth ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'mobile_api_no_auth_header',
					'message'    => $this->messages['mobile_api_no_auth_header'],
					'data'       => array(),
				)
			);
		}

		/**
		 * The HTTP_AUTHORIZATION is present, verify the format.
		 * If the format is wrong return the user.
		 */
		list($token) = sscanf( $auth, 'Bearer %s' );

		if ( ! $token ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'mobile_api_bad_auth_header',
					'message'    => $this->messages['mobile_api_bad_auth_header'],
					'data'       => array(),
				)
			);
		}

		// Get the Secret Key.
		$secret_key = defined( 'MOBILE_API_SECRET_KEY' ) ? MOBILE_API_SECRET_KEY : false;

		if ( ! $secret_key ) {
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'mobile_api_bad_config',
					'message'    => esc_html__( 'MOBILEAPIAUT is not configurated properly.', 'mobile-api' ),
					'data'       => array(),
				)
			);
		}

		// Try to decode the token.
		try {
			$alg     = $this->get_alg();
			$payload = MOBILEAPIAUT::decode( $token, $secret_key, array( $alg ) );

			// The Token is decoded now validate the iss.
			if ( $payload->iss !== $this->get_iss() ) {
				// The iss do not match, return error.
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'mobile_api_bad_iss',
						'message'    => esc_html__( 'The iss do not match with this server.', 'mobile-api' ),
						'data'       => array(),
					)
				);
			}

			// Check the user id existence in the token.
			if ( ! isset( $payload->data->user->id ) ) {
				// No user id in the token, abort!!
				return new WP_REST_Response(
					array(
						'success'    => false,
						'statusCode' => 403,
						'code'       => 'mobile_api_bad_request',
						'message'    => esc_html__( 'User ID not found in the token.', 'mobile-api' ),
						'data'       => array(),
					)
				);
			}

			// So far so good, check if the given user id exists in db.
			$user = get_user_by( 'id', $payload->data->user->id );

			if ( ! $user ) {
				// No user id in the token, abort!!
				// return new WP_REST_Response(
				// 	array(
				// 		'success'    => false,
				// 		'statusCode' => 403,
				// 		'code'       => 'mobile_api_user_not_found',
				// 		'message'    => esc_html__( "User doesn't exist", 'mobile-api' ),
				// 		'data'       => array(),
				// 	)
				// );
			}

			// Everything looks good return the token if $output is set to false.
			if ( ! $output ) {
				return $payload;
			}

			// Otherwise, return success response.
			$response = new WP_REST_Response(
				array(
					'success'    => true,
					'statusCode' => 200,
					'code'       => 'mobile_api_valid_token',
					'message'    => esc_html__( 'Token is valid', 'mobile-api' ),
					'data'       => array(),
				)
			);

			return apply_filters( 'mobile_api_auth_valid_token_response', $response, $user, $token, $payload );
		} catch ( Exception $e ) {
			// Something is wrong when trying to decode the token, return error response.
			return new WP_REST_Response(
				array(
					'success'    => false,
					'statusCode' => 403,
					'code'       => 'mobile_api_invalid_token',
					'message'    => $e->getMessage(),
					'data'       => array(),
				)
			);
		}
	}

	/**
	 * This is our Middleware to try to authenticate the user according to the token sent.
	 *
	 * @param int|bool $user_id User ID if one has been determined, false otherwise.
	 * @return int|bool User ID if one has been determined, false otherwise.
	 */
	public function determine_current_user( $user_id ) {
		/**
		 * This hook only should run on the REST API requests to determine
		 * if the user in the Token (if any) is valid, for any other
		 * normal call ex. wp-admin/.* return the user.
		 *
		 * @since 1.2.3
		 */
		$this->rest_api_slug = get_option( 'permalink_structure' ) ? rest_get_url_prefix() : '?rest_route=/';
		$this->base_api = mobile_api_base;

		$valid_api_uri = strpos( $_SERVER['REQUEST_URI'], $this->rest_api_slug );
		$valid_api_uri_2 = strpos( $_SERVER['REQUEST_URI'], $this->base_api );

		if ( ! $valid_api_uri && ! $valid_api_uri_2 ) {
			return $user_id;
		}

		/**
		 * If the request URI is for validate the token don't do anything,
		 * this avoid double calls to the validate_token function.
		 */
		$validate_uri = strpos( $_SERVER['REQUEST_URI'], 'token/validate' );
		$validate_uri_2 = strpos( $_SERVER['REQUEST_URI'], 'token/validate' );

		if ( $validate_uri > 0 || $validate_uri_2 > 0 ) {
			return $user_id;
		}

		$payload = $this->validate_token( false );

		// If $payload is an error response, then return the default $user_id.
		if ( $this->is_error_response( $payload ) ) {
			if ('mobile_api_no_auth_header' === $payload->data['code'] || 'mobile_api_bad_auth_header' === $payload->data['code']) {
				//
			} else {
				$this->mobile_api_error = $payload;
			}

			return $user_id;
		}

		// Everything is ok here, return the user ID stored in the token.
		return (isset($payload->data->user->id)?$payload->data->user->id:"");
	}

	/**
	 * Check whether or not current endpoint is whitelisted.
	 *
	 * @return bool
	 */
	public function is_whitelisted() {
		$whitelist = apply_filters( 'mobile_api_auth_whitelist', array() );

		if ( empty( $whitelist ) || ! is_array( $whitelist ) ) {
			return false;
		}

		$request_uri = $_SERVER['REQUEST_URI'];

		// Only use string before "?" sign if permalink is enabled.
		if ( get_option( 'permalink_structure' ) && false !== stripos( $request_uri, '?' ) ) {
			$split       = explode( '?', $request_uri );
			$request_uri = $split[0];
		}

		// Let's remove trailingslash for easier checking.
		$request_uri = untrailingslashit( $request_uri );

		foreach ( $whitelist as $endpoint ) {
			// If the endpoint doesn't contain * sign.
			if ( false === stripos( $endpoint, '*' ) ) {
				$endpoint = untrailingslashit( $endpoint );

				if ( $endpoint === $request_uri ) {
					return true;
				}
			} else {
				/**
				 * TODO: Maybe use regex to match glob-style pattern.
				 */
				$endpoint = str_ireplace( '*', '', $endpoint );
				$endpoint = untrailingslashit( $endpoint );

				if ( 0 === stripos( $request_uri, $endpoint ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Filter to hook the rest_pre_dispatch, if there is an error in the request
	 * send it, if there is no error just continue with the current request.
	 *
	 * @param mixed           $result Can be anything a normal endpoint can return, or null to not hijack the request.
	 * @param WP_REST_Server  $server Server instance.
	 * @param WP_REST_Request $request The request.
	 *
	 * @return mixed $result
	 */
	public function rest_pre_dispatch( $result, WP_REST_Server $server, WP_REST_Request $request ) {
		if ( $this->is_error_response( $this->mobile_api_error ) ) {
			return $this->mobile_api_error;
		}

		if ( empty( $result ) ) {
			return $result;
		}

		return $result;
	}
}?>