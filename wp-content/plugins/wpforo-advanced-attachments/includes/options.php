<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
$groups = WPF()->usergroup->get_usergroups();
?>

<input type="hidden" name="wpfaction" value="wpforo_attach_settings_save">
<div class="wpfa-go-wrap-title"><?php _e( 'Usergroup Based Options', 'wpforo_attach' ) ?></div>
<div class="wpfa-go-wrap">
    <div class="wpfa-go-sidebar">
        <ul class="wpfa-go-tabs">
			<?php
			foreach( $groups as $k => $group ) {
				printf(
					'<li data-groupid="%1$d" class="%2$s">%3$s</li>',
					$group['groupid'],
					( ! $k ? 'active' : '' ),
					$group['name']
				);
			}
			?>
        </ul>
    </div>
    <div class="wpfa-go-main">
		<?php foreach( $groups as $k => $group ) { ?>
            <table id="wpfa-go-group-<?php echo $group['groupid'] ?>" class="wpf-addon-table" style="display: <?php echo( ! $k ? 'block' : 'none' ) ?>">
                <tr>
                    <th class="wpfa-subtitle"><span class="dashicons dashicons-admin-settings"></span></th>
                    <td class="wpfa-subtitle"><?php _e( 'Usergroup', 'wpforo_attach' ) ?> / <?php echo $group['name'] ?></td>
                </tr>
                <tr>
                    <th scope="row" style="width:36%">
                        <label for="accepted_file_types-<?php echo $group['groupid'] ?>">
							<?php _e( 'Accepted File Types', 'wpforo_attach' ) ?>
                        </label>
                    </th>
                    <td>
                            <textarea
                                    id="accepted_file_types-<?php echo $group['groupid'] ?>"
                                    name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][accepted_file_types]"
                                    style="width:100%; height:160px; padding:10px; font-size:13px;"><?php
	                            wpfo(
		                            esc_textarea(
			                            ( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'accepted_file_types' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['accepted_file_types'] : WPF_ATTACH()->default->options['accepted_file_types'] )
		                            )
	                            )
	                            ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="maximum_file_size-<?php echo $group['groupid'] ?>">
							<?php _e( 'Maximum File Size', 'wpforo_attach' ) ?>
                        </label>
                        <p class="wpf-info">
							<?php _e( 'You can not set this value more than "upload_max_filesize" and "post_max_size".', 'wpforo' ); ?>
                        </p>
                    </th>
                    <td>
                        <input
                                id="maximum_file_size-<?php echo $group['groupid'] ?>"
                                class="wpf-field-small"
                                type="number"
                                min="1"
                                max="<?php echo floor( WPF()->settings->_SERVER['maxs_min'] / 1024 ) ?>"
                                name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][maximum_file_size]"
                                value="<?php echo floor( ( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'maximum_file_size' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['maximum_file_size'] : WPF_ATTACH()->default->options['maximum_file_size'] ) / 1024 ) ?>"
                        />&nbsp;KB
                        <p class="wpf-info">
							<?php
							_e( 'Server "upload_max_filesize" is ', 'wpforo_attach' );
							echo WPF()->settings->_SERVER['upload_max_filesize_human'] . '<br/>';
							_e( 'Server "post_max_size" is ', 'wpforo_attach' );
							echo WPF()->settings->_SERVER['post_max_size_human'];
							?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>
							<?php _e( 'Attachment Delete', 'wpforo_attach' ) ?>
                        </label>
                        <p class="wpf-info">
							<?php _e( 'This option work for all users except wp admins', 'wpforo_attach' ); ?>
                        </p>
                    </th>
                    <td>
                        <div class="wpf-switch-field">
                            <input type="radio" value="0"
                                   name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][disable_delete]"
                                   id="wpf_disable_delete-<?php echo $group['groupid'] ?>_0"
								<?php wpfo_check(
									( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'disable_delete' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['disable_delete'] : WPF_ATTACH()->default->options['disable_delete'] ),
									0
								); ?>>
                            <label for="wpf_disable_delete-<?php echo $group['groupid'] ?>_0">
								<?php _e( 'Enable', 'wpforo_attach' ); ?>
                            </label> &nbsp;
                            <input type="radio" value="1"
                                   name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][disable_delete]"
                                   id="wpf_disable_delete-<?php echo $group['groupid'] ?>_1"
								<?php wpfo_check(
									( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'disable_delete' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['disable_delete'] : WPF_ATTACH()->default->options['disable_delete'] ),
									1
								); ?>>
                            <label for="wpf_disable_delete-<?php echo $group['groupid'] ?>_1">
								<?php _e( 'Disable', 'wpforo_attach' ); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>
							<?php _e( 'Attachment control based on attached file owner', 'wpforo_attach' ) ?>
                        </label>
                    </th>
                    <td>
                        <div class="wpf-switch-field">
                            <input type="radio" value="1"
                                   name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][restrict_using_others_attach]"
                                   id="wpf_restrict_using_others_attach-<?php echo $group['groupid'] ?>_1"
								<?php wpfo_check(
									( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'restrict_using_others_attach' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['restrict_using_others_attach'] : WPF_ATTACH()->default->options['restrict_using_others_attach'] ),
									1
								); ?>>
                            <label for="wpf_restrict_using_others_attach-<?php echo $group['groupid'] ?>_1">
								<?php _e( 'Enable', 'wpforo_attach' ); ?>
                            </label> &nbsp;
                            <input type="radio" value="0"
                                   name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][restrict_using_others_attach]"
                                   id="wpf_restrict_using_others_attach-<?php echo $group['groupid'] ?>_0"
								<?php wpfo_check(
									( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'restrict_using_others_attach' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['restrict_using_others_attach'] : WPF_ATTACH()->default->options['restrict_using_others_attach'] ),
									0
								); ?>>
                            <label for="wpf_restrict_using_others_attach-<?php echo $group['groupid'] ?>_0">
								<?php _e( 'Disable', 'wpforo_attach' ); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="max_uploads_per_day-<?php echo $group['groupid'] ?>">
							<?php _e( 'Maximum uploaded attachments per day', 'wpforo_attach' ) ?>
                        </label>
                        <p class="wpf-info"><?php _e( 'Set this option value 0 if you want to remove the limit.', 'wpforo_attach' ); ?></p>
                    </th>
                    <td>
                        <input id="max_uploads_per_day-<?php echo $group['groupid'] ?>"
                               class="wpf-field-small"
                               type="number"
                               min="0"
                               name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][max_uploads_per_day]"
                               value="<?php wpfo(
							       ( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'max_uploads_per_day' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['max_uploads_per_day'] : WPF_ATTACH()->default->options['max_uploads_per_day'] )
						       ) ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="max_attachs_per_post-<?php echo $group['groupid'] ?>">
							<?php _e( 'Maximum attachments per post', 'wpforo_attach' ) ?>
                        </label>
                        <p class="wpf-info"><?php _e( 'Set this option value 0 if you want to remove the limit.', 'wpforo_attach' ); ?></p>
                    </th>
                    <td>
                        <input id="max_attachs_per_post-<?php echo $group['groupid'] ?>"
                               class="wpf-field-small"
                               type="number"
                               min="0"
                               name="wpforo_attach_options[groups][<?php echo $group['groupid'] ?>][max_attachs_per_post]"
                               value="<?php wpfo(
							       ( wpfkey( WPF_ATTACH()->options, 'groups', $group['groupid'], 'max_attachs_per_post' ) ? WPF_ATTACH()->options['groups'][ $group['groupid'] ]['max_attachs_per_post'] : WPF_ATTACH()->default->options['max_attachs_per_post'] )
						       ) ?>"
                        />
                    </td>
                </tr>
            </table>
		<?php } ?>
    </div>
