<?php
if (!defined('ABSPATH')) {
    exit();
}
$disabled = ($this->isDefault && !$this->isRemovable) ? 'disabled="disabled"' : '';
?>
<div class="wpfucf-field-property-wrapper " title="<?php _e('Field name in form', 'wpforo-ucf'); ?>">
    <input name="name" id="wpfucf-field-add-edit-fieldname" class="field-attr-name" type="text" value="<?php echo esc_attr($this->name); ?>" placeholder="<?php _e('Field Name', 'wpforo-ucf'); ?>" required="required" <?php echo $disabled; ?>/>
</div>