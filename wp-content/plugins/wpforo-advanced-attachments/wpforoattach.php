<?php
/*
* Plugin Name: wpForo Advanced Attachments
* Plugin URI: https://wpForo.com
* Description: Advanced file attachment system for forum topics and posts. AJAX powered media uploading and displaying system with user specific media library.
* Author: gVectors Team
* Author URI: https://gvectors.com/
* Version: 3.0.4
* Text Domain: wpforo_attach
* Domain Path: /languages
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
if( ! defined( 'WPFOROATTACH_VERSION' ) ) define( 'WPFOROATTACH_VERSION', '3.0.4' );
if( ! defined( 'WPFOROATTACH_WPFORO_REQUIRED_VERSION' ) ) define( 'WPFOROATTACH_WPFORO_REQUIRED_VERSION', '2.0.0' );

define( 'WPFOROATTACH_DIR', rtrim( str_replace( '//', '/', dirname( __FILE__ ) ), '/' ) );
define( 'WPFOROATTACH_URL', rtrim( plugins_url( '', __FILE__ ), '/' ) );
define( 'WPFOROATTACH_FOLDER', rtrim( plugin_basename( dirname( __FILE__ ) ), '/' ) );
define( 'WPFOROATTACH_BASENAME', plugin_basename( __FILE__ ) );

function wpforo_attach_load_plugin_textdomain() { load_plugin_textdomain( 'wpforo_attach', false, basename( dirname( __FILE__ ) ) . '/languages/' ); }

add_action( 'plugins_loaded', 'wpforo_attach_load_plugin_textdomain' );

require_once WPFOROATTACH_DIR . "/includes/gvt-api-manager.php";
new GVT_API_Manager( __FILE__, 'wpforo-settings&wpf_tab=wpforo-advanced-attachments', 'wpforo_settings_page_top' );

if( ! class_exists( 'wpForoAttachments' ) ) {
	class wpForoAttachments {
		private static $_instance = null;

		public  $default;
		public  $tools;
		public  $list_table;
		public  $options;
		public  $phrases;
		private $quoted_body;

		public static function instance() {
			if( is_null( self::$_instance ) ) self::$_instance = new self();

			return self::$_instance;
		}

		private function __construct() {
			$this->includes();
			$this->init_defaults();

			$this->tools = new wpForoAttachmentsTools();

			add_filter( 'wpforo_init_templates', [ $this, 'add_templates' ] );
			add_filter( 'wpforo_after_init_current_object', [ $this, 'add_current_object' ] );
			add_action( 'wpforo_after_init', [ $this, 'init' ] );
			add_action( 'admin_bar_menu', [ $this, 'add_adminbar_links' ], 1000 );
			if( is_admin() ) add_action( 'wpforo_after_init', [ $this, 'init_list_table' ] );
		}

		private function includes() {
			require_once( WPFOROATTACH_DIR . '/includes/class.wpForoAttachmentsTools.php' );
			require_once( WPFOROATTACH_DIR . '/includes/class.wpForoAttachmentsListTable.php' );
		}

		private function init_defaults() {
			$this->default = new stdClass;

			$mime_types    = array_flip( get_allowed_mime_types() );
			$allowed_types = ( ! empty( $mime_types ) ? implode( '|', $mime_types ) : 'gif|jpeg|jpg|png|mp4|mp3' );

			$umf = WPF()->settings->_SERVER['upload_max_filesize'];
			$pms = WPF()->settings->_SERVER['post_max_size'];
			$min = WPF()->settings->_SERVER['maxs_min'];

			$this->default->options = [
				// user group oriented options global
				'accepted_file_types'          => $allowed_types,
				'maximum_file_size'            => ( $min ?: 2 * 1024 * 1024 ),
				'disable_delete'               => 1,
				'restrict_using_others_attach' => 1,
				'max_uploads_per_day'          => 0,
				'max_attachs_per_post'         => 30,

				// user group oriented options for each usergroupids
				'groups'                       => [
					1 => [
						'accepted_file_types'          => $allowed_types,
						'maximum_file_size'            => ( $min ?: 2 * 1024 * 1024 ),
						'disable_delete'               => 0,
						'restrict_using_others_attach' => 0,
						'max_uploads_per_day'          => 0,
						'max_attachs_per_post'         => 0,
					],
				],

				'download_via_php'           => 0,
				'is_daily_limit_exceeded'    => 0,
				'thumbnail_width'            => 400,
				'thumbnail_height'           => 300,
				'thumbnail_jpeg_quality'     => 50,
				'image_caption'              => 0,
				'boxed'                      => 0,
				'lightbox'                   => 1,
				'server_upload_max_filesize' => $umf,
				'server_post_max_size'       => $pms,
				'server_maxs_min'            => $min,
				'attachs_per_load'           => 15,
				'auto_upload'                => 1,
				'bigimg_max_height'          => 1080,
				'bigimg_jpeg_quality'        => 70,
			];

			$this->default->phrases = [
				'attach files'                                                                              => __( 'Attach Files', 'wpforo_attach' ),
				'attached file'                                                                             => __( 'Attached File', 'wpforo_attach' ),
				'add files...'                                                                              => __( 'Add files...', 'wpforo_attach' ),
				'start upload'                                                                              => __( 'Start upload', 'wpforo_attach' ),
				'insert into post'                                                                          => __( 'Insert into post', 'wpforo_attach' ),
				'start'                                                                                     => __( 'Start', 'wpforo_attach' ),
				'cancel'                                                                                    => __( 'Cancel', 'wpforo_attach' ),
				'delete'                                                                                    => __( 'delete', 'wpforo_attach' ),
				'close'                                                                                     => __( 'Close', 'wpforo_attach' ),
				'select all'                                                                                => __( 'select all', 'wpforo_attach' ),
				'processing...'                                                                             => __( 'Processing...', 'wpforo_attach' ),
				'attachment is not available'                                                               => __( 'attachment is not available', 'wpforo_attach' ),
				'choose a file or drag it here'                                                             => __( 'Choose a file or drag it here', 'wpforo_attach' ),
				'please select a file(s) using right checkbox(es) to insert in post'                        => __( 'Please select a file(s) using right checkbox(es) to insert in post', 'wpforo_attach' ),
				'error: file is too big. allowed size is: %s'                                               => __( 'Error: File is too big. Allowed size is: %s', 'wpforo_attach' ),
				'error: file is too big. server_upload_max_filesize is: %s'                                 => __( 'Error: File is too big. server_upload_max_filesize is: %s', 'wpforo_attach' ),
				'error: file is too big. server_post_max_size is: %s'                                       => __( 'Error: File is too big. server_post_max_size is: %s', 'wpforo_attach' ),
				'error: filetype not allowed'                                                               => __( 'Error: Filetype not allowed', 'wpforo_attach' ),
				'the uploaded file exceeds the upload_max_filesize directive in php.ini'                    => __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 'wpforo_attach' ),
				'the uploaded file exceeds the max_file_size directive that was specified in the html form' => __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'wpforo_attach' ),
				'the uploaded file was only partially uploaded'                                             => __( 'The uploaded file was only partially uploaded', 'wpforo_attach' ),
				'no file was uploaded'                                                                      => __( 'No file was uploaded', 'wpforo_attach' ),
				'missing a temporary folder'                                                                => __( 'Missing a temporary folder', 'wpforo_attach' ),
				'failed to write file to disk'                                                              => __( 'Failed to write file to disk', 'wpforo_attach' ),
				'a php extension stopped the file upload'                                                   => __( 'A PHP extension stopped the file upload', 'wpforo_attach' ),
				'the uploaded file exceeds the post_max_size directive in php.ini'                          => __( 'The uploaded file exceeds the post_max_size directive in php.ini', 'wpforo_attach' ),
				'file is too big'                                                                           => __( 'File is too big', 'wpforo_attach' ),
				'file is too small'                                                                         => __( 'File is too small', 'wpforo_attach' ),
				'filetype not allowed'                                                                      => __( 'Filetype not allowed', 'wpforo_attach' ),
				'maximum number of files exceeded'                                                          => __( 'Maximum number of files exceeded', 'wpforo_attach' ),
				'image exceeds maximum width'                                                               => __( 'Image exceeds maximum width', 'wpforo_attach' ),
				'image requires a minimum width'                                                            => __( 'Image requires a minimum width', 'wpforo_attach' ),
				'image exceeds maximum height'                                                              => __( 'Image exceeds maximum height', 'wpforo_attach' ),
				'image requires a minimum height'                                                           => __( 'Image requires a minimum height', 'wpforo_attach' ),
				'file upload aborted'                                                                       => __( 'File upload aborted', 'wpforo_attach' ),
				'failed to resize image'                                                                    => __( 'Failed to resize image', 'wpforo_attach' ),
				'or drag and drop it here.'                                                                 => __( 'or drag and drop it here.', 'wpforo_attach' ),
				'max file size %1$s'                                                                        => __( 'Max file size %1$s', 'wpforo_attach' ),
				'drop here'                                                                                 => __( 'DROP HERE', 'wpforo_attach' ),
				'choose a file'                                                                             => __( 'Choose a file', 'wpforo_attach' ),
				'attached successfully'                                                                     => __( 'Attached Successfully', 'wpforo_attach' ),
				'you can even paste image from clipboard by pressing ctrl+v.'                               => __( 'You can even paste image from clipboard by pressing Ctrl+V.', 'wpforo_attach' ),
				'my media'                                                                                  => __( 'My Media', 'wpforo_attach' ),
				'add to post'                                                                               => __( 'add to post', 'wpforo_attach' ),
			];
		}

		public function init() {
			$this->init_options();
			$this->init_hooks();
			$this->get_requested_file();
		}

		private function init_options() {
			$this->options = wpforo_get_option( 'attach_options', $this->default->options );

			if( wpfkey( $this->options, 'groups', WPF()->current_user_groupid, 'disable_delete' ) ) $this->options['disable_delete'] = (int) (bool) (int) $this->options['groups'][ WPF()->current_user_groupid ]['disable_delete'];
			if( wpfkey( $this->options, 'groups', WPF()->current_user_groupid, 'maximum_file_size' ) ) $this->options['maximum_file_size'] = (int) $this->options['groups'][ WPF()->current_user_groupid ]['maximum_file_size'];
			if( wpfkey( $this->options, 'groups', WPF()->current_user_groupid, 'accepted_file_types' ) ) $this->options['accepted_file_types'] = (string) $this->options['groups'][ WPF()->current_user_groupid ]['accepted_file_types'];
			if( wpfkey( $this->options, 'groups', WPF()->current_user_groupid, 'max_uploads_per_day' ) ) $this->options['max_uploads_per_day'] = (int) $this->options['groups'][ WPF()->current_user_groupid ]['max_uploads_per_day'];
			if( wpfkey( $this->options, 'groups', WPF()->current_user_groupid, 'max_attachs_per_post' ) ) $this->options['max_attachs_per_post'] = (int) $this->options['groups'][ WPF()->current_user_groupid ]['max_attachs_per_post'];
			if( wpfkey( $this->options, 'groups', WPF()->current_user_groupid, 'restrict_using_others_attach' ) ) $this->options['restrict_using_others_attach'] = (int) (bool) (int) $this->options['groups'][ WPF()->current_user_groupid ]['restrict_using_others_attach'];

			$this->options['is_daily_limit_exceeded'] = (int) is_daily_limit_exceeded();

			//START hardcode if admin or moderator
			if( ( ! isset( $_GET['page'] ) || $_GET['page'] !== wpforo_prefix_slug( 'settings' ) ) && current_user_can( 'administrator' ) ) {
				$this->options['disable_delete'] = 0;
			}
			//END hardcode if admin or moderator
			//START hardcode restrict file types for current NEW user
			$this->options['accepted_file_types'] = $this->tools->strip_not_allowed_filetypes( $this->options['accepted_file_types'] );
			//END hardcode restrict file types for current NEW user

			$this->options['server_maxs_min_human']            = WPF()->settings->_SERVER['maxs_min_human'];
			$this->options['server_post_max_size_human']       = WPF()->settings->_SERVER['post_max_size_human'];
			$this->options['server_upload_max_filesize_human'] = WPF()->settings->_SERVER['upload_max_filesize_human'];
			$this->options['maximum_file_size_human']          = wpforo_print_size( $this->options['maximum_file_size'] );

			$this->phrases = wpforo_get_option( 'attach_phrases', $this->default->phrases );
		}

		public function init_list_table() {
			if( preg_match( '#^wpforo-(?:\d+-)?advanced-attachments$#iu', wpfval( $_GET, 'page' ) ) ) {
				$this->list_table = new wpForoAttachmentsListTable();
				$this->list_table->process_bulk_action();
			}
		}

		public function init_settings_info( $addons ) {
			$addons['wpforo-advanced-attachments'] = [
				"title"                => esc_html__( "Advanced Attachments", "wpforo" ),
				"title_original"       => "Advanced Attachments",
				"icon"                 => '<img src="' . WPFORO_URL . '/assets/addons/attachments/header.png' . '" alt="wpForo Advanced Attachments Logo">',
				"description"          => __( 'Adds an advanced file attachment system to forum topics and posts. AJAX powered media uploading and displaying system with user specific library.', 'wpforo' ),
				"description_original" => "Adds an advanced file attachment system to forum topics and posts. AJAX powered media uploading and displaying system with user specific library.",
				"docurl"               => "",
				"status"               => "ok",
				"base"                 => false,
				"callback_for_page"    => function() {
					require WPFOROATTACH_DIR . "/includes/options.php";
				},
				"options"              => [

				],
			];

            return $addons;
        }

		private function init_hooks() {
			if( wpforo_is_admin() ) {
				add_action( 'wpforo_action_wpforo_attach_settings_save', [ $this, 'settings_save' ] );
			}
            add_filter( 'wpforo_settings_init_addons_info', [ $this, 'init_settings_info' ] );
			add_filter( 'wpforo_get_statistic_array_filter', [ &$this, 'add_statistic' ] );
			add_filter( "wpforo_editor_settings", [ $this, 'add_custom_tag_to_editor' ] );
			add_filter( "wpforo_kses_allowed_html", [ $this, 'kses_allowed_html' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'css_js_enqueue' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_css_js_enqueue' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'lightbox_css_js_enqueue' ] );
			if( WPF()->current_userid ) {
				remove_action( 'wpforo_topic_form_extra_fields_after', [ WPF()->tpl, 'add_default_attach_input' ] );
				remove_action( 'wpforo_reply_form_extra_fields_after', [ WPF()->tpl, 'add_default_attach_input' ] );
				remove_action( 'wpforo_portable_form_extra_fields_after', [ WPF()->tpl, 'add_default_attach_input' ] );
				remove_action( 'wpforopm_form_bottom', [ WPF()->tpl, 'add_default_attach_input' ] );
				add_action( 'wpforo_topic_form_buttons_hook', [ $this, 'add_frontend_form_button' ], 9, 1 );
				add_action( 'wpforo_reply_form_buttons_hook', [ $this, 'add_frontend_form_button' ], 9, 0 );
				add_action( 'wpforo_portable_form_buttons_hook', [ $this, 'add_frontend_form_button' ], 9, 0 );
				add_action( 'wpforopm_form_bottom', [ $this, 'add_frontend_form_button' ], 9, 0 );
			}
			add_action( 'wpforo_topic_info_end', [ $this, 'add_attach_icon' ], 1, 1 );
			add_action( 'wp_footer', [ $this, 'add_footer_html' ], 99, 0 );
			add_action( 'wpforo_after_add_topic', [ $this, 'after_add_edit_post' ] );
			add_action( 'wpforo_after_edit_topic', [ $this, 'after_add_edit_post' ] );
			add_action( 'wpforo_after_add_post', [ $this, 'after_add_edit_post' ] );
			add_action( 'wpforo_after_edit_post', [ $this, 'after_add_edit_post' ] );
			add_action( 'wpforo_after_delete_post', [ $this, 'after_delete_post' ] );

			add_filter( 'wpforo_add_topic_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_edit_topic_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_add_post_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_edit_post_data_filter', [ $this, 'filter_post_body' ] );
			add_filter( 'wpforo_quote_post_ajax', [ $this, 'filter_post_body_on_ajax_edit' ] );
			add_filter( 'wpforo_edit_post_ajax', [ $this, 'filter_post_body_on_ajax_edit' ] );
			add_filter( 'wpforopm_add_pm_data_filter', [ $this, 'filter_pm_body' ] );

			add_filter( 'wpforo_dynamic_css_filter', [ $this, 'add_dynamic_css' ] );

			if( $this->options['restrict_using_others_attach'] ) {
				if( ! WPF()->perm->forum_can( 'et' ) ) {
					add_filter( 'wpforo_add_topic_data_filter', [ $this, 'remove_not_allowed_attachids' ] );
					add_filter( 'wpforo_edit_topic_data_filter', [ $this, 'remove_not_allowed_attachids' ] );
				}
				if( ! WPF()->perm->forum_can( 'er' ) ) {
					add_filter( 'wpforo_add_post_data_filter', [ $this, 'remove_not_allowed_attachids' ] );
					add_filter( 'wpforo_edit_post_data_filter', [ $this, 'remove_not_allowed_attachids' ] );
				}
				add_filter( 'wpforopm_add_pm_data_filter', [ $this, 'remove_pm_not_allowed_attachids' ] );
			}
		}

		private function build_unique_slug() {
			return substr( md5( rand() . time() . rand() ), 0, 32 );
		}

		public function add( $args ) {
			$default = [
				'userid'   => null,
				'slug'     => '',
				'filename' => '',
				'fileurl'  => '#',
				'size'     => 0,
				'mime'     => '',
				'created'  => 0,
			];
			$args    = wpforo_array_args_cast_and_merge( $args, $default );
			if( ! $args['filename'] || ! $args['fileurl'] ) return false;

			if( is_null( $args['userid'] ) ) $args['userid'] = WPF()->current_userid;
			if( ! $args['slug'] ) $args['slug'] = $this->build_unique_slug();
			if( ! $args['created'] ) $args['created'] = current_time( 'timestamp', 1 );

			if( WPF()->db->insert(
				WPF()->tables->attachments,
				[
					'userid'   => $args['userid'],
					'slug'     => $args['slug'],
					'filename' => $args['filename'],
					'fileurl'  => $args['fileurl'],
					'size'     => $args['size'],
					'mime'     => $args['mime'],
					'created'  => $args['created'],
				],
				[
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%d',
				]
			) ) {
				return WPF()->db->insert_id;
			}

			return false;
		}

		public function edit( $args, $attachid ) {
			if( empty( $args ) || ! ( $attachid = wpforo_bigintval( $attachid ) ) ) return false;
			$default = [
				'userid'   => null,
				'filename' => '',
				'fileurl'  => '',
				'size'     => null,
				'mime'     => '',
				'posts'    => null,
			];
			$args    = wpforo_parse_args( $args, $default );

			$sets   = array_filter( $args );
			$format = [];
			foreach( $sets as $key => $set ) {
				if( array_key_exists( $key, $default ) && is_null( $default[ $key ] ) ) {
					$format[] = '%d';
				} else {
					$format[] = '%s';
				}
			}

			if( ! empty( $set ) ) {
				if( false !== WPF()->db->update(
						WPF()->tables->attachments, $sets, [ 'attachid' => $attachid ], $format, [ '%d' ]
					) ) {
					return $attachid;
				}
			}

			return false;
		}

		public function delete( $arg ) {
			if( empty( $arg ) ) return false;

			$where  = [];
			$format = [];
			if( is_numeric( $arg ) ) {
				$where['attachid'] = wpforo_bigintval( $arg );
				$format[]          = '%d';
			} else {
				$where['fileurl'] = trim( $arg );
				$format[]         = '%s';
			}

			if( false !== WPF()->db->delete(
					WPF()->tables->attachments,
					$where,
					$format
				) ) {
				return $arg;
			}

			return false;
		}

		public function get_attach( $arg ) {
			if( empty( $arg ) ) return null;

			$where = '';
			if( is_array( $arg ) && $slug = trim( wpfval( $arg, 'slug' ) ) ) {
				$where = WPF()->db->prepare( "`slug` = %s", $slug );
			} elseif( is_numeric( $arg ) ) {
				$where = WPF()->db->prepare( "`attachid` = %d", wpforo_bigintval( $arg ) );
			} elseif( is_string( $arg ) ) {
				$where = "`fileurl` LIKE '%" . esc_sql( trim( $arg ) ) . "'";
			}
			if( ! $where ) return null;
			$sql = "SELECT * FROM `" . WPF()->tables->attachments . "` WHERE $where";
			if( WPF()->ram_cache->exists( $sql ) ) {
				$attach = WPF()->ram_cache->get( $sql );
			} else {
				$attach = WPF()->db->get_row( $sql, ARRAY_A );
				WPF()->ram_cache->set( $sql, $attach );
			}

			return apply_filters( 'wpforoattach_get_attach', $attach, $arg );
		}

		public function get_attachs( $args = [], &$items_count = 0 ) {
			$default = [
				'include'   => [],      // array( 2, 10, 25 ) or '2,10,25'
				'exclude'   => [],      // array( 2, 10, 25 ) or '2,10,25'
				'userid'    => null,        // user id in DB
				'mime'      => null,
				'orderby'   => 'attachid DESC',   // attachid|size|posts
				'order'     => '',       // ASC DESC
				'offset'    => null,        // this use when you give row_count
				'row_count' => null        // 4 or 1 ...
			];
			if( empty( $args['orderby'] ) ) $args['order'] = '';

			$args = wpforo_parse_args( $args, $default );
			extract( $args );

			$include = wpforo_parse_args( $include );
			$exclude = wpforo_parse_args( $exclude );

			$sql    = "SELECT * FROM `" . WPF()->tables->attachments . "`";
			$wheres = [];

			if( $include ) $wheres[] = "`attachid` IN(" . implode( ',', array_map( 'wpforo_bigintval', $include ) ) . ")";
			if( $exclude ) $wheres[] = "`attachid` NOT IN(" . implode( ',', array_map( 'wpforo_bigintval', $exclude ) ) . ")";
			if( ! is_null( $userid ) ) $wheres[] = "`userid` = " . wpforo_bigintval( $userid );
			if( $mime ) $wheres[] = "`mime` LIKE '" . trim( $mime ) . "'";

			if( $wheres ) $sql .= " WHERE " . implode( " AND ", $wheres );

			$item_count_sql = preg_replace( '#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql );
			if( $item_count_sql ) {
				if( WPF()->ram_cache->exists( $item_count_sql ) ) {
					$items_count = WPF()->ram_cache->get( $item_count_sql );
				} else {
					$items_count = WPF()->db->get_var( $item_count_sql );
					WPF()->ram_cache->set( $item_count_sql, $items_count );
				}
			}

			$sql .= esc_sql( " ORDER BY $orderby " . $order );

			if( $row_count ) $sql .= esc_sql( " LIMIT " . intval( $offset ) . "," . intval( $row_count ) );

			if( WPF()->ram_cache->exists( $sql ) ) {
				$attachs = WPF()->ram_cache->get( $sql );
			} else {
				$attachs = WPF()->db->get_results( $sql, ARRAY_A );
				WPF()->ram_cache->set( $sql, $attachs );
			}

			return $attachs;
		}

		public function search( $needle, $fields = [], $limit = null ) {
			$attachids = [];
			if( ! $needle ) return $attachids;

			$needle = sanitize_text_field( $needle );
			if( empty( $fields ) ) {
				$fields = [
					'filename',
					'fileurl',
					'mime',
				];
			}

			$sql    = "SELECT `attachid` FROM `" . WPF()->tables->attachments . "`";
			$wheres = [];

			foreach( $fields as $field ) {
				$field    = sanitize_text_field( $field );
				$wheres[] = "`" . esc_sql( $field ) . "` LIKE '%" . esc_sql( $needle ) . "%'";
			}

			if( ! empty( $wheres ) ) {
				$sql .= " WHERE " . implode( " OR ", $wheres );
				if( $limit ) $sql .= " LIMIT " . intval( $limit );

				$attachids = WPF()->db->get_col( $sql );
			}

			return $attachids;
		}

		public function search_possible_spams() {
			$attachids = [];

			$spam_file_phrases = [
				0 => [ 'watch', 'movie' ],
				1 => [ 'download', 'free' ],
			];

			$ext_risk = [ 'pdf', 'doc', 'docx', 'txt', 'htm', 'html', 'rtf', 'xml', 'xls', 'xlsx', 'php', 'cgi', 'php', 'cgi', 'exe' ];

			$sql    = "SELECT `attachid` FROM `" . WPF()->tables->attachments . "`";
			$wheres = [];

			foreach( $spam_file_phrases as $phrases ) {
				foreach( $phrases as $phrase ) {
					$phrase   = sanitize_text_field( $phrase );
					$wheres[] = "`fileurl` LIKE '%" . esc_sql( $phrase ) . "%'";
				}
			}
			if( $wheres ) $sql .= " WHERE (" . implode( " OR ", $wheres ) . ")";

			$wheres = [];
			foreach( $ext_risk as $ext ) {
				$ext      = sanitize_text_field( $ext );
				$wheres[] = "`fileurl` LIKE '%" . esc_sql( $ext ) . "'";
			}
			if( $wheres ) {
				$sql .= " AND (" . implode( " OR ", $wheres ) . ")";

				$attachids = WPF()->db->get_col( $sql );
			}

			return $attachids;
		}

		public function get_distinct_userids() {
			return WPF()->db->get_col( "SELECT DISTINCT `userid` FROM `" . WPF()->tables->attachments . "`" );
		}

		public function get_distinct_mimes() {
			return WPF()->db->get_col( "SELECT DISTINCT `mime` FROM `" . WPF()->tables->attachments . "`" );
		}

		public function get_post_count_by_attach( $attachid ) {
			if( empty( $attachid ) ) return false;
			$sql = "SELECT COUNT(`postid`) FROM `" . WPF()->tables->posts . "` WHERE `body` REGEXP '\\\[attach\\\]([^\\\]]*,)?%d(,[^\\\[]*)?\\\[/attach\\\]'";

			return WPF()->db->get_var( WPF()->db->prepare( $sql, $attachid ) );
		}

		public function is_attach_have_posts( $attachid ) {
			if( $this->get_post_count_by_attach( $attachid ) ) return true;

			return false;
		}

		/**
		 * @param int $userid
		 *
		 * @return int
		 */
		public function _get_user_attach_count_per_day( $userid ) {
			$userid = wpforo_bigintval( $userid );
			$sql    = "SELECT COUNT(*) AS c 
                FROM `" . WPF()->tables->attachments . "`
                WHERE `userid` = %d
                AND `created` > %d";
			$sql    = WPF()->db->prepare( $sql, $userid, ( current_time( 'timestamp', 1 ) - DAY_IN_SECONDS ) );

			return (int) WPF()->db->get_var( $sql );
		}

		public function get_user_attach_count_per_day( $userid ) {
			return wpforo_ram_get( [ $this, '_get_user_attach_count_per_day' ], $userid );
		}

		public function remove_file( $attachid ) {
			if( $attach = $this->get_attach( $attachid ) ) {
				$fileurl_dirname  = basename( dirname( $attach['fileurl'] ) );
				$fileurl_basename = basename( $attach['fileurl'] );

				$full_file_addrss = WPF()->folders['attachments']['dir'] . DIRECTORY_SEPARATOR . $fileurl_dirname . DIRECTORY_SEPARATOR . $fileurl_basename;
				if( file_exists( $full_file_addrss ) ) {
					$d1 = unlink( $full_file_addrss );
				} else {
					$d1 = true;
				}

				$full_thumbfile_addrss = WPF()->folders['attachments']['dir'] . DIRECTORY_SEPARATOR . $fileurl_dirname . DIRECTORY_SEPARATOR . 'thumbnail' . DIRECTORY_SEPARATOR . $fileurl_basename;
				if( file_exists( $full_thumbfile_addrss ) ) {
					$d2 = unlink( $full_thumbfile_addrss );
				} else {
					$d2 = true;
				}

				if( $d1 && $d2 ) return true;
			}

			return false;
		}

		public function detect_post_body_attachids( $body ) {
			$attachids = [];
			if( preg_match_all( '#\[attach]([^\[\]]+?)\[/attach]#isu', $body, $matches, PREG_SET_ORDER ) ) {
				foreach( $matches as $match ) {
					$expld     = array_filter( array_map( 'wpforo_bigintval', explode( ',', $match[1] ) ) );
					$attachids = array_merge( $attachids, $expld );
				}
			}

			return $attachids;
		}

		public function rebuild_post_count( $attachid ) {
			if( empty( $attachid ) ) return false;
			if( ! $post_count = $this->get_post_count_by_attach( $attachid ) ) $post_count = 0;
			$this->edit( [ 'posts' => $post_count ], $attachid );

			return $post_count;
		}

		public function after_add_edit_post( $post ) {
			if( empty( $post['body'] ) ) return;
			if( $attachids = $this->detect_post_body_attachids( $post['body'] ) ) {
				foreach( $attachids as $attachid ) {
					$this->rebuild_post_count( $attachid );
				}
			}
		}

		public function after_delete_post( $post ) {
			if( empty( $post['body'] ) ) return;
			if( $attachids = $this->detect_post_body_attachids( $post['body'] ) ) {
				foreach( $attachids as $attachid ) {
					if( ! $this->is_attach_have_posts( $attachid ) ) {
						if( $this->remove_file( $attachid ) ) $this->delete( $attachid );
					}
				}
			}
		}

		public function remove_pm_not_allowed_attachids( $data ) {
			$data['message'] = preg_replace_callback( '#\[attach]([^\[\]]+?)\[/attach]#isu', [ $this, '_remove_not_allowed_attachids' ], $data['message'] );

			return $data;
		}

		public function remove_not_allowed_attachids( $data ) {
			if( strpos( $data['body'], '[attach]' ) !== false ) {
				if( preg_match_all( '#<blockquote[^<>]*?data-postid=\\\?[\'\"](?<postid>\d+)\\\?[\'\"][^<>]*?>.*?\[attach][^\[\]]+\[/attach]#isu', $data['body'], $matches, PREG_SET_ORDER ) ) {
					$quoted_postids = '';
					foreach( $matches as $match ) $quoted_postids .= $match['postid'] . ",";
					$quoted_postids = trim( $quoted_postids, ',' );

					$sql               = "SELECT GROUP_CONCAT(`body`) AS quoted_bodies 
                      FROM " . WPF()->tables->posts . " 
                      WHERE `topicid` = " . wpforo_bigintval( WPF()->current_object['topicid'] ) . " 
                      AND `postid` IN(" . $quoted_postids . ")";
					$this->quoted_body = WPF()->db->get_var( $sql );
				}

				$data['body']      = preg_replace_callback( '#\[attach]([^\[\]]+?)\[/attach]#isu', [ $this, '_remove_not_allowed_attachids' ], $data['body'] );
				$this->quoted_body = null;
			}

			return $data;
		}

		private function _remove_not_allowed_attachids( $shortcode ) {
			$filtered_attachids = [];

			$replace   = '<span class="wpf-attachment-404">-- ' . wpforo_attach_phrase( 'attachment is not available', false ) . ' --</span>';
			$attachids = array_filter( array_map( 'wpforo_bigintval', explode( ',', $shortcode[1] ) ) );

			if( $this->quoted_body ) {
				foreach( $attachids as $attachid ) {
					if( preg_match( '#\[attach]([^]]*,[\r\n\t\s\0]*|[\r\n\t\s\0]*)?' . $attachid . '([\r\n\t\s\0]*,[^\[]*|[\r\n\t\s\0]*)?\[/attach]#isu', $this->quoted_body ) ) {
						$filtered_attachids[] = $attachid;
					}
				}
			}

			$args = [
				'include' => $attachids,
				'userid'  => WPF()->current_userid,
				'orderby' => 'FIELD(`attachid`,' . implode( ',', $attachids ) . ')',
			];
			if( $attachs = $this->get_attachs( $args ) ) {
				foreach( $attachs as $attach ) $filtered_attachids[] = $attach['attachid'];
			}

			if( $filtered_attachids ) $replace = '[attach]' . implode( ',', $filtered_attachids ) . '[/attach]';

			return $replace;
		}

		public function get_count() {
			return WPF()->db->get_var( "SELECT COUNT(`attachid`) FROM `" . WPF()->tables->attachments . "`" );
		}

		public function get_sizes() {
			return WPF()->db->get_var( "SELECT sum(`size`) FROM `" . WPF()->tables->attachments . "`" );
		}

		public function medialib_page() {
			$this->list_table->prepare_items();

			?>
            <div id="wpf-admin-wrap" class="wrap">
				<?php wpforo_screen_option() ?>

                <div id="icon-users" class="icon32"><br></div>
                <h2 style="padding:30px 0 0 0; line-height: 20px; margin-bottom:15px;"><?php _e( 'Media Library - wpForo Advanced Attachments', 'wpforo_attach' ); ?></h2>

				<?php WPF()->notice->show(); ?>
                <br>
                <hr>

                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="wpf-attach-lib" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>

					<?php $this->list_table->users_dropdown() ?>
					<?php $this->list_table->mimes_dropdown() ?>
                    <input type="submit" value="Filter" class="button button-large"/>

                    <!-- Now we can render the completed list table -->
					<?php $this->list_table->search_box( 'Search Attachs', 'wpf-attach-search' ) ?>
                    <hr>
                    <a href="<?php echo admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'advanced-attachments' ) ) ?>">All Attachments(<?php echo $this->get_count() ?>)</a>&nbsp;
					<?php $possible_spam_count = count( $this->search_possible_spams() ) ?>
                    <a href="<?php echo admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'advanced-attachments' ) . '&filter=possible-spam' ) ?>" style="color: <?php echo( $possible_spam_count ? 'red' : 'gray' ) ?>;">
                        Possible Spam Attachments(<?php echo $possible_spam_count ?>)
                    </a>
					<?php if( $possible_spam_count && isset( $_REQUEST['filter'] ) && $_REQUEST['filter'] === 'possible-spam' ) : ?>
                        &nbsp;&nbsp;
                        <a onclick="return confirm('<?php wpforo_phrase( "Are you sure you whant to empty all possible spam attachments?" ) ?>');"
                           href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'advanced-attachments' ) . '&filter=possible-spam&spam=empty' ), 'bulk-spam-empty' ) ?>">
                            <button type="button">Empty Possible Spams</button>
                        </a>
					<?php endif ?>
					<?php $this->list_table->display() ?>
                </form>

            </div>
			<?php if( $this->options['lightbox'] ): ?>
                <!-- The blueimp Gallery widget for content images -->
                <div style="display: none;" id="wpf-content-blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>
			<?php
			endif;
		}

		public function add_statistic( $counts ) {
			$counts['attachments']      = $this->get_count();
			$counts['attachment_sizes'] = $this->get_sizes();

			return $counts;
		}

		public function add_frontend_form_button( $forumid = null ) {
			if( ! wpforo_attach_can_attach( $forumid ) ) {
				printf( '<i class="fas fa-ban"></i><span> %1$s</span>', wpforo_phrase( 'No more attachments are allowed today', false ) );

				return;
			}
			printf(
				'<div class="wpf_attach_button_wrap" title="%1$s">
                        <i class="fas fa-photo-video wpfa-form-ico"></i>
                        <a class="wpf_attach_button">%2$s</a>&nbsp;&nbsp;
                        %5$s
                        <span  class="wpf_dd_info">%3$s</span>
                        <span class="wpf_attach_max_fz">%4$s</span>
                    </div>
                    <div class="wpfattach-portable-wrap"></div>',
				esc_attr( wpforo_attach_phrase( 'You can even paste image from clipboard by pressing Ctrl+V.', false ) ),
				wpforo_attach_phrase( 'My Media', false ),
				wpforo_attach_phrase( 'or drag and drop it here.', false ),
				sprintf(
					wpforo_attach_phrase( 'Max file size %1$s', false ),
					$this->options['maximum_file_size_human']
				),
				( $this->options['auto_upload'] ? sprintf(
					'<i class="fas fa-paperclip wpfa-form-ico"></i>
                        <label for="wpfa-file-input" class="wpfa-browse">%1$s</label>',
					wpforo_attach_phrase( 'Attach Files', false )
				) : '' )
			);
		}

		public function add_attach_icon( $args = [] ) {
			if( $args['has_attach'] ) echo ' <i class="fas fa-paperclip" aria-hidden="true" title="' . wpforo_attach_phrase( 'Attached File', false ) . '"></i> ';
		}

		public function add_footer_html() {
			if( ! is_wpforo_attach_page() ) return;

			if( is_user_logged_in() ) : ?>
                <div id="wpfa_dialog_wrap" style="display: none;" tabindex="-1">
                    <div id="wpfa_dialog">
                        <div id="wpfa_dialog_header">
                            <i id="wpfa_dialog_close" class="fas fa-window-close fa-2x"></i>
                        </div>
                        <div id="wpfa_dialog_body">
                            <form id="wpfa_fileupload" action="<?php echo admin_url( 'admin-ajax.php' ) ?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="wpforoattach_load_ajax_function"/>
                                <input title="<?php wpforo_attach_phrase( 'Choose a file or Drag it here' ) ?>" id="wpfa-file-input" type="file" name="files[]" multiple class="add" style="cursor: pointer;">
                                <div id="wpfa_dialog_action_buttons" class="wpfa-fileupload-buttonbar">
                                    <div class="wpfa-fileupload-buttons">
                                        <div class="wpfa-buttons-group">
                                            <label title="<?php wpforo_attach_phrase( 'Choose a file or Drag it here' ) ?>" for="wpfa-file-input" class="wpfa-button wpfa-add-files">
                                                <span class="wpfa-button-icon"><i class="fas fa-plus-circle"></i></span>
                                                <span title="<?php wpforo_attach_phrase( 'Choose a file or Drag it here' ) ?>" class="wpfa-button-text"><?php wpforo_attach_phrase( 'add files...' ) ?></span>
                                            </label>
											<?php if( ! $this->options['auto_upload'] ) : ?>
                                                <button type="button" class="wpfa-start wpfa-button wpfa-button-start">
                                                    <span class="wpfa-button-icon"><i class="fas fa-upload"></i></span>
                                                    <span class="wpfa-button-text"><?php wpforo_attach_phrase( 'Start upload' ) ?></span>
                                                </button>
											<?php endif; ?>
                                            <button id="wpf_attach_do" type="button" class="wpfa-button wpfa-button-add-to-post">
                                                <span class="wpfa-button-icon"><i class="fas fa-arrow-circle-right"></i></span>
                                                <span class="wpfa-button-text"><?php wpforo_attach_phrase( 'Insert into post' ) ?></span>
                                            </button>
											<?php if( ! $this->options['auto_upload'] ) : ?>
                                                <button type="reset" class="wpfa-cancel wpfa-button wpfa-button-cancel wpfa-show-for-large">
                                                    <span class="wpfa-button-icon"><i class="fas fa-ban"></i></span>
                                                    <span class="wpfa-button-text"><?php wpforo_attach_phrase( 'Cancel' ) ?></span>
                                                </button>
											<?php endif; ?>
                                        </div>

										<?php if( empty( WPF_ATTACH()->options['disable_delete'] ) ) : ?>
                                            <button type="button" class="wpfa-delete wpfa-button wpfa-button-delete wpfa-show-for-large">
                                                <span class="wpfa-button-icon"><i class="fas fa-trash-alt"></i></span>
                                                <span class="wpfa-button-text"><?php wpforo_attach_phrase( 'Delete' ) ?></span>
                                            </button>
										<?php endif; ?>
                                        <label class="wpfa-checkbox-select-all wpfa-show-for-large" title="<?php echo esc_attr( wpforo_attach_phrase( 'select all', false ) ) ?>"><input type="checkbox" class="wpfa-toggle"></label>
                                    </div>
                                    <div class="wpfa-fileupload-progress wpforo-fade" style="display:none;max-width:500px;margin-top:2px;">
                                        <div class="wpfa-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="wpfa-progress-extended"></div>
                                    </div>
                                </div>
                                <div id="wpfa_dialog_items" role="presentation">
                                    <div id="wpfa_dialog_rows" data-nomore="0" data-offset="0"></div>
                                    <span id="wpfa-loading-spinner" class="wpfa-loading"><i class="fas fa-spinner fa-spin"></i></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
			<?php endif ?>
			<?php if( $this->options['lightbox'] ): ?>
                <!-- The blueimp Gallery widget for dialog -->
                <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even" style="display: none;">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>
                <!-- The blueimp Gallery widget for content images -->
                <div id="wpf-content-blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" style="display: none;">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a><a class="next">›</a><a class="close">×</a><a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>
			<?php endif ?>
			<?php
		}

		private function can_view() {
			return WPF()->usergroup->can( 'caa' );
		}

		public function add_templates( $templates ) {
			$templates['attachment'] = [
				'type'                       => 'callback',
				'key'                        => 'attachment',
				'slug'                       => 'forofile',
				'title'                      => 'Foro File',
				'ico'                        => '<i class="fas fa-file"></i>',
				'add_in_member_menu'         => 0,
				'add_in_member_buttons'      => 0,
				'callback_for_can_view_menu' => '__return_false',
				'is_default'                 => 0,
			];

			return $templates;
		}

		public function add_current_object( $current_object ) {
			if( ! $current_object['is_404'] && $current_object['template'] === 'attachment' ) {
				$current_object['attach']      = [];
				$current_object['attachid']    = 0;
				$current_object['attach_slug'] = '';

				$slug_indx                      = ( count( $current_object['qvars'] ) > 1 && wpfval( $current_object['qvars'], 0 ) === 'thumb' ) ? 1 : 0;
				$attach_slug                    = (string) wpfval( $current_object['qvars'], $slug_indx );
				$current_object['attach_thumb'] = ( $slug_indx === 1 );

				if( $attach_slug && ( $attach = $this->get_attach( [ 'slug' => $attach_slug ] ) ) ) {
					$current_object['attach']      = $attach;
					$current_object['attachid']    = $attach['attachid'];
					$current_object['attach_slug'] = $attach['slug'];
				}
				if( ! $current_object['attachid'] ) {
					$current_object['is_404'] = true;
				}
			}

			return $current_object;
		}

		private function get_requested_file() {
			if( WPF()->current_object['template'] === 'attachment' ) {
				if( ! $this->can_view() ) {
					header( 'HTTP/1.1 403 Forbidden' );
					die;
				}

				$uploader_class = WPF_ATTACH()->tools->init_uploader_class( false );
				$uploader_class->wpf_download( WPF()->current_object['attach'], WPF()->current_object['attach_thumb'] );
				die;
			}
		}

		public function settings_save() {
			check_admin_referer( 'wpforo_settings_save_wpforo-advanced-attachments' );

			if( wpfkey( $_POST, 'reset' ) ) {
				wpforo_delete_option( 'attach_options' );
				wpforo_delete_option( 'attach_phrases' );
			} else {
				$options = wp_unslash( $_POST['wpforo_attach_options'] );

				if( wpfval( $options, 'groups' ) ) {
					$options['groups'] = array_map( function( $go ) {
						$accepted_file_types = '';
						$explds              = array_filter( explode( '|', trim( $go['accepted_file_types'], '|' ) ) );
						if( ! empty( $explds ) ) $accepted_file_types = implode( '|', array_map( 'trim', $explds ) );
						$go['accepted_file_types'] = $accepted_file_types;

						$go['maximum_file_size'] = $go['maximum_file_size'] * 1024;
						if( $go['maximum_file_size'] > $this->options['server_maxs_min'] ) $go['maximum_file_size'] = $this->options['server_maxs_min'];

						$go['disable_delete']               = (int) $go['disable_delete'];
						$go['max_uploads_per_day']          = (int) $go['max_uploads_per_day'];
						$go['max_attachs_per_post']         = (int) $go['max_attachs_per_post'];
						$go['restrict_using_others_attach'] = (int) $go['restrict_using_others_attach'];

						return $go;
					}, $options['groups'] );
				}

				$options['boxed']                  = (int) $options['boxed'];
				$options['lightbox']               = (int) $options['lightbox'];
				$options['auto_upload']            = (int) $options['auto_upload'];
				$options['thumbnail_width']        = (int) $options['thumbnail_width'];
				$options['thumbnail_height']       = (int) $options['thumbnail_height'];
				$options['thumbnail_jpeg_quality'] = (int) $options['thumbnail_jpeg_quality'];
				$options['bigimg_max_height']      = (int) $options['bigimg_max_height'];
				$options['bigimg_jpeg_quality']    = (int) $options['bigimg_jpeg_quality'];
				$options['attachs_per_load']       = (int) $options['attachs_per_load'];
				$options['download_via_php']       = (int) $options['download_via_php'];

				$dummy_html      = WPF()->folders['attachments']['dir'] . DIRECTORY_SEPARATOR . 'index.html';
				$secure_htaccess = WPF()->folders['attachments']['dir'] . DIRECTORY_SEPARATOR . '.htaccess';
				if( wpfval( $options, 'download_via_php' ) ) {
					if( ! file_exists( $dummy_html ) ) file_put_contents( $dummy_html, "" );
					if( ! file_exists( $secure_htaccess ) ) file_put_contents( $secure_htaccess, "<Files *>\r\n\tOrder Allow,Deny\r\n\tDeny from All\r\n</Files>" );
				} else {
					if( file_exists( $dummy_html ) ) unlink( $dummy_html );
					if( file_exists( $secure_htaccess ) ) unlink( $secure_htaccess );
				}

				wpforo_update_option( 'attach_options', $options );
				wpforo_update_option( 'attach_phrases', wp_unslash( $_POST['wpforo_attach_phrases'] ) );
			}

			WPF()->notice->add( 'Done', 'success' );
			wp_safe_redirect( wp_get_raw_referer() );
			exit();
		}

		public function kses_allowed_html( $allowed_html ) {
			$allowed_html['figure'] = [
				'contenteditable' => true,
				'style'           => true,
				'data-*'          => true,
			];

			return $allowed_html;
		}

		public function add_custom_tag_to_editor( $settings ) {
			if( ! wpfkey( $settings, 'tinymce' ) ) $settings['tinymce'] = [];

			$extended_valid_elements                        = (string) wpfval( $settings['tinymce'], 'extended_valid_elements' );
			$extended_valid_elements                        .= ',figure[contenteditable|style|data*]';
			$extended_valid_elements                        = trim( $extended_valid_elements, ',' );
			$settings['tinymce']['extended_valid_elements'] = $extended_valid_elements;

			$settings['tinymce']['content_style'] .= 'figure[data-attachids] *{cursor: move !important;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids]{display: inline-block; cursor: move !important; margin: 5px;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids] img{max-width: 150px !important; max-height: 80px !important; display:block; margin: auto;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids] video{max-width: 300px !important; max-height: 200px !important; margin: auto;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids] audio{max-width: 300px !important; max-height: 50px !important; margin: auto;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids] a{color: #444; margin: auto; display: inline-block;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids] a *{vertical-align: super;}';
			$settings['tinymce']['content_style'] .= 'figure[data-attachids] .wpfa-file-icon{font-size: 2em; margin-right: 5px;}';

			return $settings;
		}

		public function filter_post_body( $args ) {
			if( wpfval( $args, 'body' ) ) $args['body'] = $this->wpfa_to_shortcode( $args['body'] );

			return $args;
		}

		public function filter_post_body_on_ajax_edit( $args ) {
			if( isset( $args['body'] ) && $args['body'] ) {
				$args['body'] = $this->tools->do_shortcodes( $args['body'] );
			}

			return $args;
		}

		public function filter_pm_body( $args ) {
			if( wpfval( $args, 'message' ) ) $args['message'] = $this->wpfa_to_shortcode( $args['message'] );

			return $args;
		}

		public function wpfa_to_shortcode( $text ) {
			return addslashes( preg_replace( '#<figure[^<>]*?data-attachids=[\'\"](\d+)[\'\"][^<>]*?>.*?</figure>#isu', '[attach]$1[/attach]', stripslashes( $text ) ) );
		}

		public function admin_css_js_enqueue() {
			$wpf_stylepath  = WPFOROATTACH_URL . '/assets/css/';
			$wpf_scriptpath = WPFOROATTACH_URL . '/assets/js/';
			wp_register_style( 'wpfattach-admin-style', $wpf_stylepath . 'admin.css', false, WPFOROATTACH_VERSION );
			wp_enqueue_style( 'wpfattach-admin-style' );
			wp_register_script( 'wpfattach-admin-script', $wpf_scriptpath . 'admin.js', [ 'jquery' ], WPFOROATTACH_VERSION, true );
			wp_enqueue_script( 'wpfattach-admin-script' );
		}

		public function lightbox_css_js_enqueue() {
			$wpf_stylepath  = WPFOROATTACH_URL . '/wpf-third-party/file-uploader/css/';
			$wpf_scriptpath = WPFOROATTACH_URL . '/wpf-third-party/file-uploader/js/';
			if( $this->options['lightbox'] ) {
				wp_register_style( 'blueimp-gallery-style', $wpf_stylepath . 'blueimp-gallery.min.css', false, WPFOROATTACH_VERSION );
				wp_enqueue_style( 'blueimp-gallery-style' );
				wp_register_script( 'jquery-blueimp-gallery-script', $wpf_scriptpath . 'jquery.blueimp-gallery.min.js', [ 'jquery' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'jquery-blueimp-gallery-script' );
			}
		}

		public function css_js_enqueue() {
			if( ! is_wpforo_attach_page() ) return;
			$wpf_scriptpath = WPFOROATTACH_URL . '/wpf-third-party/file-uploader/js/';
			$wpfa_js_path   = WPFOROATTACH_URL . '/assets/js/';
			$wpfa_css_path  = WPFOROATTACH_URL . '/assets/css/';

			$this->lightbox_css_js_enqueue();

			if( is_user_logged_in() ) {
				wp_register_script( 'wpfa_jquery_ui_1_12', WPFOROATTACH_URL . '/wpf-third-party/jquery-ui.min.js', [], '1.12.1', true );
				wp_register_script( 'wpfa_jquery-fileupload', $wpf_scriptpath . 'jquery.fileupload.js', [ 'wpfa_jquery_ui_1_12', 'wpforo-frontend-js' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_jquery-fileupload' );
				wp_register_script( 'wpfa_load-image-all', $wpf_scriptpath . 'load-image.all.min.js', [ 'wpfa_jquery-fileupload' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_load-image-all' );
				wp_register_script( 'wpfa_jquery-fileupload-process', $wpf_scriptpath . 'jquery.fileupload-process.js', [ 'wpfa_jquery-fileupload' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_jquery-fileupload-process' );
				wp_register_script( 'wpfa_jquery-fileupload-image', $wpf_scriptpath . 'jquery.fileupload-image.js', [ 'wpfa_jquery-fileupload' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_jquery-fileupload-image' );
				wp_register_script( 'wpfa_jquery-fileupload-audio', $wpf_scriptpath . 'jquery.fileupload-audio.js', [ 'wpfa_jquery-fileupload' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_jquery-fileupload-audio' );
				wp_register_script( 'wpfa_jquery-fileupload-video', $wpf_scriptpath . 'jquery.fileupload-video.js', [ 'wpfa_jquery-fileupload' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_jquery-fileupload-video' );

				wp_register_script( 'wpfa_wpfa-jquery-fileupload-ui', $wpfa_js_path . 'wpfa-jquery.fileupload-ui.js', [ 'wpfa_jquery-fileupload' ], WPFOROATTACH_VERSION, true );
				wp_enqueue_script( 'wpfa_wpfa-jquery-fileupload-ui' );

				wp_register_script( 'wpf_attach', $wpfa_js_path . 'attach.js', [ 'wpfa_wpfa-jquery-fileupload-ui' ], WPFOROATTACH_VERSION, true );
				wp_localize_script( 'wpf_attach', 'wpfaOptions', $this->options );
				wp_localize_script( 'wpf_attach', 'wpfaPhrases', $this->phrases );
				wp_enqueue_script( 'wpf_attach' );
			}
			wp_register_style( 'wpfa-style', $wpfa_css_path . 'style.css', false, WPFOROATTACH_VERSION );
			wp_enqueue_style( 'wpfa-style' );
		}

		public function add_dynamic_css( $css ) {
			$css .= "
                #wpforo #wpfa_dialog_wrap #wpfa_dialog{background-color: __WPFCOLOR_1__; border-color: __WPFCOLOR_8__;}
                .wpf-dark #wpforo #wpfa_dialog_wrap #wpfa_dialog{background-color: #2f2c2c;}
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-error .fa, 
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-error .fas, 
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-error .fab, 
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-error .far, 
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-error .fal, 
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-error, 
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-item-col-error{ color: __WPFCOLOR_42__; }
                #wpforo #wpfa_dialog .wpfa-button{ background-color: __WPFCOLOR_12__; border-color: __WPFCOLOR_14__; }
                #wpforo #wpfa_dialog .wpfa-button.wpfa-button-add-to-post, #wpfa_dialog .wpfa-button.wpfa-button-attach{ background-color: __WPFCOLOR_32__; border-color: __WPFCOLOR_33__; }
                #wpforo #wpfa_dialog .wpfa-button.wpfa-button-cancel{ background-color: __WPFCOLOR_5__; border-color: __WPFCOLOR_6__; }
                #wpforo #wpfa_dialog .wpfa-button.wpfa-button-delete{ background-color: __WPFCOLOR_41__; border-color: __WPFCOLOR_42__; }
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-dialog-item-row{border-color: __WPFCOLOR_8__;}
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-dialog-item-row:nth-child(odd) { background-color: __WPFCOLOR_9__; }
                #wpforo #wpfa_dialog #wpfa_dialog_items .wpfa-dialog-item-row:hover{background-color: __WPFCOLOR_17__;}
            ";

			return $css;
		}

		public function add_adminbar_links( $wp_admin_bar ) {
			if( wpforo_current_user_is( 'admin' ) ) {
				$args = [
					'id'     => 'wpfa-attachments',
					'title'  => __( 'Attachments', 'wpforo' ),
					'href'   => admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'advanced-attachments' ) ),
					'parent' => 'wpf-addons',
				];
				$wp_admin_bar->add_node( $args );
			}
		}

	}

	if( ! function_exists( 'WPF_ATTACH' ) ) {
		function WPF_ATTACH() {
			return wpForoAttachments::instance();
		}
	}

	require_once( WPFOROATTACH_DIR . "/includes/functions.php" );
}
