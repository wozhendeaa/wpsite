<?php
if (!defined('ABSPATH')) {
    exit();
}
$fields = $this->getFields();
$trashedFields = $this->getTrashedFields();
$fields = array_merge($fields, $trashedFields);
if ($type === 'html') {
    $this->fieldsShortcode();
}
?>
<form method="POST" class="wpfucf-form wpfucf-add-member-tab">
    <?php wp_nonce_field('wpfucf-options-nonce'); ?>
    <table class="wpf-addon-table">
        <tr>
            <td>
                <p style="font-size: 20px;margin: 5px 0 10px 0px;"><?php _e('New Tab', 'wpforo-ucf'); ?></p>
                <div id="wpfucf-tab-settings">
                    <div class="wpfucf-tab-option">
                        <div class="wpfucf-tab-settings-label">
                            <label for="wpfucf-tab-title"><?php _e('Title', 'wpforo-ucf'); ?></label>
                        </div>
                        <div class="wpfucf-tab-settings-option">
                            <input type="text" name="wpforoucf[tab-title]" id="wpfucf-tab-title" maxlength="50" required />
                        </div>
                    </div>
                    <div class="wpfucf-tab-option">
                        <div class="wpfucf-tab-settings-label">
                            <label for="wpfucf-tab-key"><?php _e('Key', 'wpforo-ucf'); ?></label>
                        </div>
                        <div class="wpfucf-tab-settings-option">
                            <input type="text" name="wpforoucf[tab-key]" id="wpfucf-tab-key" />
                        </div>
                    </div>
                    <div class="wpfucf-tab-option">
                        <div class="wpfucf-tab-settings-label">
                            <label for="wpfucf-tab-status"><?php _e('Enabled', 'wpforo-ucf'); ?></label>
                        </div>
                        <div class="wpfucf-tab-settings-option">
                            <div class="wpf-switch-field">            
                                <input value="1" name="wpforoucf[tab-status]" id="wpfucf-tab-status-1" type="radio" checked />
                                <label for="wpfucf-tab-status-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>
                                <input value="0" name="wpforoucf[tab-status]" id="wpfucf-tab-status-0" type="radio" />
                                <label for="wpfucf-tab-status-0"><?php _e('No', 'wpforo-ucf'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="wpfucf-tab-option">
                        <div class="wpfucf-tab-settings-label">
                            <label for="wpfucf-tab-ico"><?php _e('Icon', 'wpforo-ucf'); ?></label>
                        </div>
                        <div class="wpfucf-tab-settings-option wpfucf-field-property-wrapper">
                            <input name="wpforoucf[tab-ico]" id="wpfucf-tab-icon" class="wpfucf-tab-faicon" type="text" placeholder="<?php _e('Pick an icon', 'wpforo-ucf'); ?>"/><span class="wpfucf-icon-preview">&nbsp;</span>
                        </div>
                    </div>
                    <div class="wpfucf-tab-option">
                        <div class="wpfucf-tab-settings-label">
                            <label for="wpfucf-tab-add-in-member-buttons"><?php _e('Add In Member Buttons', 'wpforo-ucf'); ?></label>
                        </div>
                        <div class="wpfucf-tab-settings-option">
                            <div class="wpf-switch-field">            
                                <input value="1" name="wpforoucf[tab-add-in-member-buttons]" id="wpfucf-tab-add-in-member-buttons-1" type="radio" />
                                <label for="wpfucf-tab-add-in-member-buttons-1"><?php _e('Yes', 'wpforo-ucf'); ?></label>
                                <input value="0" name="wpforoucf[tab-add-in-member-buttons]" id="wpfucf-tab-add-in-member-buttons-0" type="radio" checked />
                                <label for="wpfucf-tab-add-in-member-buttons-0"><?php _e('No', 'wpforo-ucf'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="wpfucf-tab-option">
                        <div class="wpfucf-tab-settings-label">
                            <label for="wpfucf-tab-allowed-groupids"><?php _e('Who can view?', 'wpforo-ucf'); ?></label>
                        </div>
                        <div class="wpfucf-tab-settings-option">
                            <?php
                            $groups = WPF()->usergroup->usergroup_list_data();
                            ksort($groups);
                            foreach ($groups as $groupid => $group) {
                                if ($groupid == 1) {
                                    continue;
                                }
                                ?>
                                <div class="wpfucf-elem-group-input">
                                    <input type="checkbox" name="wpforoucf[tab-allowed-groupids][]" id="wpfucf-tab-allowed-groupid-<?php echo $groupid; ?>" value="<?php echo $groupid; ?>" checked />
                                    <label for="wpfucf-tab-allowed-groupid-<?php echo $groupid; ?>"><?php echo $group['name']; ?></label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($type === 'html') {
                    ?>
                    <div class="wpfucf-content-wrapper">
                        <?php wp_editor('', 'wpfucf_html_tab', array('textarea_name' => 'wpforoucf[tab-content]', 'tinymce' => array('content_style' => 'html .mceContentBody.wpfucf_html_tab{max-width: 100%;}'))) ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="wpfucf-rows-wrapper wpfucf-sortable">
                        <div id="wpfucfRow-0" class="wpfucf-row wpfucf-sortable-row-item">
                            <div class="wpfucf-row-col-actions">
                                <a href="#" class="wpfucf-row-col-action wpfucf-action-col-1 active" title="<?php _e('1 column', 'wpforo-ucf'); ?>">
                                    <input type="hidden" class="data-wpfucf-col-type" value="1" />
                                </a>
                                <a href="#" class="wpfucf-row-col-action wpfucf-action-col-2" title="<?php _e('2 columns', 'wpforo-ucf'); ?>">
                                    <input type="hidden" class="data-wpfucf-col-type" value="2" />
                                </a>
                                <a href="#" class="wpfucf-row-col-action wpfucf-action-col-3" title="<?php _e('3 columns', 'wpforo-ucf'); ?>">
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
                                <div id="wpfucfRowCol-0_0"  class="wpfucf-row-col wpfucf-sortable-col-item wpfucf-col-type-1">
                                    <div style="text-align:center; font-size:12px; margin-top:-5px; padding:2px;">- <?php _e('column', 'wpforo-ucf'); ?> -</div>
                                    <div class="wpfucf-row-col-children"></div>                                        
                                </div>
                                <div id="wpfucf-cols-anchor" class="wpfucf-clear"></div>
                            </div>
                        </div>
                        <div id="wpfucf-rows-anchor" class="wpfucf-clear"></div>
                        <input type="hidden" class="data-wpfucf-row-index" value="0"/>
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <td style="text-align:right;">
                <button type="submit" name="wpfucf-option-submit" class="button button-primary wpfucf-save-options" value="save"><?php _e('Save', 'wpforo-ucf') ?></button>
                <button type="submit" name="wpfucf-option-submit" class="button button-primary wpfucf-save-options" value="save_and_close"><?php _e('Save &amp; Close', 'wpforo-ucf') ?></button>
            </td>
        </tr>
    </table>
    <div class="wpfucf-clear"></div>
</form>
<?php
if ($type === 'builder') {
    ?>
    <p style="font-size: 20px;margin: 20px 0 0 15px;"><?php _e('Inactive Fields', 'wpforo-ucf'); ?></p>
    <p style="font-size: 14px;margin: 0px 0 0 15px; font-family:Arial"><?php _e('To remove custom fields, just drag and drop fields from Tab Content above to Inactive Fields area below.', 'wpforo-ucf'); ?></p>
    <div class="wpfucf-rows-wrapper wpfucf-sortable wpfucf-inactive">
        <div class="wpfucf-row">
            <div class="wpfucf-row-cols-wrapper" style="text-align: center;">
                <div class="wpfucf-row-col">                    
                    <div class="wpfucf-inactive-fields wpfucf-row-col-children" style="min-height:100px;overflow:hidden;">
                        <?php
                        foreach ($fields as $field) {
                            if (WPF()->form->can_show_value($field)) {
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
    <?php
}