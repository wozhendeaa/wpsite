<?php

namespace YoutubeImporterSecondLine\RestAPI;

use WP_Error;
use YoutubeImporterSecondLine\Settings as YIS_Settings;
use YoutubeImporterSecondLine\Helper\FeedForm as YIS_Helper_FeedForm;
use YoutubeImporterSecondLine\Helper\Importer as YIS_Helper_Importer;
use YoutubeImporterSecondLine\Helper\Youtube as YIS_Helper_Youtube;

class Response {

  public static function admin_dismiss_notice( $request_data ) {
    $current_notice_dismiss_map = YIS_Settings::instance()->get( '_admin_notice_dismissed_map' );

    $current_notice_dismiss_map[ get_current_user_id() ] = time();

    YIS_Settings::instance()->update( '_admin_notice_dismissed_map', $current_notice_dismiss_map );

    return rest_ensure_response( true );
  }

  public static function import_feed( $request ) {
    $request_data = $request->get_params();
    $messages = [];

    $meta_map = YIS_Helper_FeedForm::request_data_to_meta_map( $request_data );

    $importer = YIS_Helper_Importer::from_meta_map( $meta_map );

    $import_current_feed = $importer->import_current_feed();

    if( $import_current_feed === false ) {
      return rest_ensure_response( [
        'messages'  => [
          [
            'type'    => 'danger',
            'message' => __( "Invalid Channel ID or the API quota has been reached.", 'auto-youtube-importer' )
          ]
        ]
      ] );
    }

    if( $import_current_feed[ 'current_import' ] == 0 && $import_current_feed[ 'post_count' ] != 0) {
      if( $import_current_feed[ 'synced_count' ] !== 0 ) {
        $messages[] = [
          'type'    => 'success',
          'message' => __('Success! Re-synced ', 'auto-youtube-importer' ) . $import_current_feed[ 'synced_count' ] . __( " previously imported posts.", 'auto-youtube-importer' )
        ];
      } else {
        $messages[] = [
          'type'    => 'danger',
          'message' => __( 'No new posts to import - all posts already exist in WordPress!', 'auto-youtube-importer' ) . '<br/>' .
            __('If you have existing draft, private or trashed posts with the same title as your posts, delete those and run the importer again.', 'auto-youtube-importer')
        ];
      }
    } elseif ( $import_current_feed[ 'post_count' ] == 0) { // No posts existing within feed.
      $messages[] = [
        'type'    => 'danger',
        'message' => __( 'Error! Your feed does not contain any items.', 'auto-youtube-importer' )
      ];
    } else {
      $messages[] = [
        'type'    => 'success',
        'message' => __('Success! Imported ', 'auto-youtube-importer') . $import_current_feed[ 'current_import' ] .
                     __(' out of ', 'auto-youtube-importer') . $import_current_feed[ 'post_count' ] . __(' posts', 'auto-youtube-importer' ) . '.' .
                    ( $import_current_feed[ 'synced_count' ] !== 0 ? ' ' . $import_current_feed[ 'synced_count' ] . __( " previously imported posts re-synced", 'auto-youtube-importer' ) : '' )
      ];
    }

    if( isset( $import_current_feed[ 'additional_errors' ] ) && is_array( $import_current_feed[ 'additional_errors' ] ) )
      foreach( $import_current_feed[ 'additional_errors' ] as $additional_error )
        $messages[] = [
          'type'    => 'danger',
          'message' => $additional_error
        ];

    if( isset( $request_data[ 'post_id' ] ) ) {
      foreach( $meta_map as $k => $v )
        update_post_meta( intval( $request_data[ 'post_id' ] ), $k, $v );

      if( isset( $import_current_feed[ 'latest_timestamp' ] ) && !empty( $import_current_feed[ 'latest_timestamp' ] ) )
        update_post_meta( intval( $request_data[ 'post_id' ] ), "secondline_latest_timestamp", $import_current_feed[ 'latest_timestamp' ] );
    } else if( isset( $meta_map[ 'secondline_import_continuous' ] ) && $meta_map[ 'secondline_import_continuous' ] == 'on' ) {
      $channel_title = YIS_Helper_Youtube::get_channel_title( $importer->source_id );

      if( is_wp_error( $channel_title ) )
        $channel_title = $importer->source_id;

      if( 0 === post_exists( $channel_title, "", "", YOUTUBE_IMPORTER_SECONDLINE_POST_TYPE_IMPORT )) {
        $import_post = [
          'post_title'   => $channel_title,
          'post_type'    => YOUTUBE_IMPORTER_SECONDLINE_POST_TYPE_IMPORT,
          'post_status'  => 'publish',
        ];
        $post_import_id = wp_insert_post( $import_post );

        foreach( $meta_map as $k => $v )
          update_post_meta( $post_import_id, $k, $v );

        if( isset( $import_current_feed[ 'latest_timestamp' ] ) && !empty( $import_current_feed[ 'latest_timestamp' ] ) )
          update_post_meta( $post_import_id, "secondline_latest_timestamp", $import_current_feed[ 'latest_timestamp' ] );

      } else {
        $messages[] = [
          'type'    => 'danger',
          'message' => __('This youtube is already being scheduled for import. Delete the previous schedule to create a new one.', 'auto-youtube-importer' )
        ];
      }
    }

    return rest_ensure_response( [
      'messages'  => $messages
    ] );
  }

  public static function sync_feed( $request ) {
    $all_meta = get_post_meta( intval( $request[ 'id' ] ) );
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
        update_post_meta( intval( $request[ 'id' ] ), "secondline_latest_timestamp", $response[ 'latest_timestamp' ] );
    }

    return rest_ensure_response( true );
  }

}