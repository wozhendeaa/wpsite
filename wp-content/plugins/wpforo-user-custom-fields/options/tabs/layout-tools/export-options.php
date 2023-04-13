<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wpfucf-accordion-item wpfucf-accordion-current">
    <div class="fas wpfucf-accordion-title">
        <p style="font-size: 20px;margin: 5px 0 10px 15px;"><?php _e('Export Options', 'wpforo-ucf'); ?></p>                
    </div>
    <div class="wpfucf-accordion-content">
        <form method="POST" class="wpfucf-form">
            <?php wp_nonce_field('wpfucf-options-nonce'); ?>
            <table class="wpf-addon-table">
                <tr>
                    <td>
                        <!--<p style="font-size: 20px;margin: 5px 0 10px 0px;"><?php // _e('Export Options', 'wpforo-ucf'); ?></p>-->                
                        <p><?php _e('Using this tool you can migrate or backup/restore user fields and form configurations from one WordPress to another. You can backup and download each form settings individual or all together.', 'wpforo-ucf'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <fieldset>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Export Custom Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-0" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_CUSTOM_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>                        
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Export Register Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-0" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_REGISTER_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Export Account Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-0" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_ACCOUNT_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Export Profile Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-0" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_PROFILE_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <div class="wpfucf-option-item">
                                <div style="margin: 8px 0;"><?php _e('Export Search Fields', 'wpforo-ucf'); ?></div>
                                <div class="wpf-switch-field">
                                    <input value="1" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-1" checked="" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-1"><?php echo $yesText; ?></label>
                                    <input value="0" name="wpfucfExportOptions[<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>]" id="wpfucf-export-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-0" type="radio"><label for="wpfucf-export-options-<?php echo self::UCF_OPTION_SEARCH_FIELDS; ?>-0"><?php echo $noText; ?></label>
                                </div>
                            </div>
                            <?php if (file_exists($wpfucfOptionsDir . '/' . self::UCF_BACKUP_FILE_NAME . '.txt')) { ?>
                                <div class="wpfucf-clear"></div>
                                <div class="wpfucf-option-download">
                                    <a href="<?php echo $wpfucfOptionsUrl . '/' . self::UCF_BACKUP_FILE_NAME . '.txt'; ?>" download="<?php echo self::UCF_BACKUP_FILE_NAME . '.txt'; ?>" class="button button-secondary">
                                        <?php _e('Download Options', 'wpforo-ucf'); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right;">
                        <input type="submit" name="wpfucf-export-submit" class="button button-secondary" value="<?php _e('Backup Options', 'wpforo-ucf'); ?>">
                        <input type="hidden" name="wpfucf-redirect-to" value="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_TOOLS); ?>">
                    </td>
                </tr>
            </table>
            <div class="wpfucf-clear"></div>
        </form>
    </div>
</div>