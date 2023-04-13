<?php
/*
* Plugin Name: wpForo Tenor
* Plugin URI: http://wpforo.com
* Description: Allows to embed Gifs from tenor.com.
* Author: gVectors Team
* Author URI: http://gvectors.com/
* Version: 3.0.0
* Text Domain: wpforo_tenor
* Domain Path: /languages
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
if( ! defined( 'WPFOROTENOR_VERSION' ) ) define( 'WPFOROTENOR_VERSION', '3.0.0' );
if( ! defined( 'WPFOROTENOR_WPFORO_REQUIRED_VERSION' ) ) define( 'WPFOROTENOR_WPFORO_REQUIRED_VERSION', '2.0.0' );

define( 'WPFOROTENOR_DIR', rtrim( str_replace( '//', '/', dirname( __FILE__ ) ), '/' ) );
define( 'WPFOROTENOR_URL', rtrim( plugins_url( '', __FILE__ ), '/' ) );
define( 'WPFOROTENOR_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPFOROTENOR_FOLDER', rtrim( plugin_basename( dirname( __FILE__ ) ), '/' ) );

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'wpforo_tenor', false, basename( dirname( __FILE__ ) ) . '/languages/' );
} );

require_once WPFOROTENOR_DIR . "/includes/gvt-api-manager.php";
new GVT_API_Manager( __FILE__, 'wpforo-settings&wpf_tab=wpforo-tenor', 'wpforo_settings_page_top' );

if( ! class_exists( 'wpForoTenor' ) ) {
	class wpForoTenor {
		private static $_instance = null;

		public $default;
		public $options = [];

		public static function instance() {
			if( is_null( self::$_instance ) ) self::$_instance = new self();

			return self::$_instance;
		}

		private function __construct() {
			$this->init_defaults();
			$this->init_hooks();
		}

		private function init_hooks() {
			add_action( 'wpforo_after_init', [ $this, 'init_options' ] );
			add_action( 'wpforo_action_wpftenor_settings_save', [ $this, 'settings_save' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'css_js_enqueue' ] );
			add_filter( 'wpforo_dynamic_css_filter', [ $this, 'add_dynamic_css' ] );

			add_filter( 'wpforo_settings_init_addons_info', [ $this, 'init_settings_info' ] );

			add_filter( "wpforo_editor_settings", [ $this, 'add_custom_tag_to_editor' ] );
			add_filter( "wpforo_kses_allowed_html", [ $this, 'kses_allowed_html' ] );

			add_action( 'wpforo_topic_form_extra_fields_before', [ $this, 'add_to_form' ], 9 );
			add_action( 'wpforo_reply_form_extra_fields_before', [ $this, 'add_to_form' ], 9 );
			add_action( 'wpforo_portable_form_extra_fields_before', [ $this, 'add_to_form' ], 9 );
			add_action( 'wpforopm_form_bottom', [ $this, 'add_to_form' ], 7 );

			add_filter( 'wpforo_strip_shortcodes', [ $this, 'strip_shortcodes' ] );

			add_filter( 'wpforo_add_topic_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_edit_topic_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_add_post_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_edit_post_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_quote_post_ajax', [ $this, 'filter_post_body_on_ajax_edit' ] );
			add_filter( 'wpforo_edit_post_ajax', [ $this, 'filter_post_body_on_ajax_edit' ] );
			add_filter( 'wpforopm_add_pm_data_filter', [ $this, 'filter_pm_body' ] );

			add_filter( 'wpforo_body_text_filter', [ $this, 'do_shortcode' ] );
			add_filter( 'bp_get_activity_content_body', function( $text ) {
				return $this->do_shortcode( $text, false );
			},          1 );
		}

		private function init_defaults() {
			$this->default = new \stdClass;

			$this->default->options = [
				'key'           => '6SCHYYXO2GZV',
				'cats'          => [],
				'limit'         => 25,
				'contentfilter' => 'off',
				'locale'        => '',
				'anon_id'       => '',
			];
		}

		public function init_options() {
			$this->options            = wpforo_get_option( 'tenor_options', $this->default->options );
			$this->options['anon_id'] = $this->generate_anon_id();
		}

		public function init_settings_info( $addons ) {
			$addons['wpforo-tenor'] = [
				"title"                => esc_html__( "Tenor GIFs Integration", "wpforo" ),
				"title_original"       => "Tenor GIFs Integration",
				"icon"                 => '<img src="' . WPFORO_URL . '/assets/addons/tenor/header.png' . '" alt="Tenor GIFs Integration Logo">',
				"description"          => __( 'Adds Tenor [GIF] button and opens popup where you can search for gifs and insert them in topic, post and private message content.', 'wpforo' ),
				"description_original" => "Adds Tenor [GIF] button and opens popup where you can search for gifs and insert them in topic, post and private message content.",
				"docurl"               => "",
				"status"               => "ok",
				"base"                 => false,
				"callback_for_page"    => function() {
					require WPFOROTENOR_DIR . "/includes/options.php";
				},
				"options"              => [

				],
			];

			return $addons;
		}

		public function settings_save() {
			check_admin_referer( 'wpforo_settings_save_wpforo-tenor' );

			if( ! current_user_can( 'administrator' ) ) {
				WPF()->notice->add( 'Permission denied', 'error' );
				wp_safe_redirect( admin_url() );
				exit();
			}
			if( ! wpfkey( $_POST, 'reset' ) ) {
				if( $options = wpfval( $_POST, 'wpforo_tenor_options' ) ) {
					$options['limit'] = (int) wpfval( $options, 'limit' );
					if( $options['limit'] < 10 ) $options['limit'] = 10;

					$cats = [];
					if( $options['cats'] = trim( $options['cats'] ) ) {
						if( preg_match_all( '#^(\s?)(\S.+)$#mu', $options['cats'], $matches, PREG_SET_ORDER ) ) {
							foreach( $matches as $k => $match ) {
								if( ! $k || ! $match[1] ) {
									$cats[] = [
										'name'          => $match[2],
										'subcategories' => [],
									];
								} else {
									end( $cats );
									$cats[ key( $cats ) ]['subcategories'][] = [ 'name' => $match[2] ];
								}
							}
						}
					}
					$options['cats'] = $cats;
					wpforo_update_option( 'tenor_options', $options );
					WPF()->notice->add( 'Successfully Saved', 'success' );
				}
			} else {
				wpforo_delete_option( 'tenor_options' );
				WPF()->notice->add( 'Options reset successfully', 'success' );
			}

			wp_safe_redirect( wp_get_raw_referer() );
			exit();
		}

		public function kses_allowed_html( $allowed_html ) {
			$allowed_html['figure'] = [
				'contenteditable' => true,
				'class'           => true,
				'style'           => true,
				'data-*'          => true,
			];

			return $allowed_html;
		}

		public function add_custom_tag_to_editor( $settings ) {
			if( ! wpfkey( $settings, 'tinymce' ) ) $settings['tinymce'] = [];

			$extended_valid_elements                        = (string) wpfval( $settings['tinymce'], 'extended_valid_elements' );
			$extended_valid_elements                        .= ',figure[class|contenteditable|style|data*]';
			$extended_valid_elements                        = trim( $extended_valid_elements, ',' );
			$settings['tinymce']['extended_valid_elements'] = $extended_valid_elements;

			$settings['tinymce']['content_style'] .= 'figure[data-tenorid] *{cursor: move !important;}';
			$settings['tinymce']['content_style'] .= 'figure[data-tenorid]{display: inline-block; cursor: move !important; margin: 5px;}';
			$settings['tinymce']['content_style'] .= 'figure[data-tenorid] img{max-width: auto !important; max-height: auto !important; display: block; margin: auto;}';

			return $settings;
		}

		public function css_js_enqueue() {
			wp_register_style( 'wpf-tenor', WPFOROTENOR_URL . '/assets/css/tenor.css', [], WPFOROTENOR_VERSION );
			wp_register_script( 'wpf-tenor', WPFOROTENOR_URL . '/assets/es5/tenor.js', [], WPFOROTENOR_VERSION, true );
			wp_localize_script(
				'wpf-tenor',
				'wpfTenor',
				array_merge(
					[ 'WPFOROTENOR_URL' => WPFOROTENOR_URL ],
					$this->options
				)
			);
			wp_enqueue_style( 'wpf-tenor' );
			wp_enqueue_script( 'wpf-tenor' );
		}

		public function add_dynamic_css( $css ) {
			$css .= "";

			return $css;
		}

		public function add_to_form() {
			$template  = wpfval( WPF()->current_object, 'template' );
			$templates = apply_filters( 'wpforo_gifs_loading_template', [ 'forum', 'topic', 'post', 'messages' ] );
			if( $template && in_array( $template, $templates ) ) {
				?>
                <div class="wpf-tenor-button-wrap">
                    <a class="wpf-tenor-button">
                        <img width="24" height="24" src="<?php echo WPFOROTENOR_URL ?>/assets/ico/tenor.png" alt="gif">
                    </a>
                    <div class="wpf-cl"></div>
                </div>
				<?php
			}
		}

		public function strip_shortcodes( $text ) {
			return preg_replace( '#\[wpftenor[^\[\]]+?\]#isu', '', $text );
		}

		public function filter_post_body( $args ) {
			if( wpfval( $args, 'body' ) ) $args['body'] = $this->to_shortcode( $args['body'] );

			return $args;
		}

		public function filter_post_body_on_ajax_edit( $args ) {
			if( isset( $args['body'] ) && $args['body'] ) {
				$args['body'] = $this->do_shortcode( $args['body'], false );
			}

			return $args;
		}

		public function filter_pm_body( $args ) {
			if( wpfval( $args, 'message' ) ) $args['message'] = $this->to_shortcode( $args['message'] );

			return $args;
		}

		public function to_shortcode( $text ) {
			return addslashes(
				preg_replace_callback(
					'#<figure[^<>]*?data-tenorid=[^<>]*?>.+?</figure>#isu',
					function( $match ) {
						$tenorid     = preg_match( '#\sdata-tenorid=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
						$title       = preg_match( '#\stitle=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : 'GIF';
						$alt         = preg_match( '#\salt=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : 'GIF';
						$analsent    = preg_match( '#\sdata-analsent=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
						$src         = preg_match( '#\sdata-src=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
						$srcwidth    = preg_match( '#\sdata-srcwidth=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';
						$srcheight   = preg_match( '#\sdata-srcheight=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';
						$still       = preg_match( '#\sdata-still=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
						$stillwidth  = preg_match( '#\sdata-stillwidth=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';
						$stillheight = preg_match( '#\sdata-stillheight=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';

						return sprintf(
							'[wpftenor tenorid="%1$s" title="%2$s" alt="%3$s" analsent="%4$s" src="%5$s" srcwidth="%6$d" srcheight="%7$d" still="%8$s" stillwidth="%9$d" stillheight="%10$d"]',
							esc_attr( $tenorid ),
							esc_attr( $title ),
							esc_attr( $alt ),
							esc_attr( $analsent ),
							esc_attr( $src ),
							esc_attr( $srcwidth ),
							esc_attr( $srcheight ),
							esc_attr( $still ),
							esc_attr( $stillwidth ),
							esc_attr( $stillheight )
						);
					},
					stripslashes( $text )
				)
			);
		}

		public function do_shortcode( $text, $lazy = true ) {
			return preg_replace_callback(
				'#\[wpftenor[^\[\]]+?\]#isu',
				function( $match ) use ( $lazy ) {
					$match[0] = stripslashes( $match[0] );

					$tenorid     = preg_match( '#\stenorid=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
					$title       = preg_match( '#\stitle=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : 'GIF';
					$alt         = preg_match( '#\salt=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : 'GIF';
					$analsent    = preg_match( '#\sanalsent=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
					$src         = preg_match( '#\ssrc=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
					$srcwidth    = preg_match( '#\ssrcwidth=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';
					$srcheight   = preg_match( '#\ssrcheight=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';
					$still       = preg_match( '#\sstill=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '';
					$stillwidth  = preg_match( '#\sstillwidth=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';
					$stillheight = preg_match( '#\sstillheight=[\"\']([^\"\']+)[\"\']#isu', $match[0], $m ) ? $m[1] : '200';

					return sprintf(
						'<figure class="wpf-gif-figure" contenteditable="false" 
                                data-tenorid="%1$s"
                                data-analsent="%4$s">
                            <img class="%13$s" style="width: %6$dpx; height: %7$dpx; background-color: %11$s"
                                 src="%12$s"
                                 title="%2$s"
                                 alt="%3$s"
                                 data-src="%5$s"
                                 data-srcwidth="%6$d"
                                 data-srcheight="%7$d"
                                 data-still="%8$s"
                                 data-stillwidth="%9$d"
                                 data-stillheight="%10$d"
                            >
                        </figure>',
						esc_attr( $tenorid ),
						esc_attr( $title ),
						esc_attr( $alt ),
						esc_attr( $analsent ),
						esc_attr( $src ),
						esc_attr( $srcwidth ),
						esc_attr( $srcheight ),
						esc_attr( $still ),
						esc_attr( $stillwidth ),
						esc_attr( $stillheight ),
						$this->generate_color( 2 ),
						( $lazy ? esc_attr( WPFOROTENOR_URL . '/assets/ico/transparent.png' ) : esc_attr( $src ) ),
						( $lazy ? 'wpf-gif-lazy' : 'wpf-gif-not-lazy' )
					);
				},
				$text
			);
		}

		private function generate_color( $index = null ) {
			$colors = [
				"#399DEF",
				"#007ADD",
				"#9B9B9B",
				"#EFF6F9",
				"#3F3F3F",
				"#76B7EE",
				"#90B58C",
				"#9686B3",
			];

			return ( is_null( $index ) || intval( $index ) > 7 ) ? $colors[ rand( 0, 7 ) ] : $colors[ intval( $index ) ];
		}

		private function generate_anon_id() {
			$anon_id = '';
			if( WPF()->current_userid ) {
				if( ! $anon_id = (string) wpfval( WPF()->current_usermeta, 'wpf_tenor_anon_id', 0 ) ) {
					$api_url = "https://g.tenor.com/v1/anonid?key={$this->options['key']}";
					if( $remote_response = wp_remote_get( $api_url ) ) {
						if( wp_remote_retrieve_response_code( $remote_response ) === 200 ) {
							$response_body = wp_remote_retrieve_body( $remote_response );
							$response      = json_decode( $response_body, true );
							if( $anon_id = (string) wpfval( $response, 'anon_id' ) ) {
								update_user_meta( WPF()->current_userid, 'wpf_tenor_anon_id', $anon_id );
							}
						}
					}
				}
			}

			return $anon_id;
		}

	}

	if( ! function_exists( 'WPF_TENOR' ) ) {
		function WPF_TENOR() {
			return wpForoTenor::instance();
		}
	}
}

add_action( 'wpforo_core_inited', function() {
	if( version_compare( WPFORO_VERSION, WPFOROTENOR_WPFORO_REQUIRED_VERSION, '>=' ) && wpforo_is_module_enabled( WPFOROTENOR_FOLDER ) ) {
		$GLOBALS['wpforotenor'] = WPF_TENOR();
	}
} );

add_action( 'admin_notices', function() {
	if( ! function_exists( 'WPF' ) || ! version_compare( WPFORO_VERSION, WPFOROTENOR_WPFORO_REQUIRED_VERSION, '>=' ) ) {
		$class   = 'notice notice-error';
		$message = __( 'wpForo Tenor Plugin Notice: Please activate required <a href="https://wpforo.com">wpForo latest version</a> otherwise <b>wpForo Tenor</b> plugin will not work', 'wpforo_tenor' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
} );
