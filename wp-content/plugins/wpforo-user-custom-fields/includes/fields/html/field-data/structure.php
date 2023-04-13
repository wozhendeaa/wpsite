<?php
if (!defined('ABSPATH')) {
    exit();
}
$required = isset($this->isRequired) && $this->isRequired && in_array($currentTab, $includeRequired) ? $requiredHtml : '';
$label = isset($this->label) && ($l = esc_attr(trim($this->label))) ? $l : '&nbsp;';
$type = isset($this->type) && ($t = esc_attr(trim($this->type))) ? __('Type:', 'wpforo-ucf') . " " . $t : '&nbsp;';
$statusInfo = isset($this->isTrashed) && $this->isTrashed ? __('This field is trashed and not visible in any page on frontend', 'wpforo-ucf') : '';
?>
<div id="wpfucfChild_<?php echo $k; ?>" class="wpfucf-field wpfucf-sortable-child-item <?php echo $defaultClass; ?> <?php echo $removableClass; ?> <?php echo $inactiveClass; ?>">
    <div class="wpfucf-field-info wpfucf-field-label" title="<?php echo $label; ?>"><?php echo $label; ?></div>
    <div class="wpfucf-field-info wpfucf-field-type" title="<?php echo $type; ?>"><?php echo $type; ?>&nbsp;<?php echo $required; ?></div>
    <?php if ($this->isTrashed) { ?>
        <div class="wpfucf-field-info wpfucf-field-trashed" title="<?php echo $statusInfo; ?>"><span><?php echo $statusInfo; ?></span></div>
    <?php } ?>
    <div class="wpfucf-field-actions">
        <?php if ($this->info) { ?>
            <a href="#" class="wpfucf-field-action wpfucf-field-action-info" title="<?php echo $this->info; ?>"><i class="fas fa-question"></i></a>
        <?php } ?>
        <a href="#" class="wpfucf-field-action wpfucf-field-action-move" title="<?php _e('Move field', 'wpforo-ucf'); ?>"><i class="fas fa-arrows-alt"></i></a>
    </div>
    <div class="wpfucf-clear"></div>
    <div class="wpfucf-hide">
        <input type="hidden" name="wpforoucf[<?php echo $i; ?>][<?php echo $j; ?>][<?php echo $k; ?>]" value="<?php echo $this->name; ?>" />
    </div>
</div>