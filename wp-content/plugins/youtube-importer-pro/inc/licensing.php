<?php

define( 'YTI_PRO_VERSION', '1.0.2' );
define( 'YTI_PRO_AUTHOR', 'SecondLineThemes' );

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'YTI_PRO_STORE_URL', 'https://secondlinethemes.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the download ID for the product in Easy Digital Downloads
define( 'YTI_PRO_ITEM_ID', 54226 ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the product in Easy Digital Downloads
define( 'YTI_PRO_ITEM_NAME', 'YouTube Importer Pro' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'YTI_SLT_Plugin_Updater' ) )
  require_once "lib/YTI_SLT_Plugin_Updater.php";

add_action( "admin_init", function() {
  if( !defined( "YOUTUBE_IMPORTER_SECONDLINE_ALIAS" ) )
    return;

  if( isset( $_POST[ 'yti_pro_nonce' ] ) && wp_verify_nonce( $_POST[ 'yti_pro_nonce' ], 'license_validation' ) ) {
    if( isset( $_POST[ 'yti_pro_license_deactivate' ] ) ) {
      _yti_pro_deactivate_license();
    } else if( isset( $_POST[ 'yti_pro_license_activate' ] ) ) {
      $old = get_option( 'yti_pro_license_key' );

      if ( $old && $old !== $_POST[ 'yti_pro_license_key' ] ) {
        delete_option( 'yti_pro_license_status' ); // new license has been entered, so must reactivate
        update_option( 'yti_pro_license_key', sanitize_text_field( $_POST[ 'yti_pro_license_key' ] ) );
      }

      _yti_pro_activate_license();
    }
  }

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_tools_tabs', function( $tabs ) {
    $license = get_option( 'yti_pro_license_key' );
    $status  = get_option( 'yti_pro_license_status' );

    $content = '';

    $content .= '<br><h3>Enter and activate your license below to receive automatic updates</h3><form id="licensing-form" method="POST">';
    $content .= '<input type="hidden" name="yti_pro_nonce" value="' . esc_attr( wp_create_nonce( 'license_validation' ) ) . '" />';
    $content .= '<input type="text" class="regular-text" id="yti_pro_license_key" name="yti_pro_license_key" value="' . esc_attr( $license ) . '" />';

    if( $status === 'valid' )
      $content .= '<input type="submit" class="button button-secondary" name="yti_pro_license_deactivate" value="' . __( 'Deactivate License' ) . '"/>';
    else
      $content .= '<input type="submit" class="button button-secondary" name="yti_pro_license_activate" value="' . __( 'Activate License' ) . '"/>';

    $content .= '</form>';

    if ( isset( $_GET['sl_activation'] ) && $_GET['sl_activation'] === 'false' && ! empty( $_GET['message'] ) ) {
      $content .= '<div data-secondline-import-notification="danger">' . wp_kses_post( urldecode( $_GET['message'] ) ) . '</div>';
    } else if( $status === 'valid' ) {
      $content .= '<div data-secondline-import-notification="success">' . __( 'Valid License' ) . '</div>';
    }		

    $tabs[ 'license' ] = [
      'title'   => __( "License Key", 'youtube-importer-secondline-pro' ),
      'content' => $content
    ];

    return $tabs;
  });
});

/**
 * Initialize the updater. Hooked into `init` to work with the
 * wp_version_check cron job, which allows auto-updates.
 */
