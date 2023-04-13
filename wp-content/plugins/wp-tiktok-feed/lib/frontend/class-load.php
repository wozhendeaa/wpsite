<?php

namespace QuadLayers\TTF\Frontend;

use QuadLayers\TTF\Models\Feeds as Models_Feed;
use QuadLayers\TTF\Models\Settings as Models_Settings;

use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\User_Profile\Load as API_Rest_User_Profile;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\User_Video_List\Load as API_Rest_User_Video_List;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\Trending_Video_List\Load as API_Rest_Trending_Video_List;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\Hashtag_Video_List\Load as API_Rest_Hashtag_Video_List;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\External_User_Profile\Load as API_Rest_Frontend_External_Profile;
use QuadLayers\TTF\Api\Rest\Endpoints\Frontend\External_User_Video_List\Load as API_Rest_Frontend_External_Video_List;

/**
 * Frontend Class
 */
class Load {

	protected static $instance;

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'tiktok-feed', array( $this, 'do_shortcode' ) );
	}

	function enqueue_scripts() {

		$models_settings = new Models_Settings();

		$settings = $models_settings->get();

		$frontend = include_once QLTTF_PLUGIN_DIR . 'build/frontend/js/index.asset.php';

		/**
		 * Swiper
		 */
		wp_register_style( 'swiper', plugins_url( '/assets/frontend/swiper/swiper.min.css', QLTTF_PLUGIN_FILE ), null, QLTTF_PLUGIN_VERSION );
		wp_register_script( 'swiper', plugins_url( '/assets/frontend/swiper/swiper.min.js', QLTTF_PLUGIN_FILE ), array( 'jquery' ), QLTTF_PLUGIN_VERSION, true );

		/**
		 * Tiktok
		 */
		wp_register_style( 'qlttf-frontend', plugins_url( '/build/frontend/css/style.css', QLTTF_PLUGIN_FILE ), array(), QLTTF_PLUGIN_VERSION );
		wp_enqueue_script( 'qlttf-frontend', plugins_url( '/build/frontend/js/index.js', QLTTF_PLUGIN_FILE ), $frontend['dependencies'], $frontend['version'], true );

		wp_localize_script(
			'qlttf-frontend',
			'qlttf',
			array(
				'restRoutePaths' => array(
					'profile'  => array(
						'account'  => API_Rest_User_Profile::get_rest_url(),
						'username' => API_Rest_Frontend_External_Profile::get_rest_url(),
					),
					'account'  => API_Rest_User_Video_List::get_rest_url(),
					'hashtag'  => API_Rest_Hashtag_Video_List::get_rest_url(),
					'trending' => API_Rest_Trending_Video_List::get_rest_url(),
					'username' => API_Rest_Frontend_External_Video_List::get_rest_url(),
				),
				'settings'       => $settings,
			)
		);
	}

	function create_shortcode( $feed, $id = null ) {
		wp_enqueue_style( 'qlttf-frontend' );
		ob_start();
		?>
		<div id="tiktok-feed-feed-<?php echo esc_attr( $id ); ?>" class="tiktok-feed-feed" data-feed="<?php echo htmlentities( json_encode( $feed ), ENT_QUOTES, 'UTF-8' ); ?>">
		<!-- <FeedContainer/> -->	
		</div>
		<?php
		return ob_get_clean();
	}

	function do_shortcode( $atts, $content = null ) {

		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts
		);

		$id = absint( $atts['id'] );

		$models_feeds = new Models_Feed();
		$feed         = $models_feeds->get_by_id( $id );

		if ( ! isset( $feed['layout'] ) ) {
			return;
		}

		$feed_layout = $feed['layout'];

		if ( in_array( $feed_layout, array( 'masonry', 'highlight', 'highlight-square' ) ) ) {
			wp_enqueue_script( 'masonry' );
		}

		if ( strpos( $feed_layout, 'carousel' ) !== false ) {
			wp_enqueue_style( 'swiper' );
			wp_enqueue_script( 'swiper' );
		}

		return $this->create_shortcode( $feed, $id );

	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
