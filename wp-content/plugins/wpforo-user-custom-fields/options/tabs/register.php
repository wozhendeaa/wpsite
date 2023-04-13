<?php
if (!defined('ABSPATH')) {
    exit();
}
$fields = $this->getFields();
$trashedFields = $this->getTrashedFields();
$fields = array_merge($fields, $trashedFields);
$registerFields = $this->getRegisterFields();
?>
<form method="POST" class="wpfucf-form">
    <?php wp_nonce_field('wpfucf-options-nonce'); ?>
    <table class="wpf-addon-table">
        <tr>
            <td>
                <p style="font-size: 20px;margin: 5px 0 10px 0px;"><?php _e('Registration Form', 'wpforo-ucf'); ?></p>
                <p style="font-size: 14px;margin: 0px 0 20px 0px; font-family:Arial; margin-bottom:0px;"><?php _e('This is the structure of user registration form. Below you can see "row panel(s)". By default the registration form consists of one row (panel) with one column of fields.', 'wpforo-ucf'); ?></p>
                <ul style="margin-top:0px; list-style:circle; margin-left:30px; padding-top:5px; margin-bottom:20px;">
                    <li style="margin-bottom: 1px;"><?php _e('Using [|], [||], [|||] buttons on top left side of each row panel you can set one, two and three column form layout for each row individually.', 'wpforo-ucf'); ?></li>
                    <li style="margin-bottom: 1px;"><?php _e('Using [+] green button on top right side of row panel you can add a new row in form.', 'wpforo-ucf'); ?></li>
                    <li style="margin-bottom: 1px;"><?php _e('Drag and drop field panels to set fields sequence (order). Drag and drop row panels to set rows sequence (order).', 'wpforo-ucf'); ?></li>
                    <li style="margin-bottom: 1px;"><?php _e('To add a new custom field in form, just drag and drop fields from Inactive Fields area below to Registration Form area.', 'wpforo-ucf'); ?></li>
                    <li style="margin-bottom: 1px;"><?php _e('To create a new custom field, you should navigate to User Fields Manager tab.', 'wpforo-ucf'); ?></li>
                </ul>
                <div class="wpfucf-rows-wrapper wpfucf-sortable wpfucf-tab-register">
                    <?php
                    for ($i = 0; $i < count($registerFields); $i++) {
                        $row = $registerFields[$i];
                        $colsCount = count($row);
                        $dataI = $i;

                        if ($colsCount >= 3) {
                            $colClass = 'wpfucf-col-type-3';
                        } else {
                            $colClass = "wpfucf-col-type-$colsCount";
                        }
                        ?>
                        <div id="wpfucfRow-<?php echo $i; ?>" class="wpfucf-row wpfucf-sortable-row-item">
                            <div class="wpfucf-row-col-actions">
                                <a href="#" class="wpfucf-row-col-action wpfucf-action-col-1 <?php print ($colsCount == 1) ? 'active' : ''; ?>" title="<?php _e('1 column', 'wpforo-ucf'); ?>">
                                    <input type="hidden" class="data-wpfucf-col-type" value="1" />
                                </a>
                                <a href="#" class="wpfucf-row-col-action wpfucf-action-col-2 <?php print ($colsCount == 2) ? 'active' : ''; ?>" title="<?php _e('2 columns', 'wpforo-ucf'); ?>">
                                    <input type="hidden" class="data-wpfucf-col-type" value="2" />
                                </a>
                                <a href="#" class="wpfucf-row-col-action wpfucf-action-col-3 <?php print ($colsCount == 3) ? 'active' : ''; ?>" title="<?php _e('3 columns', 'wpforo-ucf'); ?>">
                                    <input type="hidden" class="data-wpfucf-col-type" value="3" />
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
                                <?php
                                for ($j = 0; $j < count($row); $j++) {
                                    $col = $row[$j];
                                    $dataK = 0;
                                    ?>
                                    <div id="wpfucfRowCol-<?php echo $i . '_' . $j; ?>"  class="wpfucf-row-col wpfucf-sortable-col-item <?php echo $colClass; ?>">
                                        <div style="text-align:center; font-size:12px; margin-top:-5px; padding:2px;">- <?php _e('column', 'wpforo-ucf'); ?> -</div>
                                        <div class="wpfucf-row-col-children">
                                            <?php
                                            $k = 0;
                                            foreach ($col as $field) {
                                                $fieldObject = WpforoUcfHelper::getFieldObject($field);
                                                echo $fieldObject->formFieldData($i, $j, $k);
                                                $dataK = $k;
                                                $k++;
                                            }
                                            ?>
                                        </div>                                        
                                    </div>
                                    <?php if ($j == count($row) - 1) { ?>
                                        <div id="wpfucf-cols-anchor" class="wpfucf-clear"></div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div id="wpfucf-rows-anchor" class="wpfucf-clear"></div>
                    <input type="hidden" class="data-wpfucf-row-index" value="<?php echo $dataI; ?>"/>
                </div>
            </td>
        </tr>
        <tr>
            <td style="text-align:right;">
                <?php $adminPostUrl = admin_url('admin-post.php?action=wpfucfResetRegisterFields'); ?>
                <a href="<?php echo wp_nonce_url($adminPostUrl, 'wpfucfResetRegisterFields'); ?>" class="button button-secondary wpfucf-reset-options" style="margin-left: 5px;float: left;">
                    <?php _e('Reset', 'wpforo-ucf'); ?>
                </a>
                <input type="submit" name="wpfucf-option-submit" class="button button-primary wpfucf-save-options" value="<?php _e('Save Changes', 'wpforo-ucf') ?>">
                <input type="hidden" name="wpfucf-redirect-to" value="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_REGISTER); ?>">
            </td>
        </tr>
    </table>
    <div class="wpfucf-clear"></div>
</form>
<p style="font-size: 20px;margin: 20px 0 0 15px;"><?php _e('Inactive Fields', 'wpforo-ucf'); ?></p>
<p style="font-size: 14px;margin: 0px 0 0 15px; font-family:Arial"><?php _e('To remove custom fields, just drag and drop fields from Registration Form above to Inactive Fields area below.', 'wpforo-ucf'); ?></p>
<div class="wpfucf-rows-wrapper wpfucf-sortable wpfucf-inactive">
    <div class="wpfucf-row">
        <div class="wpfucf-row-cols-wrapper" style="text-align: center;">
            <div class="wpfucf-row-col">
                <div class="wpfucf-inactive-fields wpfucf-row-col-children" style="min-height:100px;overflow:hidden;">
                    <?php
                    foreach ($fields as $field) {
                        $fKey = $field['fieldKey'];
                        $keys = WpforoUcfHelper::arraySearchByValue($registerFields, $fKey);
                        if (!$keys && WPF()->form->can_show_value($field)) {
                            $fieldObject = WpforoUcfHelper::getFieldObject($field);
                            echo $fieldObject->formFieldData(0, 0, 0);
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="wpfucf-clear"></div>
        </div>
    </div>
</div>