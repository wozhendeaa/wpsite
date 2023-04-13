<?php if( ! defined( "ABSPATH" ) ) exit() ?>

    <input type="hidden" name="wpfaction[]" value="slugs_settings_save">
    <input type="hidden" name="wpfaction[]" value="board_settings_save">

<?php
WPF()->settings->header( 'board' );
WPF()->settings->form_field('board', 'under_construction');
WPF()->settings->form_field('board', 'cache');
WPF()->settings->form_field('board', 'url_structure');
?>

<div class="wpf-subtitle">
    <span class="dashicons dashicons-admin-links"></span> <?php _e( 'Permalinks', 'wpforo' ) ?>
    <div class="wpf-opt-doc" style="float: right; font-size: 16px; margin-right: -8px;">
        <a href="https://wpforo.com/docs/wpforo-v2/settings/board-settings/#permalinks" title="<?php _e('Read the documentation', 'wpforo') ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>

<?php
foreach( WPF()->settings->slugs as $_slug => $slug ){
	if( ! wpforo_is_slug_base( $_slug ) ) {
		printf(
			'<div class="wpf-opt-row">
                <div class="wpf-opt-name">
                    <label for="slug_%1$s"> &nbsp; /%1$s</label>
                </div>
                <div class="wpf-opt-input">
                    <input id="slug_%1$s" type="text" value="%2$s" name="slugs[%1$s]" placeholder="%1$s">
                </div>
                <div class="wpf-opt-doc">
                    &nbsp;
                </div>
            </div>',
			$_slug,
			urldecode( $slug )
		);
	}
}
