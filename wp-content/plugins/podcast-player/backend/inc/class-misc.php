<?php
/**
 * Podcast player miscellaneous actions.
 *
 * @link       https://www.vedathemes.com
 * @since      1.0.0
 *
 * @package    Podcast_Player
 */

namespace Podcast_Player\Backend\Inc;

use Podcast_Player\Helper\Functions\Getters as Get_Fn;
use Podcast_Player\Helper\Functions\Utility as Utility_Fn;
use Podcast_Player\Helper\Core\Singleton;

/**
 * Display podcast player instance.
 *
 * @package    Podcast_Player
 * @author     vedathemes <contact@vedathemes.com>
 */
class Misc extends Singleton {
	/**
	 * Save feed episode images locally.
	 *
	 * @since 2.9.0
	 *
	 * @param string $fprint Feed footprint.
	 */
	public function save_images_locally( $fprint ) {
		$data_key = 'pp_feed_data_' . $fprint;
		$uploaded = false;

		// Get saved feed data.
		$feed_arr = get_option( $data_key );
		if ( ! $feed_arr ) {
			return;
		}

		set_time_limit( 540 ); // Give it 9 minutes.

		// Check and get podcast cover art image.
		if ( ! isset( $feed_arr['cover_id'] ) ) {
			if ( isset( $feed_arr['image'] ) && $feed_arr['image'] ) {
				$ctitle = isset( $feed_arr['title'] ) ? $feed_arr['title'] : '';
				$cid    = Utility_Fn::upload_image( $feed_arr['image'], $ctitle );
				if ( $cid ) {
					$feed_arr['cover_id'] = $cid;
					$uploaded             = true;
				}
			}
		}

		// Check and get podcast episodes featured images.
		$items      = $feed_arr['items'];
		$counter    = 0;
		$batch_size = 10;

		// Download images for 10 latest episodes.
		uasort(
			$items,
			function( $a, $b ) {
				return $a['date'] <= $b['date'];
			}
		);

		foreach ( $items as $item => $args ) {
			if ( $counter >= $batch_size ) {
				break;
			}
			if ( ! isset( $args['featured_id'] ) ) {
				if ( isset( $args['featured'] ) && $args['featured'] ) {
					$title = isset( $args['title'] ) ? $args['title'] : '';
					$id    = Utility_Fn::upload_image( $args['featured'], $title );
					if ( $id ) {
						$args['featured_id'] = $id;
						$items[ $item ]      = $args;
						$uploaded            = true;
						$counter++;
					}
				}
			}
		}

		if ( $uploaded ) {
			$feed_arr['items'] = $items;
			update_option( $data_key, $feed_arr, 'no' );
		}
	}

	/**
	 * Add plugin action links.
	 *
	 * Add actions links for better user engagement.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $links List of existing plugin action links.
	 * @return array         List of modified plugin action links.
	 */
	public function action_links( $links ) {
		$links = array_merge(
			array(
				'<a href="' . esc_url( admin_url( 'admin.php?page=pp-options' ) ) . '">' . __( 'Settings', 'podcast-player' ) . '</a>',
			),
			$links
		);

		if ( defined( 'PP_PRO_VERSION' ) ) {
			return $links;
		}

		$links = array_merge(
			array(
				'<a href="' . esc_url( 'https://vedathemes.com/podcast-player/' ) . '" style="color: #35b747; font-weight: 700;">' . __( 'Get Pro', 'podcast-player' ) . '</a>',
			),
			$links
		);
		return $links;
	}

	/**
	 * Auto Update Podcast.
	 *
	 * @since 5.8.0
	 *
	 * @param string $feed_key Podcast feed key.
	 */
	public function auto_update_podcast( $feed_key ) {

		// Return if podcast has been deleted from the index.
		$feed_key = Get_Fn::get_feed_url_from_index( $feed_key );
		if ( false === $feed_key ) {
			return;
		}

		// Init feed fetch and update method.
		Get_Fn::get_feed_data( $feed_key );
	}

	/**
	 * Create REST API endpoints to get all pages list.
	 *
	 * @since 1.8.0
	 */
	public function register_routes() {
		register_rest_route(
			'podcastplayer/v1',
			'/fIndex',
			array(
				'methods'             => 'GET',
				'callback'            => function() {
					$feed_index = Get_Fn::get_feed_index();
					if ( $feed_index && is_array( $feed_index ) && ! empty( $feed_index ) ) {
						array_walk(
							$feed_index,
							function( &$val, $key ) {
								$val = isset( $val['title'] ) ? $val['title'] : '';
							}
						);
						$feed_index = array_filter( $feed_index );
						return array_merge(
							array( '' => esc_html__( 'Select a Podcast', 'podcast-player' ) ),
							$feed_index
						);
					}
					return array();
				},
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
