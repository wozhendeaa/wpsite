<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('HTML title attribute', 'wpforo-ucf'); ?>">
    <input name="title" id="wpfucf-field-add-edit-title" type="text" value="<?php echo esc_attr($this->title); ?>" placeholder="<?php _e('Title', 'wpforo-ucf'); ?>" />
</div>