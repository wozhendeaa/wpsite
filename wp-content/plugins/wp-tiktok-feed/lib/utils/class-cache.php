<?php

namespace QuadLayers\TTF\Utils;

/**
 * QuadLayers Cache
 * Version: 1.0.0
 * Date: 07/09/2022
 */

 /**
  * Cache Class
  */
class Cache {

	private static $disable_cache_test            = false;
	private static $force_expiration_test         = false;
	private static $prefix                        = 'qlttf_cache_';
	private static $min_expiration_lapse_in_hours = 1;
	private static $autoexpires;
	private static $expiration_lapse_in_hours;
	private static $current_time_timestamp;
	private $dynamic_prefix = '';

	/**
	 * Class constructor
	 *
	 * @param int $expiration_time
	 */
	public function __construct( int $expiration_lapse_in_hours, $autoexpires, $add_prefix ) {

		$this->dynamic_prefix = $add_prefix;

		self::$expiration_lapse_in_hours = max( self::$min_expiration_lapse_in_hours, absint( $expiration_lapse_in_hours ) );

		self::$autoexpires            = $autoexpires;
		self::$current_time_timestamp = current_time( 'timestamp' );
	}

	public function get_prefix() {

		return static::$prefix . $this->dynamic_prefix;
	}

	/**
	 * Get the expiration date
	 *
	 * @param int $date
	 * @return void
	 */
	public function get_cache_expiration_timestamp( int $cache_timestamp ) {
		if ( self::$force_expiration_test ) {
			return 0;
		}
		return $cache_timestamp + self::$min_expiration_lapse_in_hours * HOUR_IN_SECONDS;
	}

	/**
	 * Return true if a date is expired, false if not
	 *
	 * @param int $date
	 * @return boolean
	 */
	public function is_cache_expired( int $cache_timestamp ) {
		// retorna true si se vencio, false si no se vencio
		/**
		 * Conditional to hardcode function return true
		 */
		if ( ! self::$disable_cache_test ) {
			return self::$current_time_timestamp > $this->get_cache_expiration_timestamp( $cache_timestamp ); // Funcion
		}
		return true;
	}

	/**
	 * Get the url key to access to database
	 *
	 * @param string $str
	 * @return void
	 */
	public function get_db_url_key( string $url ) {
		return $this->get_prefix() . '_' . md5( $url );
	}

	/**
	 * Get option from database
	 *
	 * @param string $url
	 * @return void
	 */
	public function get( $url ) {

		$cache_option_key = $this->get_db_url_key( $url );

		$cache = '';

		if ( static::$autoexpires ) {
			$cache = get_transient( $cache_option_key );
		} else {
			$cache = get_option( $cache_option_key, false );
		}

		if ( ! isset( $cache['timestamp'] ) || $this->is_cache_expired( $cache['timestamp'] ) ) {
			return array();
		}

		return $cache;
	}

	/**
	 * Update option in database
	 *
	 * @param string $url
	 * @param array  $response
	 * @return void
	 */
	public function update( $url, $response, $custom_expiration_lapse_time = null ) {

		$cache_option_key = $this->get_db_url_key( $url );

		$cache = array(
			'response'  => $response,
			'timestamp' => current_time( 'timestamp' ),
		);

		if ( static::$autoexpires ) {
			if ( ! $custom_expiration_lapse_time ) {
				set_transient( $cache_option_key, $cache, static::$expiration_lapse_in_hours * 3600 );
			} else {

				set_transient( $cache_option_key, $cache, $custom_expiration_lapse_time );
			}
		} else {
			update_option( $cache_option_key, $cache );
		}
	}

	/**
	 * Delete option from database
	 *
	 * @param string $url
	 * @return void
	 */
	public function delete_key( $url ) {
		$cache_option_key = $this->get_db_url_key( $url );

		if ( static::$autoexpires ) {
			delete_transient( $cache_option_key );
		} else {
			delete_option( $cache_option_key );
		}

	}

	public function delete() {
		global $wpdb;

		$search_prefix = '%' . $this->get_prefix() . '%';

		$tks = $wpdb->get_results( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s", $search_prefix ) );

		if ( $tks ) {
			foreach ( $tks as $key => $name ) {
				if ( static::$autoexpires ) {
					delete_transient( str_replace( '_transient_', '', $name->option_name ) );
				} else {
					delete_option( $name->option_name );
				}
			}
		}
	}
}
