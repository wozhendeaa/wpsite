<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wpfucf-accordion-item ">
    <div class="fas wpfucf-accordion-title">
        <p style="font-size: 20px;margin: 5px 0 10px 15px;"><?php _e('Import Options', 'wpforo-ucf'); ?></p>
    </div>
    <div class="wpfucf-accordion-content">
        <form method="POST" class="wpfucf-form" enctype="multipart/form-data">
            <?php wp_nonce_field('wpfucf-options-nonce'); ?>
            <table class="wpf-addon-table">
                <tr>
                    <td>
        <!--                <p style="font-size: 20px;margin: 5px 0 10px 0px;"><?php // _e('Import Options', 'wpforo-ucf');   ?></p>                -->
                        <p><?php _e('Here you can import custom field options. Using this section you can restore custom fields and forms configuration.', 'wpforo-ucf'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <fieldset>
                            <div class="wpfucf-option-item">                        
                                <div style="margin: 8px 0;"><?php _e('Import Custom Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-0" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>                        
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Import Register Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-0" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Import Account Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-0" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Import Profile Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-0" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Import Search Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfImportOptions[<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>]" id="wpfucf-import-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-0" type="radio"><label for="wpfucf-import-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wpfucf-option-item">
                            <div>
                                <input type="file" name="wpfucfImportOptions" value="" />
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right;">
                        <input type="submit" name="wpfucf-import-submit" class="button button-secondary" value="<?php _e('Import Options', 'wpforo-ucf'); ?>">
                        <input type="hidden" name="wpfucf-redirect-to" value="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_TOOLS); ?>">
                    </td>
                </tr>
            </table>
            <div class="wpfucf-clear"></div>
        </form>
    </div>
</div>