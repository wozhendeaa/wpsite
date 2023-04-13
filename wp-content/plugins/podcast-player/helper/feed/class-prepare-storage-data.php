<?php
/**
 * Fetch Feed Data from Feed XML file.
 *
 * @link       https://www.vedathemes.com
 * @since      1.0.0
 *
 * @package    Podcast_Player
 * @subpackage Podcast_Player/Helper
 */

namespace Podcast_Player\Helper\Feed;

use Podcast_Player\Helper\Functions\Getters as Get_Fn;
use Podcast_Player\Helper\Functions\Validation as Validation_Fn;

/**
 * Fetch Feed Data from Feed XML file.
 *
 * @package    Podcast_Player
 * @subpackage Podcast_Player/Helper
 * @author     vedathemes <contact@vedathemes.com>
 */
class Prepare_Storage_Data {

	/**
	 * Holds feed raw data.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var    string
	 */
	private $feed;

	/**
	 * Holds unique feed key for current instance.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var    string
	 */
	private $feed_key = '';

	/**
	 * Holds feed url for current instance.
	 *
	 * @since  3.5.0
	 * @access private
	 * @var    string
	 */
	private $feed_url = '';

	/**
	 * Holds instance of current podcast item.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var    object
	 */
	private $item = '';

	/**
	 * Holds ID of current podcast item.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var    string
	 */
	private $id;

	/**
	 * Holds feed key prefix.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var    string
	 */
	private $prefix = 'pp_feed';

	/**
	 * Holds old feed data.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var    array
	 */
	private $old_data = array();

	/**
	 * Holds new episodes ID.
	 *
	 * @since  5.8.0
	 * @access private
	 * @var    array
	 */
	private $elist = array();

	/**
	 * Constructor method.
	 *
	 * @since  3.3.0
	 *
	 * @param object $raw_data Feed Data.
	 * @param string $key      Feed Key.
	 * @param string $url      Feed Url.
	 */
	public function __construct( $raw_data, $key, $url ) {
		$this->feed     = $raw_data;
		$this->feed_key = $key;
		$this->feed_url = $url;

		$data_key       = $this->prefix . '_data_' . $this->feed_key;
		$this->old_data = get_option( $data_key );
	}

	/**
	 * Prepare feed data for storage.
	 *
	 * @since  3.3.0
	 */
	public function init() {
		$feed  = $this->get_feed_channel_data();
		$items = $this->get_items_data();

		// Return if feed does not contain any items.
		if ( ! $items || empty( $items ) ) {
			return false;
		}

		$merged = $this->merge_data( $feed, $items );

		// Compare with old stored data to get changes.
		list( $data, $changed ) = $this->get_changes( $merged );

		// Check if some images are not saved locally.
		$is_img_save = $this->check_if_image_save( $data );
		return array( $data, $changed, $is_img_save, $this->elist );
	}

	/**
	 * Fetch feed level data.
	 *
	 * @since  3.3.0
	 */
	public function get_feed_channel_data() {
		$title     = $this->get_podcast_title();
		$desc      = $this->feed->get_description();
		$link      = $this->feed->get_permalink();
		$image     = $this->feed->get_image_url();
		$copyright = $this->feed->get_copyright();
		$author    = $this->feed->get_author();
		$cats      = $this->get_podcast_categories();
		$lastbuild = $this->get_last_build_date();
		$owner     = $this->get_podcast_owner();

		return array(
			'title'     => $title,
			'desc'      => $desc ? wp_kses_post( wp_check_invalid_utf8( $desc ) ) : '',
			'link'      => $link ? esc_url_raw( $link ) : '',
			'image'     => $image ? esc_url_raw( $image ) : '',
			'furl'      => esc_url_raw( $this->feed_url ),
			'fkey'      => sanitize_text_field( $this->feed_key ),
			'copyright' => $copyright ? sanitize_text_field( $copyright ) : '',
			'author'    => $author ? sanitize_text_field( $author ) : '',
			'podcats'   => $cats,
			'lastbuild' => $lastbuild ? absint( $lastbuild ) : 0,
			'owner'     => $owner ? $owner : false,
		);
	}

