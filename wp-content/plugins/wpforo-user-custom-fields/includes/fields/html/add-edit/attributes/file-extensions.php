<?php
if (!defined('ABSPATH')) {
    exit();
}
$fileExtensions = is_array($this->fileExtensions) ? implode(',', $this->fileExtensions) : '';
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Allowed extensions', 'wpforo-ucf'); ?>">
    <textarea name="fileExtensions" id="wpfucf-field-add-edit-fileextensions" rows="3" placeholder="<?php _e('Comma separated file extensions ', 'wpforo-ucf'); ?>" required="required"><?php echo esc_html($fileExtensions); ?></textarea>
</div>