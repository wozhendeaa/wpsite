<?php
  $post_id = ( isset( $_GET[ 'post_id' ] ) ? intval( $_GET[ 'post_id' ] ) : null );
  $render_data_list = YoutubeImporterSecondLine\Helper\FeedForm::get_for_render( $post_id );
  $has_any_advanced = false;// Will be changed during first loop.
?>

<div class="main-container-secondline">
  <form method="POST" class="youtube_importer_form">
    <?php if( $post_id !== null ) : ?>
      <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ) ?>">
    <?php endif; ?>
    <?php foreach( $render_data_list as $render_data ) : ?>
      <?php if( isset( $render_data[ 'is_advanced' ] ) && $render_data[ 'is_advanced' ] ) {
              $has_any_advanced = true;
              continue;
            } ?>
      <?php youtube_importer_secondline_load_template( '_form-field.php', [ 'data' => $render_data ] ); ?>
    <?php endforeach; ?>

    <?php if( $has_any_advanced ) : ?>
      <div class="secondline_import_advanced_settings_container">
        <h3 class="secondline_import_advanced_settings_toggle"><i></i><?php echo esc_html__('Advanced Options', 'auto-youtube-importer' ); ?></h3>
        <div class="secondline_import_advanced_settings">
          <?php foreach( $render_data_list as $render_data ) : ?>
            <?php if( !isset( $render_data[ 'is_advanced' ] ) || !$render_data[ 'is_advanced' ] ) continue; ?>

            <?php youtube_importer_secondline_load_template( '_form-field.php', [ 'data' => $render_data ] ); ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if( $post_id !== null ) : ?>
      <button class="button button-primary youtube_importer_form_submit"><?php echo esc_html__( "Update", 'auto-youtube-importer' ); ?></button>
    <?php else : ?>
      <button class="button button-primary youtube_importer_form_submit"><?php echo esc_html__( "Import", 'auto-youtube-importer' ); ?></button>
    <?php endif; ?>
  </form>
  <?php if( !youtube_importer_secondline_has_premium_theme() && !defined( 'YOUTUBE_IMPORTER_PRO_SECONDLINE' ) ) : ?>    
    <div class="upgrade-cta">
      <h2><?php echo esc_html__( "YouTube Importer Pro", 'auto-youtube-importer' ); ?></h2>
      <h5><?php echo esc_html__( "Upgrade to Pro and get additional features:", 'auto-youtube-importer' ); ?></h5>
      <ul>
        <li><?php echo esc_html__( "Unlimited scheduled imports for multiple YouTube channels.", 'auto-youtube-importer' ); ?></li>
        <li><?php echo esc_html__( "Import to any Custom Post Type or Custom Taxonomy.", 'auto-youtube-importer' ); ?></li>
        <li><?php echo esc_html__( "Import video iframes to a custom field.", 'auto-youtube-importer' ); ?></li>
        <li><?php echo esc_html__( "Force a re-sync on all existing posts (to update metadata)", 'auto-youtube-importer' ); ?></li>
        <li><?php echo esc_html__( "Set a global featured image to all imported posts.", 'auto-youtube-importer' ); ?></li>
        <li><?php echo esc_html__( "Manual 'Sync' button to sync on demand.", 'auto-youtube-importer' ); ?></li>
      </ul>
      <a href="https://secondlinethemes.com/wordpress-youtube-importer">
        <button class="button button-primary secondline_upgrade_cta">
          <?php echo esc_html__( "Upgrade", 'auto-youtube-importer' ); ?>
        </button>
      </a>
    </div>
  <?php endif;?>
</div>  