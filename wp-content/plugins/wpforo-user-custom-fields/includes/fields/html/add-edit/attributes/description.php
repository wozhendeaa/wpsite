<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Description', 'wpforo-ucf'); ?>">
    <textarea name="description" id="wpfucf-field-add-edit-description" rows="3" placeholder="<?php _e('Description', 'wpforo-ucf'); ?>"><?php echo esc_html($this->description); ?></textarea>
</div>