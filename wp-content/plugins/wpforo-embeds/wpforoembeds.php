<?php
/*
* Plugin Name: wpForo Embeds
* Plugin URI: http://wpForo.com
* Description: Allows to embed hundreds of video, social network, audio and photo content providers in forum topics and posts.
* Author: gVectors Team
* Author URI: http://gvectors.com/
* Version: 3.0.3
* Text Domain: wpforo_embed
* Domain Path: /languages
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
if( ! defined( 'WPFOROEMBED_VERSION' ) ) define( 'WPFOROEMBED_VERSION', '3.0.3' );
if( ! defined( 'WPFOROEMBED_WPFORO_REQUIRED_VERSION' ) ) define( 'WPFOROEMBED_WPFORO_REQUIRED_VERSION', '2.0.0' );

define( 'WPFOROEMBED_DIR', rtrim( str_replace( '//', '/', dirname( __FILE__ ) ), '/' ) );
define( 'WPFOROEMBED_URL', rtrim( plugins_url( '', __FILE__ ), '/' ) );
define( 'WPFOROEMBED_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPFOROEMBED_FOLDER', rtrim( plugin_basename( dirname( __FILE__ ) ), '/' ) );

function wpforo_embed_load_plugin_textdomain() { load_plugin_textdomain( 'wpforo_embed', false, basename( dirname( __FILE__ ) ) . '/languages/' ); }

add_action( 'plugins_loaded', 'wpforo_embed_load_plugin_textdomain' );

require_once WPFOROEMBED_DIR . "/includes/gvt-api-manager.php";
new GVT_API_Manager( __FILE__, 'wpforo-settings&wpf_tab=wpforo-embeds', 'wpforo_settings_page_top' );

if( ! class_exists( 'wpForoEmbeds' ) ) {
	class wpForoEmbeds {
		private static $_instance = null;
		private static $cache;

		public  $default;
		public  $options;
		public  $embeds;
		private $post_embeded_count;
		private $pattern_url_find;

		public static function instance() {
			if( is_null( self::$_instance ) ) self::$_instance = new self();

			return self::$_instance;
		}

		private function reset_cache() {
			self::$cache = [ 'wpforo_embed' => [], 'wp_oembed' => [], 'wpforo_oembed' => [] ];
		}

		private function set_cache( $type, $key, $value, $embeded = false ) {
			if( $embeded ) $this->post_embeded_count ++;
			self::$cache[ $type ][ md5( $key ) ] = [ 'value' => $value, 'embeded' => $embeded ];

			return $value;
		}

		private function get_cache( $type, $key ) {
			$value = wpfval( self::$cache, $type, md5( $key ) );
			if( $value && is_array( $value ) ) {
				$value = wpfval( $value, 'value' );
				if( wpfval( $value, 'embeded' ) ) $this->post_embeded_count ++;
			}

			return $value;
		}

		private function reset_post_embeded_count() {
			$this->post_embeded_count = 0;
		}

		private function __construct() {
			$this->pattern_url_find = '#(?:[^\'\"]|^)(https?://[^\s\'\"<>]+)(?:[^\'\"]|$)#iu';
			$this->reset_cache();
			$this->reset_post_embeded_count();

			$this->init_defaults();
			$this->init_options();

			add_action( 'wp_enqueue_scripts',                               [ &$this, 'css_js_enqueue' ] );
			add_filter( 'wpforo_dynamic_css_filter',                        [ &$this, 'add_dynamic_css' ] );

			add_filter( 'wpforo_settings_init_addons_info',                 [ &$this, 'init_settings_info' ] );
			add_action( 'wpforo_settings_after_form',                       [ &$this, 'vk_activator' ] );
			add_action( 'wpforo_action_wpforo_embeds_settings_save',        [ &$this, 'settings_save' ] );
			add_action( 'wpforo_action_wpforo_embeds_vk_make_access_token', [ &$this, 'vk_make_access_token' ] );
			add_action( 'wpforo_action_save_vk_access_token',               [ &$this, 'save_vk_access_token' ] );
			add_action( 'wpforo_action_reset_vk_api_codes',                 [ &$this, 'reset_vk_api_codes' ] );
			add_filter( 'wpforo_body_text_filter',                          [ &$this, 'do_body_text_filter' ] );
		}

		private function init_defaults() {
			$this->default = new stdClass;

			$this->default->options = [
				'video_width'       => 480,
				'video_width_type'  => 'px',
				'video_height'      => 320,
				'video_height_type' => 'px',
				'youtube_pe_mode'   => 0,
				'oembed_is_on'      => 1,
				'wpf_embed_is_on'   => 0,
				'embed_file_urls'   => 0,
				'max_per_post'      => 0,
				'own_wpposts_embed' => 0,
				'embeds'            => [],
				'vk_client_id'      => '',
				'vk_access_token'   => '',
			];

			$this->default->embeds = [
				'youtube.com'      => 1,
				'vimeo.com'        => 1,
				'dailymotion.com'  => 1,
				'rutube.ru'        => 1,
				'vevo.com'         => 1,
				'vesti.ru'         => 1,
				'metacafe.com'     => 1,
				'liveleak.com'     => 1,
				'myspace.com'      => 1,
				'funnyordie.com'   => 1,
				'dotsub.com'       => 1,
				'scribd.com'       => 1,
				'citytv.com.co'    => 1,
				'snotr.com'        => 1,
				'wat.tv'           => 1,
				'novamov.com'      => 1,
				'youku.com'        => 1,
				'v.qq.com'         => 1,
				'bilibili.com'     => 1,
				'xiami.com'        => 1,
				'music.163.com'    => 1,
				'putlocker.com'    => 1,
				'veoh.com'         => 1,
				'zappinternet.com' => 1,
				'dalealplay.com'   => 1,
				'flickr.com'       => 1,
				'zkouknito.cz'     => 1,
				'allocine.fr'      => 1,
				'break.com'        => 1,
				'vzaar.com'        => 1,
				'4shared.com'      => 1,
				'movshare.net'     => 1,
				'shiatv.net'       => 1,
				'useloom.com'      => 1,
				'ivoox.com'        => 1,
				'googleforms'      => 1,
				'hudl.com'         => 1,
				'instagram.com'    => 1,
				'ok.ru'            => 1,
				'tenor.com'        => 1,
				'bitchute.com'     => 1,
				'vk.com'           => 1,
				'facebook.com'     => 1,
				'audiomack.com'    => 1,
				'rumble.com'       => 1,
				'custom'           => 1

				//'google_360' => 1,
				//'mais.uol.com.br' => 1,
				//'viki.com' => 1,
				//'stagevu.com' => 1,
				//'clipshack.com' => 1,
				//'vxv.com' => 1,
				//'videozer.com' => 1,
				//'videobb.com' => 1,
				//'justin.tv' => 1,
				//'dorkly.com' => 1,
				//'facebook.com' => 1,
				//'yahoo.com' => 1,
				//'collegehumor.com' => 1,
				//'comedycentral.com' => 1,
				//'revver.com' => 1,
				//'clipfish.de' => 1,
				//'aniboom.com' => 1,
			];

		}

		private function init_options() {
			$this->options = wpforo_get_option( 'embed_options', $this->default->options );
			$this->embeds  = wpforo_array_args_cast_and_merge( $this->options['embeds'], $this->default->embeds );
		}

		public function init_settings_info( $addons ) {
			$addons['wpforo-embeds'] = [
				"title"                => esc_html__( "Embeds", "wpforo" ),
				"title_original"       => "Embeds",
				"icon"                 => '<img src="' . WPFORO_URL . '/assets/addons/embeds/header.png' . '" alt="wpForo Embeds Logo">',
				"description"          => __( 'Allows to embed hundreds of video, social network, audio and photo content providers in forum topics and posts.', 'wpforo' ),
				"description_original" => "Allows to embed hundreds of video, social network, audio and photo content providers in forum topics and posts.",
				"docurl"               => "",
				"status"               => "ok",
				"base"                 => false,
				"callback_for_page"    => function() {
					require WPFOROEMBED_DIR . "/includes/options.php";
				},
				"options"              => [

				],
			];

			return $addons;
		}

		private function get_width() {
			$vw = intval( $this->options['video_width'] );
			$w  = ( $vw ?: 'auto' );
			if( $w !== 'auto' ) $w .= ( $this->options['video_width_type'] === 'px' ? 'px' : '%' );

			return $w;
		}

		private function get_height() {
			$vh = intval( $this->options['video_height'] );
			$h  = ( $vh ?: 'auto' );
			if( $h !== 'auto' ) $h .= 'px';

			return $h;
		}

		private function get_per_post_limit() {
			if( $this->options['max_per_post'] ) {
				$diff = ( $this->options['max_per_post'] - $this->post_embeded_count );
				if( $diff > 0 ) {
					$limit = $diff;
				} else {
					$limit = null;
				}
			} else {
				$limit = - 1;
			}

			return $limit;
		}

		public function do_body_text_filter( $text ) {
			$this->reset_post_embeded_count();
			$text = $this->do_wpforo_embed( $text );
			if( $this->options['oembed_is_on'] ) $text = $this->do_wp_embed( $text );
			if( $this->options['wpf_embed_is_on'] || $this->options['embed_file_urls'] ) $text = $this->do_wpforo_oembed( $text );

			return preg_replace( '#(?:<a(?:\s+[^<>]*?)?>\s*)?<!--wpf_embed-->\s*(.+?)\s*<!--/wpf_embed-->(?:\s*</a>)?#isu', '$1', $text );
		}

		private function do_wpforo_embed( $text ) {
			$limit = $this->get_per_post_limit();
			if( ! is_null( $limit ) ) {
				$text = preg_replace_callback( $this->pattern_url_find, [ $this, 'get_wpforo_embed' ], $text, $limit );
			}

			return $text;
		}

		private function do_wpforo_oembed( $text ) {
			$limit = $this->get_per_post_limit();
			if( ! is_null( $limit ) ) {
				$text = preg_replace_callback( $this->pattern_url_find, [ $this, 'get_wpforo_oembed' ], $text, $limit );
			}

			return $text;
		}

		private function do_wp_embed( $text ) {
			$limit = $this->get_per_post_limit();
			if( ! is_null( $limit ) ) {
				$text = preg_replace_callback( $this->pattern_url_find, [ $this, 'get_wp_oembed' ], $text, $limit );
			}

			return $text;
		}

		/**
		 * @param array  $match
		 * @param string $w
		 * @param string $h
		 *
		 * @return string
		 */
		private function get_wpforo_embed( $match, $w = '', $h = '' ) {
			if( $this->embeds['tenor.com'] && strpos( $match[1], 'tenor.com' ) !== false ) wp_enqueue_script( 'wpf-embed-tenor-com' );

			if( $cache = $this->get_cache( 'wpforo_embed', $match[1] ) ) return $cache;

			if( ! $w ) $w = $this->get_width();
			if( ! $h ) $h = $this->get_height();
			$youtube_domain = ( $this->options['youtube_pe_mode'] ? 'youtube-nocookie.com' : 'youtube.com' );

			$values = [
				//youtube.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?youtube(?:\-nocookie)?\.com.+?v=([^\&\s\r\n\t\0<>]+)(?:(?:\?|\&|\&amp\;|\&\#38\;|\&\#63\;|\&quest\;)(?:t|start)=(\d+))?[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" src="//www.' . $youtube_domain . '/embed/$1?start=$2" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>',
					'youtube.com',
				],
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?youtu\.be\/([^\s\r\n\t\0<>\?]+)(?:(?:\?|\&|\&amp\;|\&\#38\;|\&\#63\;|\&quest\;)(?:t|start)=(\d+))?[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" src="//www.' . $youtube_domain . '/embed/$1?start=$2" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>',
					'youtube.com',
				],
				//youtube.com playlist
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?youtube(?:\-nocookie)?\.com\/playlist\?list=([^&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" src="//www.' . $youtube_domain . '/embed/videoseries?list=$1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>', 'youtube.com' ],
				//dailymotion.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?dailymotion\.com\/video\/([^_\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe frameborder="0" style="width: ' . $w . '; height: ' . $h . '" src="//www.dailymotion.com/embed/video/$1" loading="lazy"></iframe></div>', 'dailymotion.com' ],
				//metacafe.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?metacafe\.com\/watch\/([^&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" src="http://www.metacafe.com/embed/$1" frameborder="0" allowfullscreen loading="lazy"></iframe></div>', 'metacafe.com' ],
				//myspace.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?myspace\.com.+?videoID=([^&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object style="width: ' . $w . '; height: ' . $h . '" ><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent"/><param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m=$1,t=1,mt=video"><embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m=$1,t=1,mt=video" style="width: ' . $w . '; height: ' . $h . '" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"></object></div>',
					'myspace.com',
				],
				//vids.myspace.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?vids\.myspace\.com.+?videoID=([^&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object style="width: ' . $w . '; height: ' . $h . '" ><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent"/><param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m=$1,t=1,mt=video"><embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m=$1,t=1,mt=video" style="width: ' . $w . '; height: ' . $h . '" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"></object></div>',
					'myspace.com',
				],
				//myspacetv.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?myspacetv\.com.+?videoID=([^&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object style="width: ' . $w . '; height: ' . $h . '"><param name="wmode" value="transparent"/><param name="allowscriptaccess" value="always"/><param name="movie" value="http://lads.myspace.com/videos/vplayer.swf"/><param name="flashvars" value="m=$1"><embed src="http://lads.myspace.com/videos/vplayer.swf" style="width: ' . $w . '; height: ' . $h . '" flashvars="m=$1" wmode="transparent" type="application/x-shockwave-flash" allowscriptaccess="always"></object></div>',
					'myspace.com',
				],
				//myspace.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?myspace\.com.*?/video/([^/\s\r\n\t\0<>]+)/(\d+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" src="http://media.myspace.com/play/video/$1-$2" frameborder="0" allowtransparency="true" webkitallowfullscreen mozallowfullscreen allowfullscreen loading="lazy"></iframe></div>', 'myspace.com' ],
				//vimeo.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?vimeo\.com\/(?:.*?\/*)*([\d]+)(?:/([^/\s<>\'\"]+))?[^\s\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="//player.vimeo.com/video/$1?h=$2" style="width: ' . $w . '; height: ' . $h . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen loading="lazy"></iframe></div>', 'vimeo.com' ],
				//funnyordie.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?funnyordie\.com\/videos\/(.*)\/[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object style="width: ' . $w . '; height: ' . $h . '" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="ordie_player_ef76e75bcf"><param name="wmode" value="transparent"><param name="movie" value="http://player.ordienetworks.com/flash/fodplayer.swf"><param name="flashvars" value="key=$1"><param name="allowfullscreen" value="true"><param name="allowscriptaccess" value="always"><embed style="width: ' . $w . '; height: ' . $h . '" flashvars="key=$1" wmode="transparent" allowfullscreen="true" allowscriptaccess="always" quality="high" src="http://player.ordienetworks.com/flash/fodplayer.swf" name="ordie_player_$1" type="application/x-shockwave-flash"></object></div>',
					'funnyordie.com',
				],
				//dotsub.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?dotsub\.com\/view\/([^/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="https://dotsub.com/media/$1/embed/" frameborder="0" style="width: ' . $w . '; height:' . $h . '" AllowFullScreen loading="lazy"></iframe></div>', 'dotsub.com' ],
				//scribd.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?s?cribd\.com\/doc\/([^\/]*)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe class="scribd_iframe_embed" src="http://www.scribd.com/embeds/$1/content?start_page=1&view_mode=list&access_key=key-1evz4wg4vd1fxbt8udvc" data-auto-height="true" data-aspect-ratio="0.707514450867052" scrolling="no" id="doc_49419" style="width: ' . $w . '; height: ' . $h . '" frameborder="0" loading="lazy"></iframe><script type="text/javascript">(function() { const scribd = document.createElement("script"); scribd.type = "text/javascript"; scribd.async = true; scribd.src = "http://www.scribd.com/javascripts/embed_code/inject.js"; const s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(scribd, s); })();</script></div>',
					'scribd.com',
				],
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?scribd\.com/(document/(\d+)/[^\&\s\r\n\t\0<>/]+)[^\s\r\n\t\'\"<>]*#isu',
					'<p style="margin: 12px auto 6px auto; font-family: Helvetica,Arial,sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; display: block;"><a title="View on Scribd" href="https://www.scribd.com/$1#from_embed" style="text-decoration: underline;">View on Scribd...</a> by <a title="View \'s profile on Scribd" href="undefined#from_embed"  style="text-decoration: underline;"></a> on Scribd</p><iframe class="scribd_iframe_embed" title="View on Scribd" src="https://www.scribd.com/embeds/$2/content?start_page=1&view_mode=scroll&show_recommendations=true" data-auto-height="true" data-aspect-ratio="null" scrolling="no" width="100%" height="600" frameborder="0" loading="lazy"></iframe>',
					'scribd.com',
				],
				//citytv.com.co
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?citytv\.com\.co\/videos\/([^/]+)[^\&\s\r\n\t\0<>]*[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height: ' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" frameborder="0" allowfullscreen src="//player.ooyala.com/static/v4/stable/4.6.9/skin-plugin/iframe.html?ec=$1&pbid=3b0393a8b9c84ae8b0ef22288c8cd5de&pcode=JhNjExOtY42PDVgI6QVp7QFXwmQS" loading="lazy"></iframe></div>', 'citytv.com.co' ],
				//snotr.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?snotr\.com\/video\/(.*)\/[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://www.snotr.com/embed/$1" style="width: ' . $w . '; height: ' . $h . '" frameborder="0" loading="lazy"></iframe></div>', 'snotr.com' ],
				// wat.tv  (par Antoine)
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?wat\.tv\/swf2\/[0-9A-Za-z]{19}[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"> <object style="width: ' . $w . '; height: ' . $h . '"><param name="movie" value="http://www.wat.tv/swf2/$1" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><embed src="http://www.wat.tv/swf2/$1" type="application/x-shockwave-flash" style="width: ' . $w . '; height: ' . $h . '" allowScriptAccess="always" allowFullScreen="true"></object> </div>',
					'wat.tv',
				],
				//videozer.com
				//			array ('#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?videozer\.com\/video\/(.*)[^\s\r\n\t\'\"<>]*#isu','<div class="wpf-embed" style="width: '.$w.'; height:'.$h.'"><object id="player" style="width: '.$w.'; height: '.$h.'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ><param name="movie" value="http://videozer.com/embed/$1" ></param><param name="allowFullScreen" value="true" ></param><param name="allowscriptaccess" value="always"></param><param name=wmode value=transparent></param><embed src="http://videozer.com/embed/$1" type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent" allowfullscreen="true" style="width: '.$w.'; height: '.$h.'"></embed></object></div>', 'videozer.com'),
				//novamov.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?novamov\.com\/video\/(.*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="overflow: hidden; border: 0; width: ' . $w . 'px; height: ' . $h . 'px" src="http://embed.novamov.com/embed.php?width=' . $w . '&height=' . $h . '&v=$1&px=1" scrolling="no" loading="lazy"></iframe></div>', 'novamov.com' ],
				//youku.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?vo?\.youku\.com\/v_show\/id_(.*?)\.html[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height:' . $h . '" src="http://player.youku.com/embed/$1" frameborder=0 allowFullScreen="true" loading="lazy"></iframe></div>', 'youku.com' ],
				//v.qq.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?v\.qq\.com/x/(?:page|cover/[^\s/\?\&]+)/([^\s/\?\&]+)\.html[^\s\'\"<>]*#iu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height:' . $h . '" src="//v.qq.com/txp/iframe/player.html?vid=$1" frameborder=0 allowFullScreen="true" loading="lazy"></iframe></div>', 'v.qq.com' ],
				//bilibili.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?(?:bilibili\.com/video/av|acg\.tv/)(\d+)[^\s\'\"<>]*#iu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height:' . $h . '; max-width: 100%;" src="//player.bilibili.com/player.html?aid=$1" scrolling="no" frameborder="no" allowfullscreen="true" loading="lazy"></iframe></div>', 'bilibili.com' ],
				[ '#[^\s\'\"<>]*https?://(?:www\.)?bilibili\.com/video/(BV[^\s/\?\&]+)[^\s\'\"<>]*#iu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height:' . $h . '; max-width: 100%;" src="//player.bilibili.com/player.html?bvid=$1" scrolling="no"  frameborder="no" allowfullscreen="true" loading="lazy"></iframe></div>', 'bilibili.com' ],
				//xiami.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?xiami\.com/song/(\w+)[^\s\'\"<>]*#iu', '<div class="wpf-embed"><iframe style="max-width: 100%;" frameborder="no" marginwidth="0" marginheight="0" sandbox="allow-popups allow-scripts allow-same-origin" src="//www.xiami.com/webapp/embed-player?id=$1" loading="lazy"></iframe></div>', 'xiami.com' ],
				//music.163.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?music\.163\.com/\#/song\?id=(\d+)[^\s\'\"<>]*#iu', '<div class="wpf-embed"><iframe style="max-width: 100%;" frameborder="no" marginwidth="0" marginheight="0" src="//music.163.com/outchain/player?type=2&auto=0&height=90&id=$1" loading="lazy"></iframe></div>', 'music.163.com' ],
				//putlocker.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?putlocker\.com\/file\/(.*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://www.putlocker.com/embed/$1" style="width: ' . $w . '; height: ' . $h . '" frameborder="0" scrolling="no" loading="lazy"></iframe></div>', 'putlocker.com' ],
				//uploadc.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?uploadc\.com\/([^\/]*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><IFRAME SRC="http://www.uploadc.com/embed-$1.html" FRAMEBORDER=0 MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO width=' . $w . ' height=' . $h . ' loading="lazy"></IFRAME></div>', 'uploadc.com' ],
				//veoh.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?veoh\.com\/watch\/(.*)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object width="410" height="341" id="veohFlashPlayer" name="veohFlashPlayer"><param name="movie" value="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1144&permalinkId=$1&player=videodetailsembedded&videoAutoPlay=0&id=anonymous"><param name="allowFullScreen" value="true"><param name="wmode" value="transparent"><param name="allowscriptaccess" value="always"><embed src="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1144&permalinkId=$1&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" style="width: ' . $w . '; height: ' . $h . '" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed"></object></div>',
					'veoh.com',
				],
				//zappinternet.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?zappinternet\.com\/video\/([^/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://zappinternet.com/embed/$1" style="width: ' . $w . '; height:' . $h . '" frameborder="0" scrolling="no" id="zappinternet_iframe" loading="lazy"></iframe></div>', 'zappinternet.com' ],
				//liveleak.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?liveleak\.com.*=(.*)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object style="width: ' . $w . '; height: ' . $h . '"><param name="movie" value="http://www.liveleak.com/e/$1"><param name="wmode" value="transparent"><param name="allowscriptaccess" value="always"><embed src="http://www.liveleak.com/e/$1" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" style="width: ' . $w . '; height: ' . $h . '"></object></div>',
					'liveleak.com',
				],
				//dalealplay.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?dalealplay\.es.*?([\d]+)$#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe frameborder="0" marginwidth="0" marginheight ="0" id="videodap" scrolling="no" style="width: ' . $w . '; height:' . $h . '" src="http://www.dalealplay.es/videos/frame/$1/dapembed" loading="lazy"></iframe></div>', 'dalealplay.com' ],
				//flickr.com
				//                array ('#[^\s\r\n\t\'\"<>]*(https?://(?:www\.)?flickr\.com\/photos\/[^\s\r\n\t\'\"<>]*)#isu','<div class="wpf-embed" style="width: 478px; height:358px;"><a data-flickr-embed="true" data-header="true" data-footer="true" data-context="true"  href="$1" title="Flickr"><img src="https://c6.staticflickr.com/5/4072/4543966637_002e0dca9c.jpg" alt="Flickr"></a><script async src="//embedr.flickr.com/assets/client-code.js" charset="utf-8"></script></div>', 'flickr.com'),
				//rutube.ru
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?rutube\.ru.*?v=([^\&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><OBJECT style="width: ' . $w . '; height: ' . $h . '"><PARAM name="movie" value="http://video.rutube.ru/$1"><PARAM name="wmode" value="transparent"><PARAM name="allowFullScreen" value="true"><EMBED src="http://video.rutube.ru/$1" type="application/x-shockwave-flash" wmode="window" style="width: ' . $w . '; height: ' . $h . '" allowFullScreen="true"></OBJECT></div>',
					'rutube.ru',
				],
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?rutube\.ru/video/([^/\&\s\r\n\t\0<>]+)(?:[^\s\r\n\t\0<>]*)?[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="width: ' . $w . '; height: ' . $h . '" src="//rutube.ru/play/embed/$1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen loading="lazy"></iframe></div>', 'rutube.ru' ],
				//zkouknito.cz
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?zkouknito\.cz\/video_([^/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://www.zkouknito.cz/videoembed_$1" style="width: ' . $w . '; height:' . $h . '" frameborder="0" scrolling="no" loading="lazy"></iframe></div>', 'zkouknito.cz' ],
				//allocine.fr
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?allocine\.fr/.*?cmedia=([^&]+)(?:[^\s\r\n\t\0<>]*)?[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://www.allocine.fr/_video/iblogvision.aspx?cmedia=$1" style="width:' . $w . '; height:' . $h . '" loading="lazy"></iframe></div>', 'allocine.fr' ],
				//vesti.ru
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?player\.vgtrk\.com/iframe/video/id/([\d]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe allowfullscreen frameborder="0" style="width: ' . $w . '; height:' . $h . '" src="//player.vgtrk.com/iframe/video/id/$1/start_zoom/true/showZoomBtn/false/sid/vesti/isPlay/false/" loading="lazy"></iframe></div>', 'vesti.ru' ],
				//xhamster.com
				//array ('#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?xhamster\.com\/movies\/([^\/]*)[^\s\r\n\t\'\"<>]*#isu','<div class="wpf-embed" style="width: '.$w.'; height:'.$h.'"><iframe style="width: '.$w.'; height: '.$h.'" src="http://xhamster.com/xembed.php?video=$1" frameborder="0" scrolling="no" loading="lazy"></iframe></div>', 'xhamster.com'),
				//break.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?break\.com\/.*?\/.*-(.*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object type="application/x-shockwave-flash" data="http://embed.break.com/$1" style="width: ' . $w . '; height: ' . $h . '" wmode="transparent"><param name="movie" value="http://embed.break.com/$1" /></object></div>', 'break.com' ],
				//vzaar.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?vzaar\.com\/videos\/(.*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe allowFullScreen class="vzaar-video-player" frameborder="0" height="' . $h . '" id="vzvd-$1" name="vzvd-$1" src="http://view.vzaar.com/$1/player" title="vzaar video player" type="text/html" width="' . $w . '" loading="lazy"></iframe></div>', 'vzaar.com' ],
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?vzaar\.tv\/(.*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe allowFullScreen class="vzaar-video-player" frameborder="0" height="' . $h . '" id="vzvd-$1" name="vzvd-$1" src="http://view.vzaar.com/$1/player" title="vzaar video player" type="text/html" width="' . $w . '" loading="lazy"></iframe></div>', 'vzaar.com' ],
				//4shared.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?4shared\.com/video/([^/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://www.4shared.com/web/embed/file/$1" frameborder="0" scrolling="no" style="width: ' . $w . '; height:' . $h . '" loading="lazy"></iframe></div>', '4shared.com' ],
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?4shared\.com/mp3/([^/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe src="http://www.4shared.com/web/embed/audio/file/$1?type=NORMAL&widgetWidth=530&showArtwork=true&playlistHeight=0&widgetRid=781214449151" style="overflow:hidden;height:152px;width:' . $w . ';border: 0;margin:0;" loading="lazy"></iframe></div>', '4shared.com' ],
				//movshare.net
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?movshare\.net\/video\/(.*)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="overflow: hidden; border: 0; width: ' . $w . 'px; height: ' . $h . 'px" src="http://embed.movshare.net/embed.php?v=$1&width=' . $w . '&height=' . $h . '&color=black" scrolling="no" loading="lazy"></iframe></div>', 'movshare.net' ],
				//vevo.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?vevo\.com\/watch\/.*?\/.*?\/([^?]*)[^\s\r\n\t\'\"<>]*#isu',
					'<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><object style="width: ' . $w . '; height: ' . $h . '"><param name="movie" value="http://videoplayer.vevo.com/embed/Embedded?videoId=$1&playlist=false&autoplay=0&playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961&playerType=embedded&env=0&cultureName=en-US&cultureIsRTL=False"><param name="wmode" value="transparent"><param name="bgcolor" value="#000000"><param name="allowFullScreen" value="true"><param name="allowScriptAccess" value="always"><embed src="http://videoplayer.vevo.com/embed/Embedded?videoId=$1&playlist=false&autoplay=0&playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961&playerType=embedded&env=0&cultureName=en-US&cultureIsRTL=False" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" style="width: ' . $w . '; height: ' . $h . '" bgcolor="#000000" wmode="transparent"></object></div>',
					'vevo.com',
				],
				//shiatv.net
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?shiatv\.net\/video/([^/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe webkitallowfullscreen="true" mozallowfullscreen="true" allowfullscreen="true" src="http://www.shiatv.net/embed.php?viewkey=$1&autoplay=0" style="width: ' . $w . '; height:' . $h . '" loading="lazy"></iframe></div>', 'shiatv.net' ],

				[ '#[^\s\r\n\t\'\"<>]*(https?://(?:www\.)?google\.com/maps/embed\?pb=[^\&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="$1" frameborder="0" allowfullscreen loading="lazy"></iframe></div>', 'google_360' ],
				//useloom.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?(?:use)?loom\.com/(?:share|embed)/([^\&\s\r\n\t\0<>/]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="//www.loom.com/embed/$1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen loading="lazy"></iframe></div>', 'useloom.com' ],
				//ivoox.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?ivoox\.com[^\s\r\n\t\'\"<>]*?/(\d+)/?[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe id="audio_$1" frameborder="0" allowfullscreen="" scrolling="no" style="border:1px solid #EEE; box-sizing:border-box; width: ' . $w . '; height: ' . $h . '" src="//www.ivoox.com/en/player_ej_$1_4_1.html?c1=ff6600" loading="lazy"></iframe></div>', 'ivoox.com' ],
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?ivoox\.com[^\s\r\n\t\'\"<>]*?_rf_(\d+)_\d*\.html[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe id="audio_$1" frameborder="0" allowfullscreen="" scrolling="no" style="border:1px solid #EEE; box-sizing:border-box; width: ' . $w . '; height: ' . $h . '" src="//www.ivoox.com/en/player_ej_$1_4_1.html?c1=ff6600" loading="lazy"></iframe></div>', 'ivoox.com' ],
				//google forms
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?docs\.google\.com/forms/(?:\w+/)*?([^\&\s\r\n\t\0<>/]+)/viewform\?[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="//docs.google.com/forms/d/e/$1/viewform?embedded=true" frameborder="0" marginheight="0" marginwidth="0" loading="lazy">Loading...</iframe></div>', 'googleforms' ],
				//hudl.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?hudl\.com/(video/[^\&\s\r\n\t\0<>]+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="//www.hudl.com/embed/$1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen loading="lazy"></iframe></div>', 'hudl.com' ],
				//instagram.com
				[
					'#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?instagram\.com/p/([^\&\s\r\n\t\0<>/]+)[^\s\r\n\t\'\"<>]*#isu',
					'<blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/$1/?utm_source=ig_embed&amp;utm_medium=loading" data-instgrm-version="12" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/p/$1/?utm_source=ig_embed&amp;utm_medium=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div><div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;"> View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 13px; width: 13px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 13px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 13px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 13px; width: 13px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style="width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> </a></div></blockquote> <script async src="//www.instagram.com/embed.js"></script>',
					'instagram.com',
				],
				//ok.ru
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?(?:ok|odnoklassniki)\.ru/video/(\d+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="//ok.ru/videoembed/$1" frameborder="0" allow="autoplay" webkitallowfullscreen mozallowfullscreen allowfullscreen  loading="lazy"></iframe></div>', 'ok.ru' ],
				//tenor.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?tenor\.com/view/[^\s\r\n\t\'\"<>]*?-(\d+)[^\s\r\n\t\'\"<>]*#isu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><div class="tenor-gif-embed" data-postid="$1" data-share-method="host" data-width="' . $w . '"></div></div>', 'tenor.com' ],
				//bitchute.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?bitchute\.com/video/([^/]+)[^\s\'\"<>]*#iu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="//www.bitchute.com/embed/$1/" scrolling="no" frameborder="0" allow="autoplay" webkitallowfullscreen mozallowfullscreen allowfullscreen  loading="lazy"></iframe></div>', 'bitchute.com' ],
				//facebook.com
				[ '#[^\s\r\n\t\'\"<>]*(https?://(?:www\.)?(?:facebook\.com|fb.watch)\/[^\s\r\n\t\'\"<>]*)#isu', '', 'facebook.com', 'callback' => [ $this, 'fb_com' ] ],
				//vk.com
				[ '#[^\s\r\n\t\'\"<>]*https?://(?:www\.)?(?:vk\.com|vkontakte\.ru)/(?:video\?z=)?video(?P<videos>(?P<owner_id>-\d+)_(?P<id>\d+))[^\s\r\n\t\'\"<>]*#isu', '', 'vk.com', 'callback' => [ $this, 'vk_com' ] ],
				//audiomack.com
				[ '#[^\s\'\"<>]*https?://(?:www\.)?audiomack\.com/([^/\s\'\"]+?)/(song|album|playlist)/([^/\s\'\"]+)[^\s\'\"<>]*#iu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe style="border:0; width: ' . $w . '; height: ' . $h . '" src="https://audiomack.com/embed/$2/$1/$3?background=1" scrolling="no" scrollbars="no" frameborder="0" loading="lazy"></iframe></div>', 'audiomack.com' ],
				//rumble.com
                [ '#[^\s\'\"<>]*https?:(//rumble\.com/embed/[^/]+/(?:\?pub=\d+)?)[^\s\'\"<>]*#iu', '<div class="wpf-embed" style="width: ' . $w . '; height:' . $h . '"><iframe class="rumble" style="border: 0; width: ' . $w . '; height: ' . $h . '" src="$1" frameborder="0" allowfullscreen loading="lazy"></iframe></div>', 'rumble.com' ],
			];

			$values = apply_filters( 'wpforoembed_get_wpforo_embed_regexp', $values, $w, $h );

			$decoded_url = str_replace( ' ', '+', urldecode( $match[1] ) );
			if( wpforo_is_url_external( $decoded_url ) ) {
				foreach( $values as $value ) {
					if( ! wpfval( $this->embeds, $value[2] ) ) continue;
					if( $callback = wpfval( $value, 'callback' ) ) {
						$embed_code = preg_replace_callback( $value[0], $callback, $decoded_url );
					} else {
						$embed_code = preg_replace( $value[0], $value[1], $decoded_url );
					}
					if( $embed_code != $decoded_url || ( $this->options['embed_file_urls'] && $embed_code = $this->build_html( $match[1] ) ) ) {
						return $this->set_cache( 'wpforo_embed', $match[1], str_replace( $match[1], "<!--wpf_embed--><br/><br/>$embed_code<br/><br/><!--/wpf_embed-->", $match[0] ), true );
					}
				}
			}

			return $this->set_cache( 'wpforo_embed', $match[1], $match[0] );
		}

		private function get_wpforo_oembed( $match ) {
			if( $cache = $this->get_cache( 'wpforo_oembed', $match[1] ) ) return $cache;

			$decoded_url = str_replace( ' ', '+', urldecode( $match[1] ) );
			if( ( function_exists( 'wpforo_is_bot' ) && wpforo_is_bot() ) || ( ! ( $this->options['own_wpposts_embed'] && ! is_wpforo_page( $match[1] ) ) && wpforo_is_url_internal( $decoded_url ) ) ) return $this->set_cache( 'wpforo_oembed', $match[1], $match[0] );

			$domain = str_replace( 'www.', '', parse_url( $decoded_url, PHP_URL_HOST ) );
			if( $domain == 'youtu.be' ) $domain = 'youtube.com';
			if( wpfkey( $this->embeds, $domain ) && ! $this->embeds[ $domain ] ) return $this->set_cache( 'wpforo_oembed', $match[1], $match[0] );

			if( $remote_response = wp_remote_get( $decoded_url ) ) {
				if( $response_content_type = wp_remote_retrieve_header( $remote_response, 'Content-Type' ) ) {
					if( $this->options['wpf_embed_is_on'] && $embed_code = $this->build_html_from_remote_response( $match[1], $remote_response, $response_content_type ) ) {
						return $this->set_cache(
							'wpforo_oembed',
							$match[1],
							str_replace(
								$match[1],
								'<!--wpf_embed-->' . $embed_code . '<!--/wpf_embed-->',
								$match[0]
							),
							true
						);
					} elseif( $this->options['embed_file_urls'] && $embed_code = $this->build_html( $match[1], $response_content_type ) ) {
						return $this->set_cache(
							'wpforo_oembed',
							$match[1],
							str_replace(
								$match[1],
								'<!--wpf_embed-->' . $embed_code . '<!--/wpf_embed-->',
								$match[0]
							),
							true
						);
					}
				} else {
					$response_content_type = wp_remote_retrieve_header( wp_remote_head( $decoded_url ), 'Content-Type' );
					if( $this->options['embed_file_urls'] && $embed_code = $this->build_html( $match[1], $response_content_type ) ) {
						return $this->set_cache(
							'wpforo_oembed',
							$match[1],
							str_replace(
								$match[1],
								'<!--wpf_embed-->' . $embed_code . '<!--/wpf_embed-->',
								$match[0]
							),
							true
						);
					}
				}
			}

			return $this->set_cache( 'wpforo_oembed', $match[1], $match[0] );
		}

		private function get_wp_oembed( $match ) {
			if( $cache = $this->get_cache( 'wp_oembed', $match[1] ) ) return $cache;

			$decoded_url = str_replace( ' ', '+', urldecode( $match[1] ) );

			$provider = false;
			$oembed   = _wp_oembed_get_object();
            $oembed->providers['#https?://(www\.|vt\.)?tiktok\.com/[^/]+/?#iu'] = [
                'https://www.tiktok.com/oembed',
                true,
            ];
			foreach( $oembed->providers as $matchmask => $data ) {
				list( $providerurl, $regex ) = $data;

				// Turn the asterisk-type provider URLs into regex
				if( ! $regex ) {
					$matchmask = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $matchmask ), '#' ) ) . '#i';
					$matchmask = preg_replace( '|^#http\\\://|', '#https?\://', $matchmask );
				}

				if( preg_match( $matchmask, $decoded_url ) ) {
					$provider = str_replace( '{format}', 'json', $providerurl ); // JSON is easier to deal with than XML
					break;
				}
			}
			if( ! $provider ) {
				return $this->set_cache( 'wp_oembed', $match[1], $match[0] );
			}

			add_filter( 'pre_oembed_result', '__return_null' );

			if( ( function_exists( 'wpforo_is_bot' ) && wpforo_is_bot() ) || ( ! ( $this->options['own_wpposts_embed'] && ! is_wpforo_page( $match[1] ) ) && wpforo_is_url_internal( $decoded_url ) ) ) return $this->set_cache( 'wp_oembed', $match[1], $match[0] );

			$domain = str_replace( 'www.', '', parse_url( $decoded_url, PHP_URL_HOST ) );
			if( $domain == 'youtu.be' ) $domain = 'youtube.com';
			if( wpfkey( $this->embeds, $domain ) && ! $this->embeds[ $domain ] ) return $this->set_cache( 'wp_oembed', $match[1], $match[0] );

			if( $embed_code = wp_oembed_get( $decoded_url, [] ) ) return $this->set_cache( 'wp_oembed', $match[1], str_replace( $match[1], "<!--wpf_embed--><br/><br/>$embed_code<br/><br/><!--/wpf_embed-->", $match[0] ), true );

			return $this->set_cache( 'wp_oembed', $match[1], $match[0] );
		}

		private function build_html( $url, $content_type = null ) {
			$decoded_url = str_replace( ' ', '+', urldecode( $url ) );
			$domain      = str_replace( 'www.', '', parse_url( $decoded_url, PHP_URL_HOST ) );
			if( $domain === 'youtu.be' ) $domain = 'youtube.com';

			if( ! $content_type ) {
				$t = get_allowed_mime_types();
				unset( $t['htm|html'], $t['js'], $t['swf'], $t['exe'] );
				$t_filetype   = wp_check_filetype( preg_replace( '#([?&][^?&/=\r\n]*=?[^?&/=\r\n]*)(?1)*$#isu', '', $decoded_url ), $t );
				$content_type = $t_filetype['type'];
			}

			if( $content_type ) {
				$is_url_external = wpforo_is_url_external( $decoded_url );
				$rel             = $is_url_external ? ' rel="nofollow" ' : '';
				$target          = $is_url_external ? ' target="_blank" ' : '';

				if( strpos( $content_type, 'image/' ) !== false ) {
					return sprintf(
						'<div class="wpf-oembed-wrap"><img alt="" src="%1$s" %2$s></div>',
						$decoded_url,
						$rel
					);
				} elseif( strpos( $content_type, 'video/' ) !== false ) {
					return sprintf(
						'<div class="wpf-oembed-wrap"><video src="%1$s" %2$s controls></video></div>',
						$decoded_url,
						$rel
					);
				} elseif( strpos( $content_type, 'audio/' ) !== false ) {
					return sprintf(
						'<div class="wpf-oembed-wrap"><audio src="%1$s" %2$s controls></audio></div>',
						$decoded_url,
						$rel
					);
				} else {
					$embed_code = '<div class="wpf-oembed-body">';
					$favicon    = '';
					$u          = parse_url( $decoded_url );
					$fi         = $u['scheme'] . '://' . $u['host'] . '/favicon.ico';
					if( wp_remote_retrieve_response_code( wp_remote_head( $fi ) ) === 200 ) {
						$favicon = $fi;
					}
					if( $favicon ) $favicon = sprintf( '<img class="wpf-oembed-favicon" src="%1$s" alt="%2$s" %3$s>', esc_attr( $favicon ), strtoupper( $domain ), $rel );
					$embed_code .= sprintf( '<div class="wpf-oembed-domain">%1$s%2$s  <span class="wpf-oembed-url">"%3$s"</span></div>', $favicon, strtoupper( $domain ), $decoded_url );
					$embed_code .= '</div>';

					return sprintf(
						'<div class="wpf-oembed-wrap"><a href="%1$s" title="%2$s" %3$s %4$s>%5$s</a></div>',
						$decoded_url,
						strtoupper( $domain ),
						$rel,
						$target,
						$embed_code
					);
				}
			}

			return null;
		}

		private function build_html_from_remote_response( $url, $remote_response, $content_type = null ) {
			$decoded_url = str_replace( ' ', '+', urldecode( $url ) );
			$domain      = str_replace( 'www.', '', parse_url( $decoded_url, PHP_URL_HOST ) );
			if( $domain === 'youtu.be' ) $domain = 'youtube.com';

			if( ! $content_type ) $content_type = wp_remote_retrieve_header( $remote_response, 'Content-Type' );
			$response_code = wp_remote_retrieve_response_code( $remote_response );
			$response_body = wp_remote_retrieve_body( $remote_response );

			if( strpos( $content_type, 'text/html' ) !== false ) {
				$is_url_external = wpforo_is_url_external( $decoded_url );
				$rel             = $is_url_external ? ' rel="nofollow" ' : '';
				$target          = $is_url_external ? ' target="_blank" ' : '';

				$link_alt   = strtoupper( $domain );
				$embed_code = '';
				if( $response_code === 200 && preg_match( '#<meta[^<>]*?property=[\'\"]og:image[\'\"][^<>]*?content=[\'\"]([^\'\"]+?)[\'\"]#iu', $response_body, $meta ) ) {
					$embed_code .= sprintf( '<div class="wpf-oembed-head"><img alt="" class="wpf-oembed-img" src="%1$s" %2$s></div>', esc_attr( $meta[1] ), $rel );
				}
				$embed_code       .= '<div class="wpf-oembed-body">';
				$favicon          = '';
				$u                = parse_url( $decoded_url );
				$favicon_pattern1 = '#<link[^<>]*?rel=[\'\"](?:[^\'\"]*\s)?icon(?:\s[^\'\"]*)?[\'\"][^<>]*?href=[\'\"]([^\'\"]+?)[\'\"]#iu';
				$favicon_pattern2 = '#<link[^<>]*?href=[\'\"]([^\'\"]+?)[\'\"][^<>]*?rel=[\'\"](?:[^\'\"]*\s)?icon(?:\s[^\'\"]*)?[\'\"]#iu';
				if( preg_match( $favicon_pattern1, $response_body, $meta ) || preg_match( $favicon_pattern2, $response_body, $meta ) ) {
					$favicon = trim( strip_tags( $meta[1] ) );
					if( strpos( $favicon, 'http' ) !== 0 && strpos( $favicon, '//' ) !== 0 ) {
						$favicon = $u['scheme'] . '://' . $u['host'] . '/' . $favicon;
					}
				} else {
					$fi = $u['scheme'] . '://' . $u['host'] . '/favicon.ico';
					if( wp_remote_retrieve_response_code( wp_remote_head( $fi ) ) === 200 ) {
						$favicon = $fi;
					}
				}
				if( $favicon ) $favicon = sprintf( '<img alt="" class="wpf-oembed-favicon" src="%1$s" %2$s>', esc_attr( $favicon ), $rel );
				$embed_code .= sprintf( '<div class="wpf-oembed-domain">%1$s%2$s  <span class="wpf-oembed-url">"%3$s"</span></div>', $favicon, strtoupper( $domain ), $decoded_url );

				if( $response_code === 200 && preg_match( '#<title(?:\s+[^<>]*?)?>[\r\n\t\s]*([^<>]+?)[\r\n\t\s]*</title>#iu', $response_body, $meta ) ) {
					$title      = preg_replace( $this->pattern_url_find, ' ', strip_tags( $meta[1] ) );
					$title      = esc_attr( trim( $title ) );
					$embed_code .= sprintf( '<div class="wpf-oembed-title">%1$s</div>', $title );
					$link_alt   = $title;
				}
				if( $response_code === 200 && preg_match( '#<meta[^<>]*?name=[\'\"]description[\'\"][^<>]*?content=[\'\"]([^\'\"]+?)[\'\"]#iu', $response_body, $meta ) ) {
					$embed_code .= sprintf( '<div class="wpf-oembed-description">%1$s</div>', $meta[1] );
				}
				$embed_code .= '</div>';

				return sprintf(
					'<div class="wpf-oembed-wrap"><a href="%1$s" title="%2$s" %3$s %4$s>%5$s</a></div>',
					$decoded_url,
					$link_alt,
					$rel,
					$target,
					$embed_code
				);
			}

			return null;
		}

		public function settings_save() {
			check_admin_referer( 'wpforo_settings_save_wpforo-embeds' );

			if( wpfkey( $_POST, 'reset' ) ) {
				wpforo_delete_option( 'embed_options' );
				WPF()->notice->add( 'Options reset successfully', 'success' );
			} else {
				$options                                 = $this->options;
				$_POST['wpforo_embed_options']['embeds'] = array_merge(
					array_map( '__return_zero', $this->default->embeds ),
					(array) $_POST['wpforo_embed_options']['embeds']
				);
				$options                                 = array_merge( $options, $_POST['wpforo_embed_options'] );
				wpforo_update_option( 'embed_options', $options );
				WPF()->notice->add( 'Options Saved', 'success' );
			}

			wp_safe_redirect( wp_get_raw_referer() );
			exit();
		}

		public function vk_make_access_token() {
			if( $vk_client_id = wpfval( $_POST, 'vk_client_id' ) ) {
				$make_access_token_url = 'https://oauth.vk.com/authorize?client_id=' . intval( $vk_client_id ) . '&scope=video,offline&response_type=token&redirect_uri=' . urlencode( wp_get_raw_referer() . '&wpfaction=save_vk_access_token&vk_client_id=' . intval( $vk_client_id ) );
				wp_redirect( $make_access_token_url );
			}
        }

		public function save_vk_access_token() {
			if( $vk_access_token = wpfval( $_GET, 'access_token' ) ) {
				$options                    = $this->options;
				$options['vk_client_id']    = (string) wpfval( $_GET, 'vk_client_id' );
				$options['vk_access_token'] = $vk_access_token;
				wpforo_update_option( 'embed_options', $options );
				WPF()->notice->add( 'Options Saved', 'success' );
				wp_safe_redirect( wp_get_raw_referer() );
				exit;
			}
        }

		public function reset_vk_api_codes() {
			$options                    = $this->options;
			$options['vk_client_id']    = '';
			$options['vk_access_token'] = '';
			wpforo_update_option( 'embed_options', $options );
			WPF()->notice->add( 'Options reset successfully', 'success' );
			wp_safe_redirect( wp_get_raw_referer() );
			exit;
        }

		private function fb_com( $match ) {
			$h = ceil( intval( $this->options['video_height'] ) );
			if( ! $h ) $h = 314;
			$w = ceil( $h * 560 / 314 );

			return '<div class="wpf-embed" style="width: ' . $w . 'px; height: ' . $h . 'px;"><iframe src="https://www.facebook.com/plugins/post.php?width=' . $w . '&height=' . $h . '&show_text=false&href=' . urlencode( $match[1] ) . '" style="width: ' . $w . 'px; height: ' . $h . 'px; border:none; overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true" loading="lazy"></iframe></div>';
		}

		private function vk_com( $match ) {
			if( $this->options['vk_access_token'] ) {
				$w = $this->get_width();
				$h = $this->get_height();

				$params = [
					'access_token' => $this->options['vk_access_token'],
					'owner_id'     => $match['owner_id'],
					'videos'       => $match['videos'],
					'offset'       => 0,
					'count'        => 1,
					'extended'     => 0,
					'v'            => '5.92',
				];

				$url      = 'https://api.vk.com/method/video.get?' . http_build_query( $params );
				$response = file_get_contents( $url );
				$response = json_decode( $response, true );

				if( $response = wpfval( $response, 'response', 'items', 0 ) ) {
					if( $r_player = wpfval( $response, 'player' ) ) {
						$html_format = '<div class="wpf-embed" style="width: %2$s; height: %3$s;"><iframe style="border:0; width: %2$s; height: %3$s;" src="%1$s" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen loading="lazy"></iframe></div>';
						parse_str( $r_player, $player );
						if( $hash = wpfval( $player, 'hash' ) ) {
							$src = '//vk.com/video_ext.php?oid=' . $match['owner_id'] . '&id=' . $match['id'] . '&hash=' . $hash . '&hd=auto';
						} else {
							$src = $r_player;
						}

						return sprintf( $html_format, $src, $w, $h );
					}
				}
			}

			return $match[0];
		}

		public function css_js_enqueue() {
			$wpfa_css_path = WPFOROEMBED_URL . '/assets/css/';
			wp_register_style( 'wpf-embed', $wpfa_css_path . 'embed.css', false, WPFOROEMBED_VERSION );
			wp_enqueue_style( 'wpf-embed' );
			wp_register_script( 'wpf-embed-tenor-com', 'https://tenor.com/embed.js', [], WPFOROEMBED_VERSION, true );
		}

		public function add_dynamic_css( $css ) {
			$css .= "\r\n #wpforo #wpforo-wrap .wpf-oembed-wrap{background-color: #ececec;}
	                \r\n #wpforo #wpforo-wrap.wpf-dark .wpf-oembed-wrap {background-color: #444444;}";

			return $css;
		}

		public function vk_activator( $tab ) {
			if( $tab !== 'wpforo-embeds' ) return;
			?>
			<!-- -START- VK API REGISTER -->
			<fieldset id="vk_register_api" style="margin-top: 25px; padding: 0 25px 15px;">
				<legend style="margin-left: 15px;"><img alt="" src="<?php echo WPFOROEMBED_URL ?>/assets/icons/vk.com.png" title="vk.com" style="vertical-align:middle; margin-right:5px; max-height: 20px;"> VK API REQUIRED SETTINGS</legend>
				<?php if( ! $this->options['vk_access_token'] ) : ?>

					<form method="POST">
                        <input type="hidden" name="wpfaction" value="wpforo_embeds_vk_make_access_token">
						<table>
							<tr>
								<td>
									<span>To start using VK video embedding feature you should get an Application ID. Please follow this </span>
									<a href="https://gvectors.com/forum/wpforo-embeds/how-to-get-vk-application-id-for-wpforo-embeds-addon-video-embedding/" target="_blank">instruction »</a>
								</td>
							</tr>
							<tr>
								<td>
									<label><input type="text" name="vk_client_id" placeholder="Application ID" style="height: 40px;font-weight: bold; padding: 3px 15px;"></label>
									<input type="submit" value="Make Access Token" style="height: 40px;font-weight: bold; padding: 3px 15px; cursor: pointer;">
								</td>
							</tr>
						</table>
					</form>
					<script type="text/javascript">
                        const pattern = /#access_token=/
                        if (pattern.test(window.location.hash)) {
                            window.location.replace(window.location.href.replace('#', '&'))
                        }

                        jQuery(document).ready(function ($) {
                            $('#vk_no_api').on('click', function () {
                                $('html, body').scrollTop($('#vk_register_api').offset().top - 25)
                                $('input[name="vk_client_id"]').trigger('focus')
                            })
                        })
					</script>
				<?php else : ?>
					<style type="text/css">
                        table#vk_api_access_codes tr:nth-child(odd) {
                            background: #f9f9f9;
                        }
					</style>
					<table id="vk_api_access_codes">
						<tr>
							<td>vk.com Application ID :</td>
							<td><?php echo $this->options['vk_client_id'] ?></td>
						</tr>
						<tr>
							<td>vk.com Access Token :</td>
							<td><?php echo $this->options['vk_access_token'] ?></td>
						</tr>
						<tr>
							<td colspan="2" align="right">
								<form method="post">
                                    <input type="hidden" name="wpfaction" value="reset_vk_api_codes">
									<input type="submit" value="change api settings">
								</form>
							</td>
						</tr>
					</table>
				<?php endif; ?>
			</fieldset>
			<!-- -END- VK API REGISTER -->
			<?php
		}
	}

	if( ! function_exists( 'WPF_EMBED' ) ) {
		function WPF_EMBED() {
			return wpForoEmbeds::instance();
		}
	}

}

add_action( 'wpforo_core_inited', function() {
	if( version_compare( WPFORO_VERSION, WPFOROEMBED_WPFORO_REQUIRED_VERSION, '>=' ) && wpforo_is_module_enabled( WPFOROEMBED_FOLDER ) ) {
		$GLOBALS['wpforoembeds'] = WPF_EMBED();
	}
} );

add_action( 'admin_notices', function() {
	if( ! function_exists( 'WPF' ) || ! version_compare( WPFORO_VERSION, WPFOROEMBED_WPFORO_REQUIRED_VERSION, '>=' ) ) {
		$class   = 'notice notice-error';
		$message = __( 'wpForo Embeds plugin Notice: Please activate required <a href="https://wpforo.com">latest version wpForo plugin</a> otherwise <b>wpForo Embeds</b> plugin will not work', 'wpforoembeds' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
} );
