<?php

namespace QUADLAYERS\TIKTOK\Models;

use QUADLAYERS\TIKTOK\Models\Base as Model;

/**
 * Models_Account Class
 */
class Accounts extends Model {

	/**
	 * Table - Table string name.
	 *
	 * @var string
	 */
	protected $table = 'tiktok_feed_accounts';

	/**
	 * Set the max attemps to renew access_token to prevents API abuse.
	 *
	 * @var integer
	 */
	protected static $access_token_max_renew_attemps = 3;

	/**
	 * Function get_args() - return defaults args.
	 *
	 * @return object
	 */
	protected function get_args() {
		return array(
			'open_id'                       => '', // Unique public user id.
			'access_token'                  => '', // Private user access_token.
			'access_token_expiration_date'  => 0,  // Date when access_token expires.
			'access_token_renew_atemps'     => 0,  // Count attemps to renew access_token.
			'refresh_token'                 => '', // Private user refresh_token to update access_token.
			'refresh_token_expiration_date' => 0,  // Date when refresh_token expires.
		);
	}

	/**
	 * Get account by id
	 *
	 * @param [type] $id
	 * @return $account
	 */
	public function get_account( $id ) {

		$accounts = $this->get();

		if ( ! isset( $accounts[ $id ] ) ) {
			return false;
		}

		if ( ! isset( $accounts[ $id ]['access_token_expiration_date'] ) ) {
			return $accounts[ $id ];
		}

		$is_access_token_expired = $this->is_access_token_expired( $accounts [ $id ] );

		if ( ! $is_access_token_expired ) {
			return $accounts[ $id ];
		}

		/**
		 * If access_token is renewed return updated account
		 */
		if ( $this->validate_access_token( $accounts[ $id ] ) ) {
			$accounts = $this->get();
		}

		return $accounts[ $id ];
	}

	/**
	 * Function: renew_access_token():
	 * renew all user data querying from tiktok
	 *
	 * @param string $refresh_token - user refresh_token.
	 * @return object
	 */
	protected function renew_access_token( $refresh_token ) {

		$args = array(
			'refresh_token' => $refresh_token,
		);

		$new_user_data = wp_remote_post(
			'https://tiktokfeed.quadlayers.com/refreshToken',
			array(
				'method'  => 'POST',
				'timeout' => 45,
				'body'    => json_encode( $args ),
			)
		);
		$new_user_data = json_decode( $new_user_data['body'] );

		return $new_user_data;
	}

	protected function access_token_renew_attemps_increase( $account ) {
		$account['access_token_renew_atemps'] = intval( $account['access_token_renew_atemps'] ) + 1;
		$this->update_account( $account );
	}

	public static function access_token_renew_attemps_exceded( $account ) {
		if ( intval( $account['access_token_renew_atemps'] ) > self::$access_token_max_renew_attemps ) {
			return true;
		}
		return false;
	}

	protected function validate_access_token( $account ) {

		/**
		 * Checks if $account has already reached maximum attempts possible.
		 */
		if ( self::access_token_renew_attemps_exceded( $account ) ) {
			return false;
		}

		$response = $this->renew_access_token( $account['refresh_token'] );

		/**
		 *  Checks if $response has setted error, access_token_expires_in and access_token.
		 */

		if ( isset( $response->error ) || ! isset( $response->access_token_expires_in ) || ! isset( $response->access_token ) ) {
			$this->access_token_renew_attemps_increase( $account );
			return false;
		}

		// Checks if $account['access_token'] has expired.
		if ( $account['access_token_expiration_date'] >= $this->calculate_expiration_date( $response->access_token_expires_in ) ) {
			return false;
		}

		$account['access_token_renew_atemps']     = 0;
		$account['access_token']                  = $response->access_token;
		$account['refresh_token']                 = $response->refresh_token;
		$account['access_token_expiration_date']  = $this->calculate_expiration_date( $response->access_token_expires_in );
		$account['refresh_token_expiration_date'] = $this->calculate_expiration_date( $response->refresh_token_expires_in );
		$account                                  = $this->update_account( $account );

		if ( $account ) {
			return $account;
		}

		return false;
	}

	protected function is_access_token_expired( $account ) {
		if ( $account['access_token_expiration_date'] - strtotime( current_time( 'mysql' ) ) < 0 ) {
			return true;
		}
		return false;
	}

