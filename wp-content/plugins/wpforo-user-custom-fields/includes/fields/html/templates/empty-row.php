<?php
if (!defined('ABSPATH')) {
    exit();
}
$currentTab = ($tab = filter_input(INPUT_GET, 'tab')) ? $tab : WpforoUcfConstants::TAB_FIELDS;
?>
<div id="wpfucfRow-WPFUCF_ROW_I" class="wpfucf-row wpfucf-sortable-row-item ui-sortable-handle">
    <div class="wpfucf-row-col-actions">
        <a href="#" class="wpfucf-row-col-action wpfucf-action-col-1 active" title="<?php _e('1 column', 'wpforo-ucf'); ?>">
            <input class="data-wpfucf-col-type" value="1" type="hidden">
        </a>
        <a href="#" class="wpfucf-row-col-action wpfucf-action-col-2" title="<?php _e('2 columns', 'wpforo-ucf'); ?>">
            <input class="data-wpfucf-col-type" value="2" type="hidden">
        </a>
        <a href="#" class="wpfucf-row-col-action wpfucf-action-col-3" title="<?php _e('3 columns', 'wpforo-ucf'); ?>">
            <input class="data-wpfucf-col-type" value="3" type="hidden">
        </a>
        <div class="wpfucf-clear"></div>
    </div>
    <div class="wpfucf-row-actions">
        <a href="#" class="wpfucf-row-action wpfucf-row-action-move" title="<?php _e('Move row', 'wpforo-ucf'); ?>"><i class="fas fa-arrows-alt"></i></a>
        <a href="#" class="wpfucf-row-action wpfucf-row-action-trash" title="<?php _e('Remove row', 'wpforo-ucf'); ?>"><i class="far fa-trash-alt"></i></a>
        <a href="#" class="wpfucf-row-action wpfucf-row-action-add" title="<?php _e('Add row', 'wpforo-ucf'); ?>"><i class="fas fa-plus"></i></a>
        <div class="wpfucf-clear"></div>
    </div>
    <div class="wpfucf-clear"></div>
    <div class="wpfucf-row-cols-wrapper">
        <div style="text-align:center; font-size:12px; margin-top:-20px; padding:2px; padding-bottom:4px; margin-right:10px;">- <?php _e('row', 'wpforo-ucf'); ?> -</div>
        <div id="wpfucfRowCol-WPFUCF_ROW_I_0" class="wpfucf-row-col wpfucf-sortable-col-item wpfucf-col-type-1 ui-sortable-handle">
            <div style="text-align:center; font-size:12px; margin-top:-5px; padding:2px;">- <?php _e('column', 'wpforo-ucf'); ?> -</div>
            <div class="wpfucf-row-col-children"></div>
        </div>
        <div id="wpfucf-cols-anchor" class="wpfucf-clear"></div>
    </div>
</div>