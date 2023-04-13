<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Label', 'wpforo-ucf'); ?>">
    <input name="label" id="wpfucf-field-add-edit-label" type="text" value="<?php echo esc_attr($this->label); ?>" placeholder="<?php _e('Label', 'wpforo-ucf'); ?>"/>
</div>