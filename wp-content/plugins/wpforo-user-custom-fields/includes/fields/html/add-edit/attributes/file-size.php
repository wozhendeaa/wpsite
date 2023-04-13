<?php
if (!defined('ABSPATH')) {
    exit();
}
$disabled = ($this->isDefault && !$this->isRemovable) ? 'disabled="disabled"' : '';
$uploadMaxFileSize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('File size', 'wpforo-ucf'); ?>">
    <input name="fileSize" id="wpfucf-field-add-edit-fieldname" type="number" value="<?php echo esc_attr($this->fileSize); ?>" placeholder="<?php _e('Upload file max size in MB', 'wpforo-ucf'); ?>" <?php echo $disabled; ?> required="required"/>
    <p>
        <?php
        if ($uploadMaxFileSize) {
            echo __('Server "upload_max_filesize" is ', 'wpforo-ucf') . $uploadMaxFileSize . '<br/>';
        }
        if ($postMaxSize) {
            echo __('Server "post_max_size" is ', 'wpforo-ucf') . $postMaxSize . '<br/>';
        }
        ?>
    </p>
</div>