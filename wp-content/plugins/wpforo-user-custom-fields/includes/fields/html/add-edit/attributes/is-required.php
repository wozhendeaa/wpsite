<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Is required', 'wpforo-ucf'); ?>">
    <div class="wpfucf-attribute-title"><?php _e('Required?', 'wpforo-ucf'); ?></div>
    <fieldset>
        <div class="wpf-switch-field">            
            <input value="1" name="isRequired" id="wpfucf-field-add-edit-isrequired-1" type="radio" <?php wpfo_check($this->isRequired, 1); ?> /><label for="wpfucf-field-add-edit-isrequired-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>            
            <input value="0" name="isRequired" id="wpfucf-field-add-edit-isrequired-0" type="radio" <?php wpfo_check($this->isRequired, 0); ?> /><label for="wpfucf-field-add-edit-isrequired-0"><?php _e('No', 'wpforo-ucf'); ?></label>
        </div>
    </fieldset>                
</div>