	/**
	 * Get podcast title.
	 *
	 * @since  3.3.0
	 */
	public function get_podcast_title() {
		$title = $this->feed->get_title();
		if ( $title ) {
			$title = htmlspecialchars_decode($title);
			return wp_kses_post( wp_check_invalid_utf8( $title ) );
		} else {
			return '';
		}
	}

	/**
	 * Get podcast categories.
	 *
	 * @since  5.7.0
	 */
	public function get_podcast_categories() {
		$cat     = array();
		$podcats = $this->feed->get_channel_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'category' );
		if ( $podcats ) {
			foreach ( $podcats as $podcat ) {
				$label = isset( $podcat['attribs']['']['text'] ) ? $podcat['attribs']['']['text'] : false;
				if ( ! $label ) {
					continue;
				}
				$label = sanitize_text_field( $label );
				$key   = strtolower( str_replace( ' ', '', $label ) );
				if ( ! isset( $cat[ $key ] ) ) {
					$cat[ $key ] = array(
						'label'   => $label,
						'subcats' => array(),
					);
				}

				$subcat = isset( $podcat['child'][ SIMPLEPIE_NAMESPACE_ITUNES ]['category'][0]['attribs']['']['text'] ) ? $podcat['child'][ SIMPLEPIE_NAMESPACE_ITUNES ]['category'][0]['attribs']['']['text'] : false;
				if ( $subcat ) {
					$cat[ $key ]['subcats'][] = sanitize_text_field( $subcat );
				}
			}
		}
		return $cat;
	}

	/**
	 * Get podcast owner.
	 *
	 * @since  5.7.0
	 */
	public function get_podcast_owner() {
		$owner = $this->feed->get_channel_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'owner' );
		if ( $owner ) {
			$name  = isset( $owner[0]['child'][ SIMPLEPIE_NAMESPACE_ITUNES ]['name'][0]['data'] ) ? $owner[0]['child'][ SIMPLEPIE_NAMESPACE_ITUNES ]['name'][0]['data'] : false;
			$email = isset( $owner[0]['child'][ SIMPLEPIE_NAMESPACE_ITUNES ]['email'][0]['data'] ) ? $owner[0]['child'][ SIMPLEPIE_NAMESPACE_ITUNES ]['email'][0]['data'] : false;
			return array(
				'name'  => $name ? sanitize_text_field( $name ) : '',
				'email' => $email ? sanitize_email( $email ) : '',
			);
		}
		return false;
	}

	/**
	 * Get last build date.
	 *
	 * @since  5.7.0
	 */
	public function get_last_build_date() {
		$lbd = $this->feed->get_channel_tags( '', 'lastBuildDate' );
		if ( $lbd ) {
			$date = isset( $lbd[0]['data'] ) ? $lbd[0]['data'] : false;
			if ( $date ) {
				$parser = $this->feed->registry->call( 'Parse_Date', 'get' );
				if ( $parser ) {
					return $parser->parse( $date );
				}
			}
		}
		return '';
	}

	/**
	 * Fetch items level data.
	 *
	 * @since  3.3.0
	 */
	public function get_items_data() {
		$nitems = array();
		$items  = $this->feed->get_items();
		if ( ! $items ) {
			return false;
		}
		foreach ( $items as $item ) {
			$this->item = $item;
			$data       = $this->get_item_data();
			if ( $data ) {
				$nitems[ $this->id ] = $data;
			}
		}
		return $nitems;
	}

	/**
	 * Fetch single item data.
	 *
	 * @since  3.3.0
	 */
	public function get_item_data() {
		list( $media, $media_type ) = $this->get_item_media();
		if ( ! $media || ! $media_type ) {
			return false;
		}

		$this->id = $this->get_item_id( $media );
		return array(
			'title'       => $this->get_item_title(),
			'description' => $this->get_item_content(),
			'author'      => $this->get_item_author(),
			'date'        => $this->get_item_date(),
			'link'        => $this->get_item_link( $media ),
			'src'         => $media,
			'featured'    => $this->get_featured_image(),
			'mediatype'   => $media_type,
			'episode'     => $this->get_itunes_episode(),
			'season'      => $this->get_itunes_season(),
			'categories'  => $this->get_categories(),
			'episode_id'  => $this->get_episode_id(),
			'duration'    => $this->get_episode_duration(),
		);
	}

	/**
	 * Get Episode ID.
	 *
	 * @since  5.7.0
	 */
	public function get_episode_id() {
		$id = $this->item->get_id();
		if ( ! $id || empty( $id ) ) {
			return false;
		}
		return sanitize_text_field( $id );
	}

	/**
	 * Get Episode duration.
	 *
	 * @since  5.7.0
	 */
	public function get_episode_duration() {
		$d = $this->item->get_item_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'duration' );
		if ( ! $d ) {
			return false;
		}

		$d   = sanitize_text_field( $d[0]['data'] );
		$sec = 0;
		$ta  = array_reverse( explode( ':', $d ) );
		foreach ( $ta as $key => $val ) {
			$sec += absint( $val ) * pow( 60, $key );
		}
		return $sec;
	}

	/**
	 * Get media src from the item.
	 *
	 * @since  3.3.0
	 */
	public function get_item_media() {
		$enclosure  = $this->get_media_enclosure();
		$media      = $enclosure ? $enclosure->link : '';
		$media_type = $media ? Get_Fn::get_media_type( $media ) : '';
		if ( $media_type ) {
			return array( esc_url_raw( $media ), sanitize_text_field( $media_type ) );
		} else {
			return array( esc_url_raw( $media ), false );
		}
	}

	/**
	 * Get media src from the item.
	 *
	 * @since  3.3.0
	 */
	public function get_media_enclosure() {
		$enclosures = $this->item->get_enclosures();
		if ( ! $enclosures || empty( $enclosures ) ) {
			return false;
		}

		// Check if any audio or video enclosure exist.
		foreach ( $enclosures as $enclosure ) {
			$type = $enclosure->get_type();
			if ( false !== strpos( $type, 'audio' ) || false !== strpos( $type, 'video' ) ) {
				return $enclosure;
			}
		}
		return $enclosures[0];
	}

	/**
	 * Generate current item's unique ID.
	 *
	 * @since  3.3.0
	 *
	 * @param string $media Media for current item.
	 */
	public function get_item_id( $media ) {
		return md5( $media );
	}

	/**
	 * Get current item's title.
	 *
	 * @since  3.3.0
	 */
	public function get_item_title() {
		$title = $this->item->get_title();
		if ( $title ) {
			$title = htmlspecialchars_decode($title);
			return wp_kses_post( wp_check_invalid_utf8( $title ) );
		} else {
			return '';
		}
	}

	/**
	 * Get current item's description.
	 *
	 * @since  3.3.0
	 */
	public function get_item_content() {
		$content = $this->item->get_content();
		if ( $content ) {
			$content = wp_kses_post( wp_check_invalid_utf8( $content ) );
			if ( 'yes' === Get_Fn::get_plugin_option( 'rel_external' ) ) {
				$link_mod = Add_External_Link_Attr::get_instance();
				$content  = $link_mod->init( $content );
			}
			return $content;
		} else {
			return '';
		}
	}

	/**
	 * Get current item's permalink.
	 *
	 * @since  3.3.0
	 *
	 * @param string $media Media for current item.
	 */
	public function get_item_link( $media ) {
		$link = $this->item->get_link();
		if ( $link ) {
			return esc_url_raw( $link );
		} else {
			return esc_url_raw( $media );
		}
	}

	/**
	 * Get current item's publish date.
	 *
	 * @since  3.3.0
	 */
	public function get_item_date() {

		// Date in GMT/UTC timezone.
		$date = $this->item->get_date( 'U' );

		// Calculate Timezone Offset.
		$dt     = $this->item->get_date( '' );
		$offset = $dt ? date_create( $dt ) : '';
		$offset = $offset ? date_format( $offset, 'Z' ) : '';
		$offset = $offset ? $offset : '';

		// Return Proper date.
		if ( $date ) {
			return array(
				'date'   => absint( $date ),
				'offset' => intval( $offset ),
			);
		} else {
			return '';
		}
	}

	/**
	 * Get current item's author.
	 *
	 * @since  3.3.0
	 */
	public function get_item_author() {
		$author = $this->item->get_author();
		$author = $author ? $author->get_name() : '';
		if ( $author ) {
			return sanitize_text_field( $author );
		} else {
			return '';
		}
	}

	/**
	 * Get featured image for the current item.
	 *
	 * @since  3.3.0
	 */
	public function get_featured_image() {
		$image = $this->get_image_enclosure_link();
		if ( ! $image ) {
			$image = $this->get_itunes_image_link();
		}
		if ( $image && Validation_Fn::is_valid_image_url( $image ) ) {
			return esc_url_raw( $image );
		} else {
			return '';
		}
	}

	/**
	 * Get media src from the item.
	 *
	 * @since  3.3.0
	 */
	public function get_image_enclosure_link() {
		$enclosures = $this->item->get_enclosures();
		if ( ! $enclosures || empty( $enclosures ) ) {
			return false;
		}

		// Check if any audio or video enclosure exist.
		foreach ( $enclosures as $enclosure ) {
			if ( 'image' === $enclosure->get_medium() ) {
				return $enclosure->get_link();
			}
		}
	}

	/**
	 * Get media src from the item.
	 *
	 * @since  3.3.0
	 */
	public function get_itunes_image_link() {
		$img_tag = $this->item->get_item_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'image' );
		if ( $img_tag ) {
			return $img_tag[0]['attribs']['']['href'];
		}
		return false;
	}

	/**
	 * Get iTunes season.
	 *
	 * @since  3.3.0
	 */
	public function get_itunes_season() {
		$season = $this->item->get_item_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'season' );
		if ( $season ) {
			return sanitize_text_field( $season[0]['data'] );
		}
		return '';
	}

	/**
	 * Get iTunes episode.
	 *
	 * @since  3.3.0
	 */
	public function get_itunes_episode() {
		$episode = $this->item->get_item_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'episode' );
		if ( $episode ) {
			$episode = $episode[0]['data'];
			$season  = $this->get_itunes_season();
			$episode = $season ? $season . '-' . $episode : $episode;
			return sanitize_text_field( $episode );
		}
		return '';
	}

	/**
	 * Get item categories.
	 *
	 * @since  3.3.0
	 */
	public function get_categories() {
		$catobjects = $this->item->get_categories();
		$cats       = array();
		if ( $catobjects ) {
			foreach ( $catobjects as $catobject ) {
				$term         = sanitize_text_field( $catobject->term );
				$key          = strtolower( str_replace( ' ', '', $term ) );
				$cats[ $key ] = $term;
			}
		}
		return $cats;
	}

	/**
	 * Get merged feed data for storage.
	 *
	 * @since  3.3.0
	 *
	 * @param array $feed_data Feed header level data.
	 * @param array $items     Feed items data.
	 */
	public function merge_data( $feed_data, $items ) {

		// Get cumulative array of all available seasons.
		$seasons = array_values( array_filter( array_unique( array_column( $items, 'season' ) ) ) );

		// Get cumulative array of all available categories.
		$cats = array_column( $items, 'categories' );
		$cats = array_unique( call_user_func_array( 'array_merge', $cats ) );

		// Merge all data into single array.
		$feed_data['seasons']    = $seasons;
		$feed_data['categories'] = $cats;
		$feed_data['items']      = $items;
		$feed_data['total']      = count( $items );
		return $feed_data;
	}

	/**
	 * Compare new data with old stored data for changes.
	 *
	 * @since  3.3.0
	 *
	 * @param array $new_data Newly Created data for the feed.
	 */
	public function get_changes( $new_data ) {
		$is_changed = false;
		$final_data = $this->old_data;

		// Return new data if old data is not available in database.
		if ( ! $final_data || ! isset( $final_data['items'] ) || ! $final_data['items'] ) {
			$is_changed = true;
			return array( $new_data, $is_changed );
		}

		// Compare and update channel data.
		$updated_channel_data = $this->channel_changes( $new_data );
		if ( $updated_channel_data ) {
			$is_changed = true;
			$final_data = $updated_channel_data;
		}

		// Compare and update itmes data.
		$updated_items_data = $this->items_changes( $new_data['items'] );
		if ( $updated_items_data ) {
			$is_changed          = true;
			$final_data['items'] = $updated_items_data;
		}

		return array( $final_data, $is_changed );
	}

	/**
	 * Compare channel level data.
	 *
	 * @since  3.3.0
	 *
	 * @param array $new_data Newly Created data for the feed.
	 */
	public function channel_changes( $new_data ) {
		$base_data  = $this->old_data;
		$is_changed = false;
		foreach ( $new_data as $key => $data ) {

			// Only compare channel level items.
			if ( 'items' === $key ) {
				continue;
			}

			// Check if current data is changed or newly added.
			if ( isset( $base_data[ $key ] ) && $data === $base_data[ $key ] ) {
				continue;
			}

			// Data changed/ Added, so update accordingly.
			$base_data[ $key ] = $data;
			$is_changed        = true;

			// Remove saved cover ID if cover image changes.
			if ( 'image' === $key ) {
				unset( $base_data['cover_id'] );
			}
		}

		if ( $is_changed ) {
			return $base_data;
		} else {
			return false;
		}
	}

	/**
	 * Compare items level data.
	 *
	 * @since  3.3.0
	 *
	 * @param array $new_items Newly Created items data for the feed.
	 */
	public function items_changes( $new_items ) {
		$base_items    = $this->old_data['items'];
		$base_count    = count( $base_items );
		$deleted_items = array_diff_key( $base_items, $new_items );
		$del_ids       = array();
		$is_changed    = false;

		if ( $deleted_items ) {
			$ep_ids  = array_column( $deleted_items, 'episode_id' );
			$ep_keys = array_keys( $deleted_items );
			if ( count( $ep_ids ) === count( $ep_keys ) ) {
				$del_ids = array_filter( array_combine( $ep_ids, $ep_keys ) );
			}
		}

		// Conditionally remove old items which are no longer available in the feed.
		$keep_old = Get_Fn::get_plugin_option( 'keep_old' );
		if ( 'yes' !== $keep_old ) {
			$base_items = array_intersect_key( $base_items, $new_items );
		}

		foreach ( $new_items as $id => $item ) {

			// If the item is already available and has not changed.
			if ( isset( $base_items[ $id ] ) && $item === $base_items[ $id ] ) {
				continue;
			}

			// Just add if new item does not exist.
			if ( ! isset( $base_items[ $id ] ) ) {

				// Handle borderline cases where episode audio URL is modified.
				$eid = isset( $item['episode_id'] ) ? $item['episode_id'] : false;
				$did = isset( $del_ids[ $eid ] ) ? $del_ids[ $eid ] : false;
				if ( $did ) {
					$base_items[ $did ] = $item;
					if ( isset( $deleted_items[ $did ] ) && $deleted_items[ $did ] !== $item ) {
						$is_changed = true;
					}
					unset( $deleted_items[ $did ] );
					continue;
				}

				// New item has been added.
				$base_items[ $id ] = $item;
				$this->elist[]     = $id;
				$is_changed        = true;
				continue;
			}

			// Check and update modified item data.
			$item_data = $this->item_changes( $item, $base_items[ $id ] );
			if ( $item_data ) {
				$base_items[ $id ] = $item_data;
				$is_changed        = true;
			}
		}

		if ( $is_changed || count( $deleted_items ) ) {
			return $base_items;
		} else {
			return false;
		}
	}

	/**
	 * Compare and update item properties.
	 *
	 * @since  3.3.0
	 *
	 * @param array $new_item Newly Created data for the item.
	 * @param array $old_item Old Stored data for the item.
	 */
	public function item_changes( $new_item, $old_item ) {
		$is_changed = false;
		foreach ( $new_item as $key => $data ) {
			if ( isset( $old_item[ $key ] ) && $data === $old_item[ $key ] ) {
				continue;
			}

			$old_item[ $key ] = $data;
			$is_changed       = true;

			// Remove saved featured image ID if featured image changes.
			if ( 'featured' === $key && isset( $old_item['featured_id'] ) ) {
				unset( $old_item['featured_id'] );
			}
		}

		if ( $is_changed ) {
			return $old_item;
		} else {
			return false;
		}
	}

	/**
	 * Check if some images are not saved locally.
	 *
	 * @since  3.3.0
	 *
	 * @param array $data Fetched final data for the feed.
	 */
	public function check_if_image_save( $data ) {
		$items = $data['items'];
		if ( ! isset( $data['cover_id'] ) ) {
			return true;
		}
		foreach ( $items as $item => $atts ) {
			if ( isset( $atts['featured'] ) && $atts['featured'] ) {
				if ( ! isset( $atts['featured_id'] ) ) {
					return true;
				}
			}
		}
		return false;
	}
}
