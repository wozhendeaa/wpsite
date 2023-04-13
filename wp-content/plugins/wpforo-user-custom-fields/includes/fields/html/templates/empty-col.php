<?php
if (!defined('ABSPATH')) {
    exit();
}
$currentTab = ($tab = filter_input(INPUT_GET, 'tab')) ? $tab : WpforoUcfConstants::TAB_FIELDS;
?>
<div id="wpfucfRowCol-WPFUCF_ROW_I_WPFUCF_COL_J" class="wpfucf-row-col wpfucf-sortable-col-item wpfucf-col-type-2 ui-sortable-handle">
    <div style="text-align:center; font-size:12px; margin-top:-5px; padding:2px;">- <?php _e('column', 'wpforo-ucf'); ?> -</div>
    <div class="wpfucf-row-col-children"></div>    
</div>