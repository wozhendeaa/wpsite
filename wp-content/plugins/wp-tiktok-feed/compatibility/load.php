<?php

add_filter(
	'option_tiktok_feed_feeds',
	function( $feeds ) {

		foreach ( $feeds as $i => $feed ) {
			$is_old = isset( $feed['hashtag_id'] );

			if ( ! $is_old ) {
				continue;
			}

			if ( isset( $feed['mask']['digg_count'] ) ) {
				$feeds[ $i ]['mask']['likes_count'] = $feed['mask']['digg_count'];
			}

			if ( isset( $feed['mask']['comment_count'] ) ) {
				$feeds[ $i ]['mask']['comments_count'] = $feed['mask']['comment_count'];
			}

			if ( isset( $feed['popup'] ) ) {
				$feeds[ $i ]['modal'] = $feed['popup'];
			}

			if ( $feed['source'] == 'username' ) {
				$feeds[ $i ]['source'] = 'account';
			}
		}
		return $feeds;
	}
);

add_action(
	'init',
	function() {

		if ( ! is_admin() ) {
			return;
		}

		$old_menus = array( 'qlttf', 'qlttf_account', 'qlttf_feeds', 'qlttf_setting' );

		if ( ! isset( $_GET['page'] ) || ! in_array( $_GET['page'], $old_menus ) ) {
			return;
		}

		switch ( $_GET['page'] ) {
			case 'qlttf':
				wp_safe_redirect( admin_url( 'admin.php?page=qlttf_backend' ) );
				exit;
			case 'qlttf_account':
				wp_safe_redirect( admin_url( 'admin.php?page=qlttf_backend&tab=accounts' ) );
			case 'qlttf_feeds':
				wp_safe_redirect( admin_url( 'admin.php?page=qlttf_backend&tab=feeds' ) );
				exit;
			case 'qlttf_setting':
				wp_safe_redirect( admin_url( 'admin.php?page=qlttf_backend&tab=settings' ) );
				exit;
		}

	}
);

function qlttf_thousands_roud( $num ) {
	if ( $num > 1000 ) {

		$x               = round( $num );
		$x_number_format = number_format( $x );
		$x_array         = explode( ',', $x_number_format );
		$x_parts         = array( 'k', 'm', 'b', 't' );
		$x_count_parts   = count( $x_array ) - 1;
		$x_display       = $x;
		$x_display       = $x_array[0] . ( (int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '' );
		$x_display      .= $x_parts[ $x_count_parts - 1 ];

		return $x_display;
	}

	return $num;
}


if ( ! class_exists( 'QLTTF_Frontend' ) ) {

	class QLTTF_Frontend {

		static function template_path( $template_name, $template_file = false ) {

			if ( file_exists( QLTTF_PLUGIN_DIR . "templates/{$template_name}" ) ) {
				$template_file = QLTTF_PLUGIN_DIR . "templates/{$template_name}";
			}

			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . "tiktok-feed/{$template_name}" ) ) {
				$template_file = trailingslashit( get_stylesheet_directory() ) . "tiktok-feed/{$template_name}";
			}

			return apply_filters( 'qlttf_template_file', $template_file, $template_name );
		}
	}
}
