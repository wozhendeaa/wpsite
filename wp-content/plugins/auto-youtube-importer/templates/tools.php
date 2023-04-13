<?php
  $current_tab = sanitize_text_field( ( $_GET['tab'] ?? null ) );
  $tabs = [
    'import'  => [
      'title'     => __( "YouTube Import", 'auto-youtube-importer' ),
      'template'  => 'importer-form.php'
    ],
    'scheduled-list'  => [
      'title'     => __( "Scheduled Imports", 'auto-youtube-importer' ),
      'template'  => 'importer-scheduled.php'
    ]
  ];

  if( isset( $_GET[ 'post_id' ] ) && $current_tab === 'edit' )
    $tabs[ 'edit' ] = [
      'title'     => sprintf( __( "Edit Feed %s", 'auto-youtube-importer' ), get_the_title( intval( $_GET[ 'post_id' ] ) ) ),
      'template'  => 'importer-form.php'
    ];

  $tabs[ 'settings' ] = [
    'title'     => __( "Settings", 'auto-youtube-importer' ),
    'template'  => 'settings.php'
  ];

  if( !youtube_importer_secondline_has_premium_theme() && !defined( 'YOUTUBE_IMPORTER_PRO_SECONDLINE' ) )
    $tabs[ 'upgrade' ] = [
      'title'   => __( "Upgrade", 'auto-youtube-importer' ),
      'template'  => 'upgrade-plugin.php'
    ];    

  $tabs = apply_filters( YOUTUBE_IMPORTER_SECONDLINE_ALIAS . '_tools_tabs', $tabs );

  if( $current_tab !== 'settings' && YoutubeImporterSecondLine\Settings::instance()->get( 'youtube_api_key' ) === '' )
    $current_tab = 'settings';

  if( !isset( $tabs[ $current_tab ] ) )
    $current_tab = array_key_first( $tabs );

?><div class="wrap youtube-importer-secondline">
  <h1>
    <span><?php echo esc_html__('Import a YouTube Channel', 'auto-youtube-importer' );?></span>
    <?php if( !youtube_importer_secondline_has_premium_theme() ) :?>
    <a href="https://secondlinethemes.com/?utm_source=yti-title-notice" target="_blank" class="tagline-powered-by">
      <?php echo esc_html__('Powered by SecondLineThemes', 'auto-youtube-importer' );?>
    </a>
    <?php endif;?>
  </h1>

  <nav class="nav-tab-wrapper">
    <?php foreach( $tabs as $tab_alias => $tab_information ) : ?>
      <a href="tools.php?page=<?php echo YOUTUBE_IMPORTER_SECONDLINE_PREFIX; ?>&tab=<?php echo esc_attr($tab_alias) . ( $tab_alias === 'edit' ? '&post_id=' . intval( $_GET[ 'post_id' ] ) : '' ); ?>"
         class="nav-tab<?php echo $tab_alias === $current_tab ? ' nav-tab-active' : '' ?>">
        <?php echo esc_html( $tab_information[ 'title' ] ); ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <?php
    if( isset( $tabs[ $current_tab ][ 'template' ] ) )
      youtube_importer_secondline_load_template( $tabs[ $current_tab ][ 'template' ] );
    else if( isset( $tabs[ $current_tab ][ 'content' ] ) )
      echo ($tabs[ $current_tab ][ 'content' ]);
  ?>
</div>