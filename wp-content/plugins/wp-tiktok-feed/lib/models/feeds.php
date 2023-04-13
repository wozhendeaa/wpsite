<?php

namespace QUADLAYERS\TIKTOK\Models;

use QUADLAYERS\TIKTOK\Models\Base as Model;

/**
 * Models_Feed Class
 */
class Feeds extends Model {

	protected $table = 'tiktok_feed_feeds';

	public function get_args() {
		return array(
			'id'                    => 1,
			'source'                => 'account',
			/* source_ */'open_id'  => 0,
			/* source_ */'hashtag'  => 'wordpress',
			/* source_ */'username' => '',
			'create_time'           => 0,
			'layout'                => 'masonry',
			'limit'                 => 12,
			'columns'               => 3,
			'lazy'                  => true,
			'profile'               => array(
				'display'   => false,
				'username'  => '',
				'nickname'  => '',
				'biography' => '',
				'link_text' => 'Follow',
				'avatar'    => '',
			),
			'video'                 => array(
				'spacing' => 10,
				'radius'  => 0,
			),
			'highlight'             => array(
				'id'       => '',
				'tag'      => '',
				'position' => '1, 5, 7',
			),
			'mask'                  => array(
				'display'        => true,
				'background'     => '#000000',
				'likes_count'    => true,
				'comments_count' => true,
			),
			'box'                   => array(
				'display'    => false,
				'padding'    => 1,
				'radius'     => 0,
				'background' => '#fefefe',
				'text_color' => '#000000',
			),
			'card'                  => array(
				'display'           => false,
				'radius'            => 0,
				'font_size'         => '12',
				'background'        => '#ffffff',
				'background_hover'  => '#ffffff',
				'text_color'        => '#000000',
				'padding'           => '5',
				'likes_count'       => true,
				'max_word_count'    => 10,
				'video_description' => true,
				'comments_count'    => true,
			),
			'carousel'              => array(
				'slidespv'          => 5,
				'autoplay'          => false,
				'autoplay_interval' => 3000,
				'navarrows'         => true,
				'navarrows_color'   => '',
				'pagination'        => true,
				'pagination_color'  => '',
			),
			'modal'                 => array(
				'display'           => true,
				'profile'           => true,
				'download'          => false,
				'video_description' => true,
				'likes_count'       => true,
				'autoplay'          => true,
				'comments_count'    => true,
				'date'              => true,
				'controls'          => true,
				'align'             => 'right',
			),
			'button'                => array(
				'display'          => true,
				'text'             => 'View on TikTok',
				'background'       => '',
				'background_hover' => '',
			),
			'button_load'           => array(
				'display'          => false,
				'text'             => 'Load more...',
				'background'       => '',
				'background_hover' => '',
				'profile'          => '',
			),
		);
	}

	protected function get_next_id() {
		$feeds = $this->get();
		if ( count( $feeds ) ) {
			return max( array_keys( $feeds ) ) + 1;
		}
		return 0;
	}

	public function get_by_id( $id ) {
		$feeds = $this->get();

		if ( isset( $feeds[ $id ] ) ) {
			return $feeds[ $id ];
		}
	}

	public function get() {
		$feeds = $this->get_all();
		// make sure each feed has all values
		if ( count( $feeds ) ) {
			foreach ( $feeds as $id => $feed ) {
				$feeds[ $id ] = array_replace_recursive( $this->get_args(), $feeds[ $id ] );
			}
		}
		return $feeds;
	}

	protected function sanitize_username( $feed ) {
		// Removing @, # and trimming input
		// ---------------------------------------------------------------------

		$feed = sanitize_text_field( $feed );

		$feed = trim( $feed );
		$feed = str_replace( '@', '', $feed );
		$feed = str_replace( '#', '', $feed );
		$feed = str_replace( QLTTF_TIKTOK_URL, '', $feed );
		$feed = str_replace( '/explore/tags/', '', $feed );
		$feed = str_replace( '/', '', $feed );

		return $feed;
	}

	public function add( $feed_data ) {

		$feed_id               = $this->get_next_id();
		$feed_data['id']       = $feed_id;
		$feed_data['hashtag']  = $this->sanitize_username( $feed_data['hashtag'] );
		$feed_data['username'] = $this->sanitize_username( $feed_data['username'] );

		$success = $this->save( $feed_data );

		if ( $success ) {
			return $feed_data;
		}

		return false;
	}

	// public
	public function update( $feed_data ) {
		return $this->save( $feed_data );
	}

	// public
	public function edit( $feed ) {
		$feeds = $this->get_all();
		if ( $feeds ) {
			if ( count( $feeds ) > 0 ) {
				$new_feeds = array_map(
					function ( $f ) use ( $feed ) {
						return ( absint( $f['id'] ) === absint( $feed['id'] ) ? $feed : $f );
					},
					$feeds
				);
				$success   = $this->save_all( $new_feeds );
				if ( $success ) {
					return $success;
				}
			}
		}
	}

	// public
	public function delete( $feed_id = null ) {
		$feeds = $this->get_all();
		if ( $feeds ) {
			if ( count( $feeds ) > 0 ) {
				$new_feeds = array_filter(
					$feeds,
					function ( $feed ) use ( $feed_id ) {
						return ( absint( $feed['id'] ) !== absint( $feed_id ) );
					}
				);
				$success   = $this->save_all( $new_feeds );
				if ( $success ) {
					return $success;
				}
			}
		}
		return false;
	}

	// protected
	protected function save( $feed_data = null ) {
		$feeds                     = $this->get();
		$feeds[ $feed_data['id'] ] = self::array_intersect_key_recursive( array_replace_recursive( $this->get_args(), $feed_data ), $this->get_args() );
		$success                   = $this->save_all( $feeds );
		if ( $success ) {
			return $feeds;
		}
		return false;
	}

	public function delete_table() {
		$this->delete_all();
	}

	protected static function array_intersect_key_recursive( $array1, $array2 ) {
		$array1 = array_intersect_key( $array1, $array2 );
		foreach ( $array1 as $key => $value ) {
			if ( is_array( $value ) && is_array( $array2[ $key ] ) ) {
				$array1[ $key ] = self::array_intersect_key_recursive( $value, $array2[ $key ] );
			}
		}
		return $array1;
	}
}
