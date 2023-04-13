<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Display default values', 'wpforo-ucf'); ?>">
    <div class="wpfucf-attribute-title"><?php _e('Display default values?', 'wpforo-ucf'); ?></div>
    <fieldset>
        <div class="wpf-switch-field">            
            <input value="1" name="isDisplayDefaultValues" id="wpfucf-field-add-edit-isdisplaydefaultvalues-1" type="radio" <?php wpfo_check($this->isDisplayDefaultValues, 1); ?> /><label for="wpfucf-field-add-edit-isdisplaydefaultvalues-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>
            <input value="0" name="isDisplayDefaultValues" id="wpfucf-field-add-edit-isdisplaydefaultvalues-0" type="radio" <?php wpfo_check($this->isDisplayDefaultValues, 0); ?> /><label for="wpfucf-field-add-edit-isdisplaydefaultvalues-0"><?php _e('No', 'wpforo-ucf'); ?></label>
        </div>
    </fieldset>                
</div>