function yti_pro_sl_plugin_updater() {

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'yti_pro_license_key' ) );

	// setup the updater
	$edd_updater = new YTI_SLT_Plugin_Updater(
		YTI_PRO_STORE_URL,
		YTI_PRO_STORE_URL_PLUGIN_FILE,
		array(
			'version' => YTI_PRO_VERSION, // current version number
			'license' => $license_key,    // license key (used get_option above to retrieve from DB)
			'item_id' => YTI_PRO_ITEM_ID, // ID of the product
			'author'  => YTI_PRO_AUTHOR, 	// author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'yti_pro_sl_plugin_updater' );

/**
 * Activates the license key.
 *
 * @return void
 */
function _yti_pro_activate_license() {

	// retrieve the license from the database
	$license = trim( get_option( 'yti_pro_license_key' ) );
	if ( ! $license ) {
		$license = filter_input( INPUT_POST, 'yti_pro_license_key', FILTER_SANITIZE_STRING );
	}
	if ( ! $license ) {
		return;
	}

	// data to send in our API request
	$api_params = array(
		'edd_action'  => 'activate_license',
		'license'     => $license,
		'item_id'     => YTI_PRO_ITEM_ID,
		'item_name'   => rawurlencode( YTI_PRO_ITEM_NAME ), // the name of our product in EDD
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		YTI_PRO_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

		// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' );
		}
	} else {

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {

			switch ( $license_data->error ) {

				case 'expired':
					$message = sprintf(
						/* translators: the license key expiration date */
						__( 'Your license key expired on %s.', 'youtube-importer-secondline-pro' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled':
				case 'revoked':
					$message = __( 'Your license key has been disabled.', 'youtube-importer-secondline-pro' );
					break;

				case 'missing':
					$message = __( 'Invalid license.', 'youtube-importer-secondline-pro' );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'youtube-importer-secondline-pro' );
					break;

				case 'item_name_mismatch':
					/* translators: the plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'youtube-importer-secondline-pro' ), YTI_PRO_ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'youtube-importer-secondline-pro' );
					break;

				default:
					$message = __( 'An error occurred, please try again.', 'youtube-importer-secondline-pro' );
					break;
			}
		}
	}

		// Check if anything passed on a message constituting a failure
	if ( ! empty( $message ) ) {
		$redirect = add_query_arg(
			array(
				'sl_activation' => 'false',
				'message'       => rawurlencode( $message ),
			),
      admin_url( 'tools.php?page=' . YOUTUBE_IMPORTER_SECONDLINE_PREFIX . '&tab=license' )
		);

		wp_safe_redirect( $redirect );
		exit();
	}

	// $license_data->license will be either "valid" or "invalid"
	if ( 'valid' === $license_data->license ) {
		update_option( 'yti_pro_license_key', $license );
	}
	update_option( 'yti_pro_license_status', $license_data->license );
  wp_safe_redirect( admin_url( 'tools.php?page=' . YOUTUBE_IMPORTER_SECONDLINE_PREFIX . '&tab=license' ) );
	exit();
}

function _yti_pro_deactivate_license() {
  // retrieve the license from the database
  $license = trim( get_option( 'yti_pro_license_key' ) );

  // data to send in our API request
  $api_params = array(
    'edd_action'  => 'deactivate_license',
    'license'     => $license,
    'item_id'     => YTI_PRO_ITEM_ID,
    'item_name'   => rawurlencode( YTI_PRO_ITEM_NAME ), // the name of our product in EDD
    'url'         => home_url(),
    'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
  );

  // Call the custom API.
  $response = wp_remote_post(
    YTI_PRO_STORE_URL,
    array(
      'timeout'   => 15,
      'sslverify' => false,
      'body'      => $api_params,
    )
  );

  // make sure the response came back okay
  if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

    if ( is_wp_error( $response ) ) {
      $message = $response->get_error_message();
    } else {
      $message = __( 'An error occurred, please try again.' );
    }

    $redirect = add_query_arg(
        [
        'sl_activation' => 'false',
        'message'       => rawurlencode( $message ),
      ],
      admin_url( 'tools.php?page=' . YOUTUBE_IMPORTER_SECONDLINE_PREFIX . '&tab=license' )
    );

    wp_safe_redirect( $redirect );
    exit();
  }

  // decode the license data
  $license_data = json_decode( wp_remote_retrieve_body( $response ) );

  // $license_data->license will be either "deactivated" or "failed"
  if ( 'deactivated' === $license_data->license ) {
    delete_option( 'yti_pro_license_status' );
  }

  wp_safe_redirect( admin_url( 'tools.php?page=' . YOUTUBE_IMPORTER_SECONDLINE_PREFIX . '&tab=license' ) );
  exit();
}