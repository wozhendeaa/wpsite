<?php
if (!defined('ABSPATH')) {
    exit();
}
$values = '';
if ($this->type == 'select') {
    $values = WpforoUcfHelper::createOptgroupFromDD($this->values);
} else { // checkbox, radio
    $values = is_array($this->values) ? implode(PHP_EOL, $this->values) : $this->values;
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Values', 'wpforo-ucf'); ?>">
    <textarea name="values" id="wpfucf-field-add-edit-values" class="" rows="3" required="required" placeholder="<?php _e('Values', 'wpforo-ucf'); ?>"><?php echo esc_html($values); ?></textarea>
    <?php if ($this->type == 'select' && ($this->name == 'timezone' || $this->name == 'location')) { ?>
        <br/><button type="button" id="wpfucf-reset-timezone-location" class="button button-secondary wpfucf-not-clicked" style="margin:5px 0;"><?php _e('Reset values', 'wpforo-ucf'); ?></button>
    <?php } ?>
</div>