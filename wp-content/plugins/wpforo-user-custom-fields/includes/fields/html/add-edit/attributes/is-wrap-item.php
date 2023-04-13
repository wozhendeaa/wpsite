<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Wrap inputs with label?', 'wpforo-ucf'); ?>">
    <div class="wpfucf-attribute-title"><?php _e('Wrap each item with label element', 'wpforo-ucf'); ?></div>
    <fieldset>
        <div class="wpf-switch-field">            
            <input value="1" name="isWrapItem" id="wpfucf-field-add-edit-iswrapitem-1" type="radio" <?php wpfo_check($this->isWrapItem, 1); ?> /><label for="wpfucf-field-add-edit-iswrapitem-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>
            <input value="0" name="isWrapItem" id="wpfucf-field-add-edit-iswrapitem-0" type="radio" <?php wpfo_check($this->isWrapItem, 0); ?> /><label for="wpfucf-field-add-edit-iswrapitem-0"><?php _e('No', 'wpforo-ucf'); ?></label>
        </div>
    </fieldset>                
</div>