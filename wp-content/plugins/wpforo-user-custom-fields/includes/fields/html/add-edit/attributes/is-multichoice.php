<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Is drop down multichoice?', 'wpforo-ucf'); ?>">
    <div class="wpfucf-attribute-title"><?php _e('Is multichoice?', 'wpforo-ucf'); ?></div>
    <fieldset>
        <div class="wpf-switch-field">            
            <input value="1" name="isMultiChoice" id="wpfucf-field-add-edit-ismultichoice-1" type="radio" <?php wpfo_check($this->isMultiChoice, 1); ?> /><label for="wpfucf-field-add-edit-ismultichoice-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>            
            <input value="0" name="isMultiChoice" id="wpfucf-field-add-edit-ismultichoice-0" type="radio" <?php wpfo_check($this->isMultiChoice, 0); ?> /><label for="wpfucf-field-add-edit-ismultichoice-0"><?php _e('No', 'wpforo-ucf'); ?></label>
        </div>
    </fieldset>
</div>