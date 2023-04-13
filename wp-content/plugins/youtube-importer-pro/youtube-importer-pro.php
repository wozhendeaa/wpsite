<?php
/**
 * Plugin Name:       YouTube Importer Pro
 * Description:       Pro version of the YouTube Importer plugin
 * Version:           1.0.2
 * Author:            SecondLineThemes
 * Author URI:        https://secondlinethemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       youtube-importer-secondline-pro
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) )
	die;

if ( !defined( 'YTI_PRO_STORE_URL_PLUGIN_FILE' ) ) {
  define( 'YTI_PRO_STORE_URL_PLUGIN_FILE', __FILE__ );
}

if ( !defined( 'YOUTUBE_IMPORTER_PRO_SECONDLINE' ) ) {
  define( 'YOUTUBE_IMPORTER_PRO_SECONDLINE', 'youtube_importer_pro_secondline' );
}

require_once "inc/licensing.php";



add_action( 'plugins_loaded', function() {
  if( !defined( 'YOUTUBE_IMPORTER_SECONDLINE_ALIAS' ) )
    return;

  add_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_before_feed_item_operations', function( WP_Post $post ) {
    echo '<a class="button button-secondary" 
             data-secondline-import-success-message="' . esc_attr( __( 'Synced', 'youtube-importer-secondline-pro' ) ) . '"
             data-youtube-importer-rest-api-request="' . YOUTUBE_IMPORTER_SECONDLINE_REST_API_PREFIX . '/v1/sync-feed/' . $post->ID . '">' .
            __( 'Sync Now', 'youtube-importer-secondline-pro' ) .
         '</a>';
  });

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_cron_limit', function() {
    return 9999;
  } );

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_supported_post_types', function( $response ) {
    $post_types = get_post_types([ 'public' => true, 'show_in_nav_menus' => true ] );

    foreach ( $post_types as $post_type ) {
      if( in_array( $post_type, [ 'elementor_library', 'secondline_psb_post', 'secondline_import', 'secondline_shows', 'attachment', 'product', 'page' ] ) )
        continue;

      if( in_array( $post_type, $response ) )
        continue;

      $response[] = $post_type;
    }

    return $response;
  });

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_form_definitions', function( $form_definitions ) {
    $form_definitions[ 'import_allow_sync' ] = [
      'label'           => __( 'Re-sync details for already imported content.', 'youtube-importer-secondline-pro'),
      'name'            => 'import_allow_sync',
      'type'            => 'checkbox',
      'value_unchecked' => 'off',
      'value_checked'   => 'on',
      'storage'         => [
        'type'  => 'meta',
        'meta'  => 'secondline_import_allow_sync'
      ]
    ];    

    $form_definitions[ 'pro_default_image_post_id' ] = [
      'label'   => __( "Set a global featured image to all posts", 'youtube-importer-secondline-pro' ),
      'name'    => 'pro_default_image_post_id',
      'type'    => 'media_image_id',
      'storage' => [
        'type'  => 'meta',
        'meta'  => 'secondline_pro_default_image_post_id'
      ]
    ];

    $form_definitions[ 'pro_import_player_meta' ] = [
      'label'   => __( "Import YouTube video player into a custom meta field", 'youtube-importer-secondline-pro' ),
      'name'    => 'pro_import_player_meta',
      'type'    => 'text',
      'storage' => [
        'type'  => 'meta',
        'meta'  => 'secondline_pro_import_player_meta'
      ]
    ];

    $tag_post_type_data = [];

    foreach( youtube_importer_secondline_supported_post_types() as $post_type ) {
      $post_type_taxonomies = get_object_taxonomies( $post_type );

      foreach( $post_type_taxonomies as $post_type_taxonomy ) {
        if( $post_type_taxonomy === 'post_format' )
          continue;

        $taxonomy_information = get_taxonomy( $post_type_taxonomy );

        if( boolval( $taxonomy_information->hierarchical ) !== false )
          continue;

        if( isset( $tag_post_type_data[ $post_type_taxonomy ] ) ) {
          $tag_post_type_data[ $post_type_taxonomy ][ 'data-post-types' ] .= ' ' . $post_type;

          continue;
        }

        $tag_post_type_data[ $post_type_taxonomy ] = [
          'data-post-types' => $post_type,
          'label'           => $taxonomy_information->name
        ];
      }
    }

    $form_definitions[ 'pro_import_keywords_as_tags' ] = [
      'label'       => __( 'Import Keywords as Tags', 'youtube-importer-secondline-pro' ),
      'name'        => 'pro_import_keywords_as_tags',
      'type'        => 'multiple_select',
      'options'     => $tag_post_type_data,
      'class'       => 'youtube-importer-post-filter',
      'storage'     => [
        'type'  => 'meta',
        'meta'  => 'secondline_pro_import_keywords_as_tags'
      ]
    ];

    return $form_definitions;
  });

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_importer_settings_from_meta_map', function( $settings, $meta_map ) {
    if( isset( $meta_map[ 'secondline_pro_import_player_meta' ] )
        && is_string( $meta_map[ 'secondline_pro_import_player_meta' ] )
        && !empty( $meta_map[ 'secondline_pro_import_player_meta' ] )
    )
      $settings[ 'pro_import_player_meta' ] = $meta_map[ 'secondline_pro_import_player_meta' ];

    if( isset( $meta_map[ 'secondline_pro_default_image_post_id' ] ) && is_numeric( $meta_map[ 'secondline_pro_default_image_post_id' ] ) )
      $settings[ 'pro_default_image_post_id' ] = $meta_map[ 'secondline_pro_default_image_post_id' ];

    if( isset( $meta_map[ 'secondline_pro_import_keywords_as_tags' ] ) && !empty( $meta_map[ 'secondline_pro_import_keywords_as_tags' ] ) )
      $settings[ 'pro_import_keywords_as_tags' ] = $meta_map[ 'secondline_pro_import_keywords_as_tags' ];

    return $settings;
  }, 100, 2 );

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_item_has_player_in_content', function( $response, YoutubeImporterSecondLine\Helper\Importer\FeedItem $feedItem ) {
    if( isset( $feedItem->importer->additional_settings[ 'pro_import_player_meta' ] )
      && !empty( $feedItem->importer->additional_settings[ 'pro_import_player_meta' ] )
      && !empty( $feedItem->player_embed_html ) )
      return false;

    return $response;
  }, 100, 2 );

  add_filter( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_item_import_category_map', function( $categories_import_map, YoutubeImporterSecondLine\Helper\Importer\FeedItem $feedItem ) {
    if( !isset( $feedItem->importer->additional_settings[ 'pro_import_keywords_as_tags' ] )
        || empty( $feedItem->importer->additional_settings[ 'pro_import_keywords_as_tags' ] ) )
      return $categories_import_map;

    if( !isset( $feedItem->feed_item[ 'snippet' ][ 'tags' ] ) || empty( $feedItem->feed_item[ 'snippet' ][ 'tags' ] ) )
      return $categories_import_map;

    foreach( $feedItem->importer->additional_settings[ 'pro_import_keywords_as_tags' ] as $taxonomy ) {
      if( is_numeric( $taxonomy ) )
        continue;

      $categories_import_map[ $taxonomy ] = $feedItem->feed_item[ 'snippet' ][ 'tags' ];
    }

    return $categories_import_map;
  }, 100, 2 );

  add_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_feed_item_imported', function( YoutubeImporterSecondLine\Helper\Importer\FeedItem $feedItem ) {
    if( isset( $feedItem->importer->additional_settings[ 'pro_import_player_meta' ] )
        && !empty( $feedItem->importer->additional_settings[ 'pro_import_player_meta' ] )
        && !empty( $feedItem->player_embed_html ) )
      update_post_meta( $feedItem->current_post_id, $feedItem->importer->additional_settings[ 'pro_import_player_meta' ], $feedItem->player_embed_html );

    if( isset( $feedItem->importer->additional_settings[ 'pro_default_image_post_id' ] ) && is_numeric( $feedItem->importer->additional_settings[ 'pro_default_image_post_id' ] ) )
      if( empty( get_post_thumbnail_id( $feedItem->current_post_id ) ) )
        set_post_thumbnail($feedItem->current_post_id, $feedItem->importer->additional_settings[ 'pro_default_image_post_id' ] );
  });

}, 9999 );

