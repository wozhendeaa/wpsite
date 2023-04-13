<?php
if (!defined('ABSPATH')) {
    exit();
}
$required = isset($this->isRequired) && $this->isRequired ? $requiredHtml : '';
$label = isset($this->label) && ($l = esc_attr(trim($this->label))) ? $l : '&nbsp;';
$type = isset($this->type) && ($t = esc_attr(trim($this->type))) ? __('Type:', 'wpforo-ucf') . " " . $t : '&nbsp;';
$properties = (array) $this;
$data = json_encode($properties, JSON_UNESCAPED_UNICODE);
?>
<div id="wpfucf_<?php echo $this->name; ?>" class="wpfucf-field <?php echo $defaultClass; ?> <?php echo $removableClass; ?> <?php echo $inactiveClass; ?>" data-field-type="<?php echo $this->type; ?>" data-field-uniqueid="<?php echo uniqid(); ?>">
    <div class="wpfucf-field-info wpfucf-field-label" title="<?php echo $label; ?>"><?php echo $label; ?></div>
    <div class="wpfucf-field-info wpfucf-field-type" title="<?php echo $type; ?>"><?php echo $type; ?>&nbsp;<?php echo $required; ?></div>
    <div class="wpfucf-field-actions">
        <a href="#" class="wpfucf-field-action wpfucf-field-action-edit wpfucf-not-clicked <?php echo $this->isTrashed ? 'wpfucf-hide' : ''; ?>" title="<?php _e('Edit field', 'wpforo-ucf'); ?>"><i class="fas fa-pencil-alt"></i></a>
        <a href="#" class="wpfucf-field-action wpfucf-field-action-duplicate <?php echo $this->isTrashed ? 'wpfucf-hide' : ''; ?> <?php echo $duplicateClass; ?>" title="<?php _e('Duplicate field', 'wpforo-ucf'); ?>"><i class="fas fa-copy"></i></a>
        <a href="#" class="wpfucf-field-action wpfucf-field-action-trash wpfucf-not-clicked <?php echo $this->isTrashed ? '' : 'wpfucf-hide'; ?>" title="<?php _e('Remove field', 'wpforo-ucf'); ?>"><i class="far fa-trash-alt"></i></a>        
        <a href="#" class="wpfucf-field-action wpfucf-field-action-move wpfucf-not-clicked" title="<?php _e('Move field', 'wpforo-ucf'); ?>"><i class="fas fa-arrows-alt"></i></a>        
    </div>
    <div class="wpfucf-clear"></div>
    <div class="wpfucf-hide">
        <textarea class="field-json-data" name="<?php echo $this->isTrashed ? 'wpforoucf-trashed' : 'wpforoucf'; ?>[<?php echo $fKey; ?>]"><?php echo $data; ?></textarea>        
    </div>
</div>