<?php

$post_list = get_posts( [
  'post_type'    	 => YOUTUBE_IMPORTER_SECONDLINE_POST_TYPE_IMPORT,
  'posts_per_page' => 99,
] );

?>
<div data-secondline-import-notification="info"><?php echo esc_html__('Scheduled imports are set for sync once every two hours.', 'auto-youtube-importer' );?></div>

<?php if( !empty( $post_list ) ) : ?>
  <table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
      <tr>
        <th><?php echo esc_html__( 'Channel', 'auto-youtube-importer' );?></th>
        <th><?php echo esc_html__( 'Channel ID', 'auto-youtube-importer' );?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $post_list as $post ) : ?>
        <tr>
          <td><?php echo esc_html( get_the_title( $post ) ); ?></td>
          <td><?php echo esc_html( get_post_meta($post->ID, 'secondline_youtube_channel_id', true) );?></td>
          <td class="secondline_import_buttons_container">
            <?php do_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_before_feed_item_operations', $post ); ?>
            <a href="tools.php?page=<?php echo YOUTUBE_IMPORTER_SECONDLINE_PREFIX; ?>&tab=edit&post_id=<?php echo esc_attr($post->ID); ?>" class="button button-primary">
              <?php echo esc_html__('Edit Import', 'auto-youtube-importer' );?>
            </a>
            <a href="<?php echo get_delete_post_link( $post->ID, '', true );?>" class="button button-secondary button-delete">
              <?php echo esc_html__('Delete Import', 'auto-youtube-importer' );?>
            </a>
            <?php do_action( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_after_feed_item_operations', $post ); ?>
          </td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
<?php endif; ?>