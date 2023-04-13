<?php

namespace QUADLAYERS\TIKTOK;

final class Load {

	protected static $_instance;

	public function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
		load_plugin_textdomain( 'wp-tiktok-feed', false, QLTTF_PLUGIN_DIR . '/languages/' );
		// Utils\Dashboard\Widget::instance();
		Utils\Dashboard\Links::instance();
		Utils\Dashboard\Notices::instance();
		Api\Rest\Routes::instance();
		Backend\Load::instance();
		Frontend\Load::instance();
		do_action( 'qlttf_init' );
	}

	public function autoload( $class_to_load ) {

		if ( 0 !== strpos( $class_to_load, __NAMESPACE__ ) ) {
			return;
		}

		if ( ! class_exists( $class_to_load ) ) {

			$class_file = $this->class_file( $class_to_load );

			if ( is_readable( $class_file ) ) {
				include_once $class_file;
			} else {
				if ( defined( 'QLTTF_DEVELOPER' ) && QLTTF_DEVELOPER ) {
					$warning_message = sprintf( __( '"Can\'t find %1$s" for "%2$s" in "%3$s".', 'wp-tiktok-feed' ), $class_file, $class_to_load, __NAMESPACE__ );
					error_log( $warning_message, E_USER_NOTICE );
				}
			}
		}
	}

	public static function class_file( $class_name ) {

		$class_name = str_replace( __NAMESPACE__, '', $class_name );
		$filename   = strtolower( preg_replace( array( '/([a-z])([A-Z])/', '/_/', '/\\\/' ), array( '$1-$2', '-', DIRECTORY_SEPARATOR ), $class_name ) );

		if ( strpos( $filename, 'build' ) !== false ) {
			return QLTTF_PLUGIN_DIR . $filename . '.php';
		}
		return QLTTF_PLUGIN_DIR . 'lib/' . $filename . '.php';
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

function INIT() {
	return Load::instance();
}

INIT();
