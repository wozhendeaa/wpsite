<?php

namespace YoutubeImporterSecondLine;

use YoutubeImporterSecondLine\Helper\Importer as YIS_Helper_Importer;
use YoutubeImporterSecondLine\Settings as YIS_Settings;

class ActionScheduler {

  /**
   * @var null|ActionScheduler;
   */
  protected static $_instance = null;

  /**
   * @return ActionScheduler
   */
  public static function instance(): ActionScheduler {
    if( self::$_instance === null )
      self::$_instance = new self();

    return self::$_instance;
  }

  public function setup() {
    add_action( 'init', [ $this, "_init" ] );

    add_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feeds_sync', [ $this, '_feeds_sync' ] );

    // Async Scheduled
    add_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feed_sync', [ $this, '_feed_sync' ], 10, 2 );
    add_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_image_sync', [ $this, '_image_sync' ], 10, 2 );
  }

  public function _init() {
    if( YIS_Settings::instance()->get( 'youtube_api_key' ) === '' )
      return;

      if (function_exists( 'as_has_scheduled_action' )) {
        if ( false === as_has_scheduled_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feeds_sync' ) ) {
          as_schedule_recurring_action( strtotime( 'now' ), ( 2 * HOUR_IN_SECONDS ), YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feeds_sync' );
        }
      } else {
        if ( false === as_next_scheduled_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feeds_sync' ) ) {
          as_schedule_recurring_action( strtotime( 'now' ), ( 2 * HOUR_IN_SECONDS ), YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feeds_sync' );
        }
      }
      
    }

  public function _feeds_sync() {
    $post_ids = get_posts( [
      'post_type'    	 => YOUTUBE_IMPORTER_SECONDLINE_POST_TYPE_IMPORT,
      'posts_per_page' => -1,
      'fields'         => 'ids'
    ] );

    foreach( $post_ids as $post_id )
      as_enqueue_async_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_scheduler_feed_sync', [ $post_id ], YOUTUBE_IMPORTER_SECONDLINE_ALIAS );
  }

  public function _feed_sync( $feed_post_id ) {
    $all_meta = get_post_meta( $feed_post_id );
    $meta_map = [];

    foreach( $all_meta as $k => $v ) {
      if( is_array( $v ) && count( $v ) === 1 )
        $v = maybe_unserialize( $v[ 0 ] );

      $meta_map[ $k ] = $v;
    }

    // Maybe deleted after queued, need to ensure it's fine.
    if( isset( $meta_map[ 'secondline_youtube_channel_id' ] ) ) {
      $importer = YIS_Helper_Importer::from_meta_map( $meta_map );
      $response = $importer->import_current_feed();

      if( isset( $response[ 'latest_timestamp' ] ) && !empty( $response[ 'latest_timestamp' ] ) )
        update_post_meta( $feed_post_id, "secondline_latest_timestamp", $response[ 'latest_timestamp' ] );
    }
  }

  public function _image_sync( $post_id, $image_path ) {
    if( !function_exists( 'media_sideload_image' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/media.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
    }

    $image_contents = wp_remote_get( $image_path );
    $image_contents = wp_remote_retrieve_body( $image_contents );

    global $wpdb;

    $post_id = intval( $post_id );

    $attachment_post_id = $wpdb->get_var(
      'SELECT post_id 
               FROM ' . $wpdb->postmeta . ' 
              WHERE meta_key = "secondline_attachment_md5"
                AND meta_value = ' . $wpdb->prepare( "%s", md5( $image_contents ) )
    );

    if( null === $attachment_post_id ) {
      $attachment_post_id = media_sideload_image($image_path, $post_id, get_the_title( $post_id ), 'id' );

      update_post_meta( $attachment_post_id, 'secondline_attachment_md5', md5( $image_contents ) );
    } else {
      $attachment_post_id = intval( $attachment_post_id );
    }

    set_post_thumbnail( $post_id, $attachment_post_id );
  }

}