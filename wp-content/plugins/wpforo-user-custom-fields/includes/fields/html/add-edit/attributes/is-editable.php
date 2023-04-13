<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Is this field editable by owner?', 'wpforo-ucf'); ?>">
    <div class="wpfucf-attribute-title"><?php _e('Can user edit this field?', 'wpforo-ucf'); ?></div>
    <fieldset>
        <div class="wpf-switch-field">            
            <input value="1" name="isEditable" id="wpfucf-field-add-edit-iseditable-1" type="radio" <?php wpfo_check($this->isEditable, 1); ?> /><label for="wpfucf-field-add-edit-iseditable-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>
            <input value="0" name="isEditable" id="wpfucf-field-add-edit-iseditable-0" type="radio" <?php wpfo_check($this->isEditable, 0); ?> /><label for="wpfucf-field-add-edit-iseditable-0"><?php _e('No', 'wpforo-ucf'); ?></label>
        </div>
    </fieldset>
</div>