</div>
<table class="wpf-addon-table">
    <tr>
        <th scope="row"><label for="thumbnail_width"><?php _e( 'Thumbnail Max Width', 'wpforo_attach' ) ?></label></th>
        <td><input id="thumbnail_width" class="wpf-field-small" type="number" min="50" name="wpforo_attach_options[thumbnail_width]" value="<?php wpfo( WPF_ATTACH()->options['thumbnail_width'] ) ?>"/>&nbsp;px</td>
    </tr>
    <tr>
        <th scope="row"><label for="thumbnail_height"><?php _e( 'Thumbnail Max Height', 'wpforo_attach' ) ?></label></th>
        <td><input id="thumbnail_height" class="wpf-field-small" type="number" min="50" name="wpforo_attach_options[thumbnail_height]" value="<?php wpfo( WPF_ATTACH()->options['thumbnail_height'] ) ?>"/>&nbsp;px</td>
    </tr>
    <tr>
        <th scope="row"><label for="thumbnail_jpeg_quality"><?php _e( 'Thumbnail JPEG Quality', 'wpforo_attach' ) ?></label></th>
        <td><input id="thumbnail_jpeg_quality" class="wpf-field-small" type="number" min="0" max="100" name="wpforo_attach_options[thumbnail_jpeg_quality]" value="<?php wpfo( WPF_ATTACH()->options['thumbnail_jpeg_quality'] ) ?>">&nbsp</td>
    </tr>
    <tr>
        <th scope="row"><label for="bigimg_max_height"><?php _e( 'Big Image Max Height', 'wpforo_attach' ) ?></label></th>
        <td><input id="bigimg_max_height" class="wpf-field-small" type="number" min="50" name="wpforo_attach_options[bigimg_max_height]" value="<?php wpfo( WPF_ATTACH()->options['bigimg_max_height'] ) ?>"/>&nbsp;px</td>
    </tr>
    <tr>
        <th scope="row"><label for="bigimg_jpeg_quality"><?php _e( 'Big Image JPEG Quality', 'wpforo_attach' ) ?></label></th>
        <td><input id="bigimg_jpeg_quality" class="wpf-field-small" type="number" min="0" max="100" name="wpforo_attach_options[bigimg_jpeg_quality]" value="<?php wpfo( WPF_ATTACH()->options['bigimg_jpeg_quality'] ) ?>">&nbsp;</td>
    </tr>
    <tr>
        <th scope="row"><label for="attachs_per_load"><?php _e( 'Attachments Per Load In Dialog Lazy Load', 'wpforo_attach' ) ?></label></th>
        <td><input id="attachs_per_load" class="wpf-field-small" type="number" min="15" max="100" name="wpforo_attach_options[attachs_per_load]" value="<?php wpfo( WPF_ATTACH()->options['attachs_per_load'] ) ?>"/></td>
    </tr>
    <tr>
        <th scope="row"><label><?php _e( 'Attachment Dialog Auto Upload', 'wpforo_attach' ) ?></label></th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_attach_options[auto_upload]" id="wpf_auto_upload_1" <?php wpfo_check( WPF_ATTACH()->options['auto_upload'], 1 ); ?>><label for="wpf_auto_upload_1"><?php _e( 'Enable', 'wpforo_attach' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_attach_options[auto_upload]" id="wpf_auto_upload_0" <?php wpfo_check( WPF_ATTACH()->options['auto_upload'], 0 ); ?>><label for="wpf_auto_upload_0"><?php _e( 'Disable', 'wpforo_attach' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row"><label>
				<?php _e( 'Thumbnail Box Symmetry', 'wpforo_attach' ) ?></label>
            <p class="wpf-info">
				<?php
				_e( 'Only use this option if the thumbnail width is twice as small as post content width (approx up to 260px)', 'wpforo_attach' );
				?>
            </p>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_attach_options[boxed]" id="wpf_boxed_1" <?php wpfo_check( WPF_ATTACH()->options['boxed'], 1 ); ?>><label for="wpf_boxed_1"><?php _e( 'Enable', 'wpforo_attach' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_attach_options[boxed]" id="wpf_boxed_0" <?php wpfo_check( WPF_ATTACH()->options['boxed'], 0 ); ?>><label for="wpf_boxed_0"><?php _e( 'Disable', 'wpforo_attach' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php _e( 'Attachment Lightbox/Slider', 'wpforo_attach' ) ?></label></th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_attach_options[lightbox]" id="wpf_lightbox_1" <?php wpfo_check( WPF_ATTACH()->options['lightbox'], 1 ); ?>><label for="wpf_lightbox_1"><?php _e( 'Enable', 'wpforo_attach' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_attach_options[lightbox]" id="wpf_lightbox_0" <?php wpfo_check( WPF_ATTACH()->options['lightbox'], 0 ); ?>><label for="wpf_lightbox_0"><?php _e( 'Disable', 'wpforo_attach' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'Secure Attachment URLs', 'wpforo_attach' ) ?></label>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_attach_options[download_via_php]" id="wpf_download_via_php_1" <?php wpfo_check( WPF_ATTACH()->options['download_via_php'], 1 ); ?>><label for="wpf_download_via_php_1"><?php _e( 'Enable', 'wpforo_attach' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_attach_options[download_via_php]" id="wpf_download_via_php_0" <?php wpfo_check( WPF_ATTACH()->options['download_via_php'], 0 ); ?>><label for="wpf_download_via_php_0"><?php _e( 'Disable', 'wpforo_attach' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php _e( 'Attachment Permissions', 'wpforo_attach' ) ?></label></th>
        <td style="font-size:14px; line-height:20px; padding:10px 20px;">
			<?php _e( 'wpForo Advanced Attachment addon is integrated with wpForo Usergroup and Forum Access permissions system. You can control users and guests permissions to attach files (users only) or view attachments (users and guests) using according Access Role in Forums &gt; Settings &gt; Forum Access admin page. Just use <span class="wpfog">Can Attach Files</span> and <span class="wpfog">Can view Attached files</span> permissions of according', 'wpforo' ); ?> <a
                    href="<?php echo admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'settings' ) . '&tab=accesses' ); ?>" style="text-decoration:none;"><?php _e( 'forum access', 'wpforo' ); ?>.</a>
        </td>
    </tr>
    <tr>
        <th scope="row"><label><?php _e( 'Disk Usage', 'wpforo_attach' ) ?></label></th>
        <td style="font-size:14px; line-height:20px; padding:10px 20px;">
			<?php
			$size_avatar = wpforo_dir_size( WPF()->folders['avatars']['dir'] );
			$size_da     = wpforo_dir_size( WPF()->folders['default_attachments']['dir'] );
			$size_aa     = wpforo_dir_size( WPF()->folders['attachments']['dir'] );
			if( function_exists( 'wpforo_print_size' ) ) {
				echo __( 'Avatars', 'wpforo' ) . ': <span class="wpfogx" title="' . WPF()->folders['avatars']['dir'] . '">' . wpforo_print_size( $size_avatar ) . '</span><br>';
				echo __( 'Default attachments' ) . ': <span class="wpfogx" title="' . WPF()->folders['default_attachments']['dir'] . '">' . wpforo_print_size( $size_da ) . '</span><br>';
				echo __( 'Advanced attachments' ) . ': <span class="wpfogx" title="' . WPF()->folders['attachments']['dir'] . '">' . wpforo_print_size( $size_aa ) . '</span><br>';
				$total = $size_aa + $size_da + $size_aa;
				echo __( 'Total' ) . ': <span class="wpfogx" title="' . WPF()->folders['upload']['dir'] . '">' . wpforo_print_size( $total ) . '</span><br>';
			}
			?>
        </td>
    </tr>
    <tr>
        <th colspan="2"><h3 style="font-weight:normal; margin:0; padding:10px 0"><?php _e( 'Front-end Phrases', 'wpforo_attach' ) ?></h3></th>
    </tr>
	<?php if( isset( WPF_ATTACH()->phrases ) && is_array( WPF_ATTACH()->phrases ) ): ?>
		<?php foreach( WPF_ATTACH()->phrases as $key => $phrase ): ?>
            <tr>
                <th style="padding:2px 15px;"><label for="wpfaid<?php echo $key ?>" style="font-weight:normal;"><?php echo ucfirst( $key ) ?></label></th>
                <td style="padding:2px 15px;"><input id="wpfaid<?php echo $key ?>" value="<?php wpfo( $phrase ) ?>" name="wpforo_attach_phrases[<?php echo $key ?>]" type="text" style="width:100%;"></td>
            </tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>
