<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_notices', function() {
	if( ! function_exists( 'WPF' ) || ! version_compare( WPFORO_VERSION, WPFOROATTACH_WPFORO_REQUIRED_VERSION, '>=' ) ) {
		$class   = 'notice notice-error';
		$message = __( 'IMPORTANT: wpForo Attachments plugin is a wpForo extension, please install latest version wpForo plugin.', 'wpforoattach' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
} );

add_action( 'wpforo_core_inited', function() {
	if( version_compare( WPFORO_VERSION, WPFOROATTACH_WPFORO_REQUIRED_VERSION, '>=' ) && wpforo_is_module_enabled( WPFOROATTACH_FOLDER ) ) {
		if( wpforo_get_option( 'attach_version', null, false ) !== WPFOROATTACH_VERSION ) wpforoattach_activation();
		$GLOBALS['wpforoattach'] = WPF_ATTACH();
	}
} );

add_action( 'wpforo_admin_menu', function( $parent_slug ) {
    if( version_compare( WPFORO_VERSION, WPFOROATTACH_WPFORO_REQUIRED_VERSION, '>=' ) ){
	    if( wpforo_is_module_enabled( WPFOROATTACH_FOLDER ) ) {
		    add_submenu_page(
			    $parent_slug, __( 'Attachments', 'wpforo' ), __( 'Attachments', 'wpforo' ), 'activate_plugins', wpforo_prefix_slug( 'advanced-attachments' ), function() { WPF_ATTACH()->medialib_page(); }
		    );
	    }
    }
} );

