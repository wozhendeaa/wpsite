<?php

namespace YoutubeImporterSecondLine\Helper;

use YoutubeImporterSecondLine\Helper\Importer\FeedItem as YIS_Helper_Importer_FeedItem;

class Importer {

  /**
   * @param array $meta_map
   * @return Importer
   */
  public static function from_meta_map( array $meta_map ): Importer {
    $settings = [];

    if( isset( $meta_map[ 'secondline_import_import_type' ] ) )
      $settings[ 'import_type' ] = $meta_map[ 'secondline_import_import_type' ];
    else
      $settings[ 'import_type' ] = 'channel';

    if( isset( $meta_map[ 'secondline_import_post_type' ] ) )
      $settings[ 'post_type' ] = $meta_map[ 'secondline_import_post_type' ];

    if( isset( $meta_map[ 'secondline_import_publish' ] ) )
      $settings[ 'post_status' ] = $meta_map[ 'secondline_import_publish' ];

    if( isset( $meta_map[ 'secondline_import_author' ] ) )
      $settings[ 'post_author' ] = $meta_map[ 'secondline_import_author' ];

    if( isset( $meta_map[ 'secondline_import_category' ] ) && is_array( $meta_map[ 'secondline_import_category' ] ) )
      $settings[ 'post_categories' ] = $meta_map[ 'secondline_import_category' ];

    if( isset( $meta_map[ 'secondline_import_images' ] ) )
      $settings[ 'import_images' ] = self::_meta_setting_to_bool( $meta_map[ 'secondline_import_images' ] );

    if( isset( $meta_map[ 'secondline_truncate_post' ] ) )
      $settings[ 'import_content_truncate' ] = ( $meta_map[ 'secondline_truncate_post' ] === '' ? false : intval( $meta_map[ 'secondline_truncate_post' ] ) );

    if( isset( $meta_map[ 'secondline_prepend_title' ] ) )
      $settings[ 'import_prepend_title' ] = $meta_map[ 'secondline_prepend_title' ];

    if( isset( $meta_map[ 'secondline_import_allow_sync' ] ) )
      $settings[ 'import_allow_sync' ] = self::_meta_setting_to_bool( $meta_map[ 'secondline_import_allow_sync' ] );

    if( isset( $meta_map[ 'secondline_import_date_from' ] ) )
      $settings[ 'import_date_from' ] = ( $meta_map[ 'secondline_import_date_from' ] !== '' ? $meta_map[ 'secondline_import_date_from' ] : false );

    if( isset( $meta_map[ 'secondline_parent_show' ] ) )
      $settings[ 'import_parent_show' ] = $meta_map[ 'secondline_parent_show' ];

    if( !isset( $meta_map[ 'secondline_import_allow_sync' ] )
        || ( isset( $meta_map[ 'secondline_import_allow_sync' ] ) && $meta_map[ 'secondline_import_allow_sync' ] ) )
      if( isset( $meta_map[ 'secondline_latest_timestamp' ] ) && !empty( $meta_map[ 'secondline_latest_timestamp' ] ) )
        $settings[ 'import_date_from' ] = intval( $meta_map[ 'secondline_latest_timestamp' ] );

    $settings = apply_filters( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_importer_settings_from_meta_map', $settings, $meta_map );

    $meta_key = ( $settings[ 'import_type' ] === 'channel' ? 'secondline_youtube_channel_id' : 'secondline_youtube_playlist_id' );

    return new self( $meta_map[ $meta_key ] ?? null, $settings  );
  }

  public static function _meta_setting_to_bool( $val ): bool {
    return ( $val === 'off' ? false : ( $val === 'on' ? true : boolval( $val ) ) );
  }

  public $channel_items = [];
  public $source_id = '';

  public $post_type       = 'post';
  public $post_status     = 'publish';
  public $post_author     = 'admin';
  public $post_categories = [];

  public $import_type = 'channel';
  public $import_allow_sync = false;
  public $import_images = false;
  public $import_content_truncate = false;
  public $import_prepend_title    = '';
  public $import_parent_show      = '';
  public $import_date_from = false;

  public $additional_settings = [];

  private $_current_feed_post_count = 0;
  private $_current_video_id_to_post_id_map = [];
  private $_current_imported_count = 0;
  private $_post_categories_import_map = [];

  public function __construct( $source_id, $settings = array() ) {
    $this->source_id = $source_id;

    foreach( $settings as $k => $v ) {
      if( !isset( $this->$k ) ) {
        $this->additional_settings[ $k ] = $v;
        continue;
      }

      $this->$k = $v;
    }

    if( $this->import_date_from !== false )
      $this->import_date_from = !is_numeric( $this->import_date_from ) ? strtotime( $this->import_date_from ) : intval( $this->import_date_from );

    if( !function_exists( 'post_exists' ) )
      require_once(ABSPATH . 'wp-admin/includes/post.php' );
  }

  public function import_current_feed() {
    do_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_importer_before_import_current_feed', $this );

    set_time_limit(360);

    if( empty( $this->channel_items ) ) {
      if( $this->import_type === 'channel' )
        $this->channel_items = Youtube::get_items( $this->source_id, 99999, '', $this->import_date_from );
      else
        $this->channel_items = Youtube::get_playlist_items( $this->source_id, 99999, '', $this->import_date_from );

      $this->_current_feed_post_count = count( $this->channel_items );

      if( empty( $this->channel_items ) )
        return false;
    }

    if( $this->_current_feed_post_count === 0 )
      return false;

    if( empty( $this->_post_categories_import_map ) ) {
      foreach( $this->post_categories as $post_category ) {
        $term = get_term( $post_category );

        if( !isset( $term->taxonomy ) )
          continue;

        if( !isset( $this->_post_categories_import_map[ $term->taxonomy ] ) )
          $this->_post_categories_import_map[ $term->taxonomy ] = [];

        $this->_post_categories_import_map[ $term->taxonomy ][] = $post_category;
      }
    }

    $this->_current_imported_count = 0;

    $synced_count  = 0;
    $skipped_count = 0;
    $skipped_missing_id_count = 0;
    $additional_errors = [];

    foreach ( $this->channel_items as $index => $channel_item ) {
      if( $this->import_date_from !== false ) {
        if( strtotime( $channel_item[ 'snippet' ][ 'publishedAt' ] ) < $this->import_date_from ) {
          $skipped_count++;
          continue;
        }
      }

      if( !isset( $channel_item[ 'id' ] ) ) {
        $additional_errors[] = sprintf( __( "Missing 'id' param for an entry, at index %s", 'auto-youtube-importer' ), $index );

        $skipped_missing_id_count++;
        continue;
      }

      $feedItemInstance = new YIS_Helper_Importer_FeedItem( $this, $channel_item );

      if( $feedItemInstance->current_post_id !== 0 ) {
        if( $this->import_allow_sync ) {
          $sync_response = $feedItemInstance->sync();

          if( is_wp_error( $sync_response ) ) {
            $additional_errors[] = $sync_response->get_error_message();

            unset( $feedItemInstance );

            continue;
          }

          $synced_count++;
        }

        $this->_current_video_id_to_post_id_map[ $feedItemInstance->youtube_video_id ] = $feedItemInstance->current_post_id;

        unset( $feedItemInstance );

        continue;
      }

      $import_response = $feedItemInstance->import();

      if( is_wp_error( $import_response ) ) {
        $additional_errors[] = $import_response->get_error_message();

        unset( $feedItemInstance );

        continue;
      }

      $this->_current_video_id_to_post_id_map[ $feedItemInstance->youtube_video_id ] = $feedItemInstance->current_post_id;
      $this->_current_imported_count++;

      unset( $feedItemInstance );
    }

    do_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_importer_after_import_current_feed', $this );

    return [
      'post_count'     => $this->_current_feed_post_count - $skipped_missing_id_count,
      'skipped_count'     => $skipped_count,
      'synced_count'      => $synced_count,
      'imported_count'    => count( $this->_current_video_id_to_post_id_map ),
      'current_import'    => $this->_current_imported_count,
      'additional_errors' => $additional_errors,
      'latest_timestamp'  => ( !empty( $this->channel_items ) ? strtotime( $this->channel_items[ array_key_first( $this->channel_items ) ][ 'snippet' ][ 'publishedAt' ] ) : false )
    ];
  }

  public function get_post_categories_import_map() :array {
    return $this->_post_categories_import_map;
  }


}