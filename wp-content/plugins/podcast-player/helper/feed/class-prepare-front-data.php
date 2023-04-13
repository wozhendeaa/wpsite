<?php
/**
 * Escape data and prepare to send to frontend.
 *
 * @link       https://www.vedathemes.com
 * @since      1.0.0
 *
 * @package    Podcast_Player
 * @subpackage Podcast_Player/Helper
 */

namespace Podcast_Player\Helper\Feed;

use Podcast_Player\Helper\Functions\Getters as Get_Fn;
use Podcast_Player\Helper\Core\Singleton;

/**
 * Escape data and prepare to send to frontend.
 *
 * @package    Podcast_Player
 * @subpackage Podcast_Player/Helper
 * @author     vedathemes <contact@vedathemes.com>
 */
class Prepare_Front_Data extends Singleton {

	/**
	 * Constructor method.
	 *
	 * @since  3.3.0
	 *
	 * @param array $data Fetched feed data to be sent to frontend.
	 */
	public function init( $data ) {
		array_walk(
			$data,
			function( &$val, $key ) {
				$callback = $this->get_channel_callback( $key );
				if ( $callback && is_callable( $callback ) ) {
					$val = call_user_func( $callback, $val );
				}
			}
		);
		return $data;
	}

	/**
	 * List of callbacks for podcast field.
	 *
	 * @since  5.7.0
	 *
	 * @param array $key Feed item field key.
	 */
	public function get_channel_callback( $key ) {

		/**
		 * List of callbacks for item fields.
		 *
		 * @since 5.7.0
		 *
		 * @param array $callbacks All feed items field callbacks.
		 */
		$callbacks = apply_filters(
			'podcast_player_frontend_channel_callback',
			array(
				'title'     => array( $this, 'title' ),
				'desc'      => array( $this, 'description' ),
				'podcats'   => array( $this, 'escape_podcats' ),
				'owner'     => array( $this, 'escape_owner' ),
				'items'     => array( $this, 'escape_data' ),
				'link'      => 'esc_url',
				'image'     => 'esc_url',
				'furl'      => 'esc_url',
				'fkey'      => 'esc_html',
				'copyright' => 'esc_html',
				'author'    => 'esc_html',
				'lastbuild' => 'absint',
			)
		);

		return isset( $callbacks[ $key ] ) ? $callbacks[ $key ] : '';
	}

	/**
	 * Escape podcast categories.
	 *
	 * @since  5.7.0
	 *
	 * @param array $cats Podcast Categories to be escaped and prepared.
	 */
	public function escape_podcats( $cats ) {
		foreach ( $cats as $key => $cat ) {
			if ( ! isset( $cat['label'] ) ) {
				continue;
			}
			$new_arr      = array(
				'label'   => esc_html( $cat['label'] ),
				'subcats' => array_map( 'esc_html', $cat['subcats'] ),
			);
			$cats[ $key ] = $new_arr;
		}
		return $cats;
	}

	/**
	 * Escape podcast owner details.
	 *
	 * @since  5.7.0
	 *
	 * @param array $owner Podcast owner details to be escaped and prepared.
	 */
	public function escape_owner( $owner ) {
		if ( ! $owner ) {
			return false;
		}
		$owner['name']  = isset( $owner['name'] ) ? esc_html( $owner['name'] ) : '';
		$owner['email'] = isset( $owner['email'] ) ? esc_html( $owner['email'] ) : '';
		return $owner;
	}

	/**
	 * Escape feed items.
	 *
	 * @since  3.3.0
	 *
	 * @param array $items Feed items to be escaped and prepared.
	 */
	public function escape_data( $items ) {
		return array_map( array( $this, 'escape_item' ), $items );
	}

	/**
	 * Escape feed item fields.
	 *
	 * @since  3.3.0
	 *
	 * @param array $item Feed item to be escaped and prepared.
	 */
	public function escape_item( $item ) {
		array_walk(
			$item,
			function( &$val, $key ) {
				$callback = $this->get_callback( $key );
				if ( is_callable( $callback ) ) {
					$val = call_user_func( $callback, $val );
				}
			}
		);
		return $item;
	}

	/**
	 * List of callbacks for item fields.
	 *
	 * @since  3.3.0
	 *
	 * @param array $key Feed item field key.
	 */
	public function get_callback( $key ) {

		/**
		 * List of callbacks for item fields.
		 *
		 * @since 3.3.0
		 *
		 * @param array $callbacks All feed items field callbacks.
		 */
		$callbacks = apply_filters(
			'podcast_player_frontend_callback',
			array(
				'title'       => array( $this, 'title' ),
				'description' => array( $this, 'description' ),
				'date'        => array( $this, 'date' ),
				'categories'  => array( $this, 'categories' ),
				'duration'    => array( $this, 'duration' ),
				'link'        => 'esc_url',
				'src'         => 'esc_url',
				'featured'    => 'esc_url',
				'author'      => 'esc_html',
				'mediatype'   => 'esc_html',
				'episode'     => 'esc_html',
				'season'      => 'absint',
			)
		);

		return isset( $callbacks[ $key ] ) ? $callbacks[ $key ] : '';
	}

	/**
	 * Escape feed item title.
	 *
	 * @since  3.3.0
	 *
	 * @param string $title Feed item title.
	 */
	public function title( $title ) {
		return trim( convert_chars( wptexturize( str_replace( '&quot;', '&#8221;', $title ) ) ) );
	}

	/**
	 * Escape feed item description.
	 *
	 * @since  3.3.0
	 *
	 * @param string $description Feed item description.
	 */
	public function description( $description ) {
		return wpautop( wptexturize( str_replace( '&quot;', '&#8221;', $description ) ) );
	}

	/**
	 * Escape feed item date.
	 *
	 * @since  3.3.0
	 *
	 * @param string|Array $d Feed item date.
	 */
	public function date( $d ) {

		$timezone = Get_Fn::get_plugin_option( 'timezone' );
		$date     = is_array( $d ) ? $d['date'] : $d;
		$offset   = is_array( $d ) ? $d['offset'] : 0;

		if ( 'local' === $timezone ) {
			return date_i18n( get_option( 'date_format' ), $date + 60 * 60 * get_option( 'gmt_offset' ) );
		} elseif ( 'feed' === $timezone ) {
			return date_i18n( get_option( 'date_format' ), $date + $offset );
		} else {
			return date_i18n( get_option( 'date_format' ), $date );
		}
	}

	/**
	 * Escape feed item categories.
	 *
	 * @since  3.3.0
	 *
	 * @param string $categories Feed item categories.
	 */
	public function categories( $categories ) {
		return array_map( 'esc_html', $categories );
	}

	/**
	 * Properly formatted episode duration.
	 *
	 * @since  6.2.0
	 *
	 * @param string $duration Episode duration in seconds.
	 */
	public function duration( $duration ) {
		$duration = absint($duration);
		if ( ! $duration ) {
			return '00:00';
		}
		$dur   = array();
		$hours = floor($duration / 3600);
		if ( $hours ) {
			$dur[] = sprintf('%02d', $hours);
		}
		$dur[] = sprintf('%02d', floor(($duration / 60)) % 60);
		$dur[] = sprintf('%02d', $duration % 60);
		return implode(':', $dur);
	}
}