function wpforoattach_activation() {
	global $wpdb;
	$charset_collate = '';
	if( ! empty( $wpdb->charset ) ) $charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
	if( ! empty( $wpdb->collate ) ) $charset_collate .= " COLLATE " . $wpdb->collate;

	$sql = "CREATE TABLE IF NOT EXISTS `" . WPF()->tables->attachments . "`(  
	  `attachid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `userid` INT UNSIGNED NOT NULL,
	  `slug` VARCHAR(32) NOT NULL DEFAULT '',
	  `filename` varchar(255) NOT NULL,
	  `fileurl` VARCHAR(255) NOT NULL,
	  `size` INT UNSIGNED NOT NULL DEFAULT 0,
	  `mime` varchar(255) NOT NULL,
	  `posts` INT UNSIGNED NOT NULL DEFAULT 0,
	  `created` INT UNSIGNED NOT NULL DEFAULT 0,
	  PRIMARY KEY (`attachid`),
	  KEY `userid_created` (`userid`, `created`)
	)ENGINE=InnoDB $charset_collate";

	if( false === @$wpdb->query( $sql ) ) {
		@$wpdb->query( preg_replace( '#\)\s*ENGINE.*$#isu', ')', $sql ) );
	}

	if( ! wpforo_db_check( [ 'table' => WPF()->tables->attachments, 'col' => 'posts', 'check' => 'col_exists' ] ) ) {
		$sql = "ALTER TABLE `" . WPF()->tables->attachments . "` ADD COLUMN `posts` INT UNSIGNED NOT NULL DEFAULT 0";
		@$wpdb->query( $sql );
	}

	if( ! wpforo_db_check( [ 'table' => WPF()->tables->attachments, 'col' => 'slug', 'check' => 'col_exists' ] ) ) {
		$sql = "ALTER TABLE `" . WPF()->tables->attachments . "` ADD `slug` VARCHAR(32) AFTER `userid`, ADD INDEX `slug`(`slug`)";
		@$wpdb->query( $sql );
	}

	$sql = "UPDATE `" . WPF()->tables->attachments . "` 
        SET `slug` = MD5( CONCAT(`attachid`, UNIX_TIMESTAMP()) )
        WHERE `slug` = '' OR `slug` IS NULL";
	@$wpdb->query( $sql );

	if( ! wpforo_db_check( [ 'table' => WPF()->tables->attachments, 'col' => 'created', 'check' => 'col_exists' ] ) ) {
		$sql = "ALTER TABLE `" . WPF()->tables->attachments . "` ADD `created` INT UNSIGNED DEFAULT 0, ADD INDEX `userid_created`(`userid`, `created`)";
		@$wpdb->query( $sql );
	}

	$attachs_upload_dir = WPF()->folders['attachments']['dir'];
	if( ! is_dir( $attachs_upload_dir ) ) wp_mkdir_p( $attachs_upload_dir );

	$options = wpforo_get_option( 'attach_options', [], false );
	if( ! empty( $options['maximum_file_size'] ) && $options['maximum_file_size'] < 1024 ) {
		$min                          = WPF()->settings->_SERVER['maxs_min'];
		$new_val                      = $options['maximum_file_size'] * 1024 * 1024;
		$options['maximum_file_size'] = min( $min, $new_val );
		wpforo_update_option( 'attach_options', $options );
	}

	wpforo_update_option( 'attach_version', WPFOROATTACH_VERSION );
}

function wpforo_attach_phrase( $phrase_key, $echo = true ) {
	$phrase_key = strtolower( $phrase_key );
	if( isset( WPF_ATTACH()->phrases[ $phrase_key ] ) ) {
		$phrase = stripslashes( WPF_ATTACH()->phrases[ $phrase_key ] );
	} else {
		$phrase = ucfirst( str_replace( '_', ' ', $phrase_key ) );
	}
	if( $echo ) echo $phrase; else return $phrase;
}

function wpforo_attach_server_extension_status() {
	?>
    <tr class="wpf-dw-tr">
        <td class="wpf-dw-td">GD and Image Functions</td>
        <td class="wpf-dw-td-value"><?php echo function_exists( 'getimagesize' ) && function_exists( 'imagecreatetruecolor' ) && function_exists( 'exif_read_data' ) ? '<span class="wpf-green">' . __( 'Available', 'wpforo' ) . '</span>' : '<span class="wpf-red">' . __( 'Not available', 'wpforo' ) . '</span> | <a href="http://php.net/manual/en/ref.image.php" target="_blank">more info&raquo;</a>'; ?> </td>
    </tr>
    <tr class="wpf-dw-tr">
        <td class="wpf-dw-td">PHP imagick extension</td>
        <td class="wpf-dw-td-value"><?php echo extension_loaded( 'imagick' ) ? '<span class="wpf-green">' . __( 'Available', 'wpforo' ) . '</span>' : '<span class="wpf-red">' . __( 'Not available', 'wpforo' ) . '</span> ( <span style="font-size: smaller; color: gray;">' . __( 'Not required if "GD and Image Functions" are Available', 'wpforo_attach' ) . '</span> ) | <a href="http://php.net/manual/en/imagick.installation.php" target="_blank">more info&raquo;</a>'; ?></td>
    </tr>
	<?php
}

add_action( 'wpforo_dashboard_widget_server', 'wpforo_attach_server_extension_status' );

/**
 * @return bool
 */
function is_wpforo_attach_page() {
	$templates = [
		'post',
		'topic',
		'forum',
	];
	$templates = apply_filters( 'is_wpforo_attach_page_templates', $templates );

	if( in_array( WPF()->current_object['template'], $templates ) ) return true;

	return false;
}

function is_daily_limit_exceeded( $userid = null ) {
	if( is_null( $userid ) ) $userid = WPF()->current_userid;
	$uploads_per_day     = WPF_ATTACH()->get_user_attach_count_per_day( $userid );
	$max_uploads_per_day = WPF_ATTACH()->options['max_uploads_per_day'];

	return ! ( ! $max_uploads_per_day || $max_uploads_per_day > $uploads_per_day );
}

function wpforo_attach_can_attach( $forumid = null, $userid = null ) {
	if( is_null( $userid ) ) $userid = WPF()->current_userid;

	return ! is_daily_limit_exceeded( $userid ) && WPF()->perm->can_attach( $forumid );
}

add_filter( 'wpforo_init_tables', function( $tables ) {
	$tables[] = 'attachments';

	return $tables;
} );

add_filter( 'wpforo_init_folders', function( $folders ) {
	if( ! in_array( 'attachments', $folders, true ) ) $folders[] = 'attachments';

	return $folders;
} );
