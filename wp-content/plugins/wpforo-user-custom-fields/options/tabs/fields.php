<?php
if (!defined('ABSPATH')) {
    exit();
}
$fields = $this->getFields();
$trashedFields = $this->getTrashedFields();
?>
<form method="POST" class="wpfucf-form wpfucf-form-fields">
    <?php wp_nonce_field('wpfucf-options-nonce'); ?>
    <table class="wpf-addon-table">
        <tr>
            <td>
                <div class="wpfucf-rows-wrapper wpfucf-tab-fields wpfucf-sortable">
                    <div>
                        <p style="font-size:20px; margin:5px 0px 0px;"><?php _e('User Fields Manager', 'wpforo-ucf'); ?></p>
                        <p style="font-size:13px; line-height:18px; font-family:Arial;"><b style="color:#FF3300"><?php _e('Tips:', 'wpforo-ucf'); ?></b> &nbsp;<?php _e('Below you can find all profile fields. Fields with light blue background are the default fields. They cannot be deleted, but you can add/remove them from certain forms in next tabs (Register form, Account form, etc...). Please use the [+] button (below all fields) to add new fields. All custom created fields will be displayed with white background. Once you\'ve created new fields and done all necessary customization you can navigate to next tabs for building Register, Account and Member Search Forms, as well as the User Profile page fields. The [reset] button removes all custom created fields and restores the default values of all fields.', 'wpforo-ucf'); ?></p>
                        <p style="font-size:13px; line-height:18px; font-family:Arial;"><b style="color:#FF3300"><?php _e('Note:', 'wpforo-ucf'); ?></b> &nbsp;<?php _e('Here you can manage fields settings (label, description, placeholder, icons...) and add/remove custom fields. This is a field manager page. On other tabs you\'ll be able to use these fields to build certain form. All changes in fields settings made here, affects fields in all forms of next tabs.', 'wpforo-ucf'); ?></p>
                    </div>
                    <div id="wpfucfRow-0" class="wpfucf-row">  
                        <div class="wpfucf-row-cols-wrapper">
                            <div id="wpfucfRowColFields"  class="wpfucf-row-col wpfucf-col-type-1">
                                <div class="wpfucf-row-col-children">
                                    <?php
                                    $i = 0;
                                    foreach ($fields as $field) {
                                        $fKey = $field['fieldKey'];
                                        $fieldObject = WpforoUcfHelper::getFieldObject($field);
                                        echo $fieldObject->fieldsData($fKey);
                                    }
                                    ?>
                                </div>
                                <div class="wpfucf-add-field-popup-wrapper">
                                    <a href="#" class="wpfucf-add-field-popup" title="<?php _e('Add field', 'wpforo-ucf'); ?>">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>                                            
                            </div>
                            <div id="wpfucf-cols-anchor" class="wpfucf-clear"></div>
                        </div>
                    </div>
                    <div id="wpfucf-rows-anchor" class="wpfucf-clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="text-align:right;">
                <?php $adminPostUrl = admin_url('admin-post.php?action=wpfucfResetFields'); ?>
                <a href="<?php echo wp_nonce_url($adminPostUrl, 'wpfucfResetFields'); ?>" class="button button-secondary wpfucf-reset-options" style="margin-left: 5px;float: left;">
                    <?php _e('Reset', 'wpforo-ucf'); ?>
                </a>
                <input type="submit" name="wpfucf-option-submit" class="button button-primary wpfucf-save-options" value="<?php _e('Save Changes', 'wpforo-ucf') ?>">
                <input type="hidden" name="wpfucf-redirect-to" value="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN); ?>">                
            </td>
        </tr>
    </table>
    <div class="wpfucf-clear"></div>
    <p style="font-size: 20px;margin: 20px 0 0 15px;"><?php _e('Trashed Fields', 'wpforo-ucf'); ?></p>
    <div class="wpfucf-rows-wrapper wpfucf-sortable wpfucf-inactive wpfucf-trashed">
        <div class="wpfucf-row">
            <div class="wpfucf-row-cols-wrapper" style="text-align: center;">
                <div class="wpfucf-row-col">
                    <div class="wpfucf-inactive-fields wpfucf-row-col-children" style="min-height:100px;overflow:hidden;">
                        <?php
                        foreach ($trashedFields as $field) {
                            $fKey = $field['fieldKey'];
                            $fieldObject = WpforoUcfHelper::getFieldObject($field);
                            echo $fieldObject->fieldsData($fKey);
                        }
                        ?>
                    </div>
                </div>
                <div class="wpfucf-clear"></div>
            </div>
        </div>
    </div>
</form>