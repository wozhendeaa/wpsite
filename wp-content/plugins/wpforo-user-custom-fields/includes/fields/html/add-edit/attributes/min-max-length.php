<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Minimum length / value', 'wpforo-ucf'); ?>">
    <input name="minLength" id="wpfucf-field-add-edit-minlength" type="number" value="<?php echo esc_attr($this->minLength); ?>" placeholder="<?php _e('Minimum Length / Value', 'wpforo-ucf'); ?>"/>
</div>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Maximum length / value', 'wpforo-ucf'); ?>">
    <input name="maxLength" id="wpfucf-field-add-edit-maxlength" type="number" value="<?php echo esc_attr($this->maxLength); ?>" placeholder="<?php _e('Maximum Length / Value', 'wpforo-ucf'); ?>"/>
</div>