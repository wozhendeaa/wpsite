<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Is label first input last?', 'wpforo-ucf'); ?>">
    <div class="wpfucf-attribute-title"><?php _e('Put a label first, a input last', 'wpforo-ucf'); ?></div>
    <fieldset>
        <div class="wpf-switch-field">            
            <input value="1" name="isLabelFirst" id="wpfucf-field-add-edit-islabelfirst-1" type="radio" <?php wpfo_check($this->isLabelFirst, 1); ?> /><label for="wpfucf-field-add-edit-islabelfirst-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>
            <input value="0" name="isLabelFirst" id="wpfucf-field-add-edit-islabelfirst-0" type="radio" <?php wpfo_check($this->isLabelFirst, 0); ?> /><label for="wpfucf-field-add-edit-islabelfirst-0"><?php _e('No', 'wpforo-ucf'); ?></label>
        </div>
    </fieldset>                
</div>