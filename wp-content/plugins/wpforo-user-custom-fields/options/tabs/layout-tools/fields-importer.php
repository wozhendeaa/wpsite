<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-accordion-item ">
    <div class="fas wpfucf-accordion-title">
        <p style="font-size: 20px;margin: 5px 0 10px 15px;"><?php _e('Import Fields From Other Plugins', 'wpforo-ucf'); ?></p>                
    </div>
    <div class="wpfucf-accordion-content">
        <table class="wpf-addon-table">
            <tr>
                <td>
        <!--            <p style="font-size: 20px;margin: 5px 0 10px 0px;"><?php // _e('Import Fields From Plugins', 'wpforo-ucf');   ?></p>                -->
                    <p><?php _e('Using this tool you can migrate custom fields from other plugins', 'wpforo-ucf'); ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <div>                
                        <div class="wpfucf-import-item">
                            <button class="button button-secondary wpfucf-fields-importer wpfucf-bp-fields-importer wpfucf-not-clicked"><?php _e('Import BuddyPress Fields', 'wpforo-ucf'); ?></button>
                            <span class="wpfucf-loading wpfucf-gone"><i class="fa fa-pulse fas-spinner"></i></span>
                            <div class="wpfucf-process"></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="wpfucf-clear"></div>
    </div>
</div>