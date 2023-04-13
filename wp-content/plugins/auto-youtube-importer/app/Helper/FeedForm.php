<?php

namespace YoutubeImporterSecondLine\Helper;

class FeedForm {

  public static function get_for_render( $post_id = null ) :array {
    $field_definitions = self::field_definitions();
    $response = [];

    foreach( $field_definitions as $key => $field_definition ) {
      if( !isset( $field_definition[ 'storage' ] ) )
        continue;

      if( isset( $field_definition[ 'views' ] ) ) {
        if( !in_array( 'add', $field_definition[ 'views' ] ) && !is_numeric( $post_id ) )
          continue;

        if( !in_array( 'edit', $field_definition[ 'views' ] ) && is_numeric( $post_id ) )
          continue;

        unset( $field_definition[ 'views' ] );
      }

      $storage = $field_definition[ 'storage' ];

      unset( $field_definition[ 'storage' ] );

      if( is_numeric( $post_id ) ) {
        $field_definition[ 'value' ] = null;

        if( $storage[ 'type' ] === 'meta' )
          $field_definition[ 'value' ] = get_post_meta( $post_id, $storage[ 'meta' ], ( $storage['meta_is_single'] ?? true ) );

        if( isset( $field_definition[ 'options' ] )
            && !is_array( $field_definition[ 'value' ] )
            && !isset( $field_definition[ 'options' ][ $field_definition[ 'value' ] ] )
            && !empty( $field_definition[ 'value' ] )
        )
          $field_definition[ 'options' ] = [ $field_definition[ 'value' ] => $field_definition[ 'value' ] ] + $field_definition[ 'options' ];
      }

      if( ( !isset( $field_definition[ 'value' ] ) || $field_definition[ 'value' ] === null ) && isset( $field_definition[ 'default' ] ) )
        $field_definition[ 'value' ] = $field_definition[ 'default' ];

      unset( $field_definition[ 'default' ] );

      $response[ $key ] = $field_definition;
    }

    return apply_filters( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_form_for_render', $response, $field_definitions );
  }

  public static function field_definitions() :array {
    $response = [];

    $response[ 'import_type' ] = [
      'label'       => __( 'Import Type', 'auto-youtube-importer' ),
      'name'        => 'import_type',
      'type'        => 'select',
      'options'     => [
        'channel'  => __( 'Channel', 'auto-youtube-importer' ),
        'playlist' => __( 'Playlist', 'auto-youtube-importer' )
      ],
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_import_type'
      ]
    ];

    $response[ 'channel_id' ] = [
      'label'       => __( 'YouTube Channel ID', 'auto-youtube-importer' ),
      'name'        => 'channel_id',
      'type'        => 'text',
      'required'    => 0,
      'placeholder' => YOUTUBE_IMPORTER_SECONDLINE_EXAMPLE_CHANNEL_ID,
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_youtube_channel_id'
      ]
    ];

