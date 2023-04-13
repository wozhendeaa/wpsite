<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Font awesome icon', 'wpforo-ucf'); ?>">
    <input name="faIcon" id="wpfucf-field-add-edit-faicon" class="wpfucf-field-add-edit-faicon" type="text" value="<?php echo esc_attr($this->faIcon); ?>" placeholder="<?php _e('Pick an icon', 'wpforo-ucf'); ?>"/><span class="wpfucf-icon-preview">&nbsp;</span>
</div>