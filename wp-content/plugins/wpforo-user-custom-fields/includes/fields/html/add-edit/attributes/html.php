<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-field-property-wrapper" title="<?php _e('Html content', 'wpforo-ucf'); ?>">
    <textarea name="html" id="wpfucf-field-add-edit-html" class="wpfucf-field-add-edit-html" rows="10" placeholder="<?php _e('Html', 'wpforo-ucf'); ?>"><?php echo esc_attr(stripslashes(html_entity_decode($this->html))); ?></textarea>
</div>