    $response[ 'playlist_id' ] = [
      'label'       => __( 'YouTube Playlist ID', 'auto-youtube-importer' ),
      'name'        => 'playlist_id',
      'placeholder' => 'PLm2bYLClU4pGj-3sWzhYJ2Nk-2_PwLNjC',
      'type'        => 'text',
      'required'    => 0,
      'placeholder' => '',
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_youtube_playlist_id'
      ]
    ];

    $post_type_options = [];

    foreach( youtube_importer_secondline_supported_post_types() as $post_type )
      $post_type_options[ $post_type ] = get_post_type_object( $post_type )->labels->singular_name;

    $response[ 'post_type' ] = [
      'label'       => __( 'Post Type', 'auto-youtube-importer' ),
      'name'        => 'post_type',
      'type'        => 'select',
      'options'     => $post_type_options,
      'default'     => youtube_importer_secondline_default_post_type(),
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_post_type'
      ]
    ];

    $response[ 'post_status' ] = [
      'label'       => __( 'Post Status', 'auto-youtube-importer' ),
      'name'        => 'post_status',
      'type'        => 'select',
      'options'     => [
        'publish' => __( 'Publish', 'auto-youtube-importer' ),
        'draft'   => __( 'Save as Draft', 'auto-youtube-importer' )
      ],
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_publish'
      ]
    ];

    $response[ 'post_author' ] = [
      'label'       => __( 'Post Author', 'auto-youtube-importer' ),
      'name'        => 'post_author',
      'type'        => 'wp_dropdown_users',
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_author'
      ]
    ];

    $response[ 'post_taxonomies' ] = [
      'label'       => __( 'Post Category (or Categories)', 'auto-youtube-importer' ),
      'name'        => 'post_taxonomies',
      'type'        => 'multiple_select',
      'options'     => youtube_importer_secondline_get_taxonomies_select_definition( array_keys( $post_type_options ), true ),
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_category'
      ]
    ];

    if( !youtube_importer_secondline_feed_limit_reached() ) {

      $response[ 'import_continuous' ] = [
        'label'           => __( 'Ongoing Import (Enable to continuously import future posts as they are released)', 'auto-youtube-importer'),
        'name'            => 'import_continuous',
        'type'            => 'checkbox',
        'value_unchecked' => 'off',
        'value_checked'   => 'on',
        'storage'         => [
          'type'  => 'meta',
          'meta'  => 'secondline_import_continuous'
        ],
        'views'       => [ 'add' ]
      ];

    }

    $response[ 'import_images' ] = [
      'label'           => __( 'Import Featured Images', 'auto-youtube-importer' ),
      'name'            => 'import_images',
      'type'            => 'checkbox',
      'value_unchecked' => 'off',
      'value_checked'   => 'on',
      'storage'         => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_images'
      ]
    ];

    $response[ 'import_date_from' ] = [
      'label'           => __( 'Date Limit', 'auto-youtube-importer' ),
      'name'            => 'import_date_from',
      'type'            => 'date',
      'placeholder'     => __( '01-01-2022', 'auto-youtube-importer' ),
      'description'     => __( 'Optional: only import posts after a certain date.', 'auto-youtube-importer' ),
      'storage'         => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_date_from'
      ]
    ];

    $response[ 'import_truncate_post' ] = [
      'label'       => __( 'Truncate Post Content', 'auto-youtube-importer' ),
      'name'        => 'import_truncate_post',
      'type'        => 'number',
      'description' => __( 'Optional: Will trim the post content when imported to the character amount below.', 'auto-youtube-importer' ) . 
                       __( 'Leave empty to skip trimming, set to 0 to skip content import.', 'auto-youtube-importer' ),
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_truncate_post'
      ]
    ];

    $response[ 'import_prepend_title' ] = [
      'label'           => __( 'Append text to Post Title', 'auto-youtube-importer' ),
      'name'            => 'import_prepend_title',
      'type'            => 'text',
      'placeholder'     => __( 'Example: My Channel -', 'auto-youtube-importer' ),
      'value_unchecked' => 'off',
      'value_checked'   => 'on',
      'storage'         => [
        'type'  => 'meta',
        'meta'  => 'secondline_prepend_title'
      ]
    ];

    return apply_filters( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_form_definitions', $response );
  }

  public static function request_data_to_meta_map( $request_data ) :array {
    $field_definitions = self::field_definitions();
    $response = [];

    foreach( $field_definitions as $field_definition ) {
      if( !isset( $field_definition[ 'storage' ][ 'meta' ] ) )
        continue;

      if( isset( $request_data[ $field_definition[ 'name' ] ] ) ) {
        if( is_array( $request_data[ $field_definition[ 'name' ] ] ) ) {
          $response[ $field_definition[ 'storage' ][ 'meta' ] ] = array_map( 'sanitize_text_field', $request_data[ $field_definition[ 'name' ] ] );
          continue;
        }

        $response[ $field_definition[ 'storage' ][ 'meta' ] ] = sanitize_text_field( $request_data[ $field_definition[ 'name' ] ] );
        continue;
      }

      if( isset( $field_definition[ 'default' ] ) )
        $request_data[ $field_definition[ 'storage' ][ 'meta' ] ] = $field_definition[ 'default' ];
      else if( isset( $field_definition[ 'value_unchecked' ] ) )
        $request_data[ $field_definition[ 'storage' ][ 'meta' ] ] = $field_definition[ 'value_unchecked' ];
    }

    return apply_filters( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_form_request_data_to_meta_map', $response, $request_data, $field_definitions );
  }

}