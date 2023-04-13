<?php if( isset( $_POST[ 'youtube_api_key' ] )
          && strpos( $_POST[ 'youtube_api_key' ], '****' ) === false
          && isset( $_POST[ '_youtube_importer_secondline_settings' ] ) ) : ?>
  <?php if( wp_verify_nonce( $_POST[ '_youtube_importer_secondline_settings' ], 'youtube_importer_secondline_settings' ) ) : ?>
    <?php $api_key = sanitize_text_field( $_POST[ 'youtube_api_key' ] ); ?>

    <?php if( YoutubeImporterSecondLine\Helper\Youtube::is_valid_api_key( $api_key ) ) : ?>
      <?php YoutubeImporterSecondLine\Settings::instance()->update( 'youtube_api_key', $api_key ); ?>
    <?php else: ?>
      <div data-secondline-import-notification="warning">
        <?php echo esc_html__( 'A valid youtube API Key is required in order for this plugin to work.', 'auto-youtube-importer' ); ?>
      </div>
    <?php endif; ?>
  <?php else : ?>
    <div data-secondline-import-notification="warning">
      <?php echo esc_html__( 'Nonce has expired.', 'auto-youtube-importer' ); ?>
    </div>
  <?php endif; ?>
<?php else : ?>
  <?php if( YoutubeImporterSecondLine\Settings::instance()->get( 'youtube_api_key' ) === '' ) : ?>
    <div data-secondline-import-notification="warning">
      <?php echo esc_html__( 'A valid youtube API Key is required in order for this plugin to work.', 'auto-youtube-importer' ); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<br/>

<div class="main-container-secondline">
  <form method="POST" class="secondline_settings_form">
    <?php echo wp_nonce_field( 'youtube_importer_secondline_settings', '_youtube_importer_secondline_settings' ); ?>

    <?php youtube_importer_secondline_load_template( '_form-field.php', [ 'data' => [
        'label'           => __( 'API Key', 'auto-youtube-importer' ),
        'name'            => 'youtube_api_key',
        'type'            => 'text',
        'description'     => ( ( ( !isset( $api_key ) && YoutubeImporterSecondLine\Settings::instance()->get('youtube_api_key' ) !== ''  )
                              || ( isset( $api_key ) && $api_key === YoutubeImporterSecondLine\Settings::instance()->get('youtube_api_key' ) )
                            ) ? '<p class="valid-key-flag">' . __( "Success: API Key is Valid", "auto-youtube-importer") . '</p>' : '' ) .
                            "<p><a href='https://secondlinethemes.com/how-to-use-the-youtube-api-and-find-your-api-key/'>Click here to learn how to get your own YouTube API Key.</a></p>",
        'value'           => ( isset( $api_key ) ? $api_key : youtube_importer_secondline_mask( YoutubeImporterSecondLine\Settings::instance()->get('youtube_api_key' ) ) )
    ] ] ); ?>

    <br/>

    <input class="button button-primary" type="submit" name="save_settings" value="<?php echo esc_attr( __( "Save Settings", 'auto-youtube-importer' ) ); ?>"/>
  </form>
</div>