	public function force_refresh_access_token( $account_id ) {
		$accounts = $this->get();

		$account_to_refresh_access_token = $accounts[ $account_id ];

		$new_account_data = $this->renew_access_token( $account_to_refresh_access_token['refresh_token'] );

		$account_to_refresh_access_token['open_id']                       = $new_account_data->open_id;
		$account_to_refresh_access_token['access_token']                  = $new_account_data->access_token;
		$account_to_refresh_access_token['refresh_token']                 = $new_account_data->refresh_token;
		$account_to_refresh_access_token['access_token_renew_atemps']     = 0;
		$account_to_refresh_access_token['access_token_expiration_date']  = $this->calculate_expiration_date( $new_account_data->access_token_expires_in );
		$account_to_refresh_access_token['refresh_token_expiration_date'] = $this->calculate_expiration_date( $new_account_data->refresh_token_expires_in );

		return $this->update_account( $account_to_refresh_access_token );
	}

	public function get() {
		$accounts = $this->get_all();
		/**
		 * Make sure each account has all values.
		 */
		if ( count( $accounts ) ) {
			foreach ( $accounts as $id => $account ) {
				$accounts[ $id ] = array_replace_recursive( $this->get_args(), $accounts[ $id ] );
			}
		}
		return $accounts;
	}

	protected function update_account( $account_data ) {
		return $this->save_account( $account_data );
	}

	public function update( $accounts ) {
		return $this->save_all( $accounts );
	}

	public function add_account( $account_data ) {

		/**
		 * Check if account_data dosent exist and return error.
		 */
		if ( empty( $account_data ) ) {
			return array(
				'error'   => 404,
				'message' => esc_html__( 'Account data is empty/null', 'wp-tiktok-feed' ),
			);
		}

		/**
		 * Check if refresh_token dosent exist and return error.
		 */
		if ( empty( $account_data['refresh_token'] ) ) {
			return array(
				'error'   => 404,
				'message' => esc_html__( 'refresh_token is empty/null', 'wp-tiktok-feed' ),
			);
		}

		/**
		 * Check if all attributes exist and return a new account.
		 */
		if ( isset( $account_data['open_id'] ) &&
		isset( $account_data['access_token'] ) &&
		isset( $account_data['access_token_expires_in'] ) &&
		isset( $account_data['refresh_token'] ) &&
		isset( $account_data['refresh_token_expires_in'] ) &&
		isset( $account_data['access_token_expiration_date'] ) &&
		isset( $account_data['refresh_token_expiration_date'] )
		) {
			return $this->save_account( $account_data );
		}

		$response = $this->renew_access_token( $account_data['refresh_token'] );

		if ( ! empty( $response->message ) ) {
			/**
			 * Check if refresh_token is not valid return error.
			 */
			return array(
				'error'   => 404,
				'message' => $response->data->description,
			);
		}

		/**
		 * Check if is a valid response or return error.
		*/
		if ( ! isset( $response->open_id, $response->access_token, $response->refresh_token, $response->access_token_expires_in, $response->refresh_token_expires_in ) ) {
			return array(
				'error'   => 404,
				'message' => esc_html__( 'Unknown error.', 'wp-tiktok-feed' ),
			);
		}

		$account_data['open_id']                       = $response->open_id;
		$account_data['access_token']                  = $response->access_token;
		$account_data['refresh_token']                 = $response->refresh_token;
		$account_data['access_token_renew_atemps']     = 0;
		$account_data['access_token_expiration_date']  = $this->calculate_expiration_date( $response->access_token_expires_in );
		$account_data['refresh_token_expiration_date'] = $this->calculate_expiration_date( $response->refresh_token_expires_in );
		$account_data['access_token_expires_in']       = $response->access_token_expires_in;
		$account_data['refresh_token_expires_in']      = $response->refresh_token_expires_in;

		return $this->save_account( $account_data );
	}

	protected function save_account( $account_data = null ) {

		if ( $account_data['open_id'] ) {
			/**
			 * Make sure the account has all values.
			 */
			$account_data                         = array_intersect_key( $account_data, $this->get_args() );
			$accounts                             = $this->get();
			$accounts[ $account_data['open_id'] ] = array_replace_recursive( $this->get_args(), $account_data );

			$success = $this->save_all( $accounts );

			if ( $success ) {
				return $account_data;
			}

			return false;
		}
	}

	public function delete_account( $open_id = null ) {
		$accounts = $this->get_all();
		if ( $accounts ) {
			if ( count( $accounts ) > 0 ) {
				unset( $accounts[ $open_id ] );
				$success = $this->save_all( $accounts );
				if ( $success ) {
					return $success;
				}
			}
		}
		return false;
	}

	public function calculate_expiration_date( $expires_in ) {
		return strtotime( current_time( 'mysql' ) ) + $expires_in - 1;
	}

	public function delete_table() {
		$this->delete_all();
	}
}
