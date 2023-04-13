<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Field information', 'wpforo-ucf'); ?>">
    <textarea name="info" id="wpfucf-field-add-edit-info" rows="3" placeholder="<?php _e('What for this field? (shows information about the field in other tabs)', 'wpforo-ucf'); ?>"><?php echo esc_attr($this->info); ?></textarea>
</div>