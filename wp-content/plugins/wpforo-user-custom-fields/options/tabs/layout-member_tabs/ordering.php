<?php
if (!defined('ABSPATH')) {
    exit();
}
$tabs = $this->getTabs();
?>
<form method="POST" class="wpfucf-form wpfucf-form-member-tabs">
    <?php wp_nonce_field('wpfucf-options-nonce'); ?>
    <table class="wpf-addon-table">
        <tr>
            <td>
                <div class="wpfucf-rows-wrapper wpfucf-sortable wpfucf-tab-tabs">
                    <div id="wpfucfRow" class="wpfucf-row">  
                        <?php
                        $i = 0;
                        foreach ($tabs as $key => $tab) {
                            ?>
                            <div id="wpfucf_tab_<?php echo $key; ?>" class="wpfucf-tab wpfucf-sortable-row-item wpfucf-sortable-child-item <?php echo ($tab['is_default'] ? 'wpfucf-tab-default' : 'wpfucf-tab-removable') . ' ' . ($tab['status'] ? 'wpfucf-tab-status' : 'wpfucf-tab-disabled'); ?>">
                                <div class="wpfucf-tab-info wpfucf-tab-label">
                                    <span class="wpfucf-tab-icon"><?php echo $tab['ico']; ?></span>
                                    <span class="wpfucf-tab-title"><?php echo $tab['title']; ?></span>
                                    <i class="wpfucf-tab-type"><?php echo '(' . (!$tab['is_default'] ? $tab['type'] : 'default') . ')'; ?></i>
                                </div>
                                <div class="wpfucf-tab-info wpfucf-tab-actions">
                                    <?php
                                    if ($tab['is_default']) {
                                        if (in_array($key, array('profile', 'account'))) {
                                            ?>
                                            <a href="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS . '&action=edit&type=default&key=' . $key); ?>" class="wpfucf-tab-action wpfucf-tab-action-edit wpfucf-not-clicked" title="<?php _e('Edit tab', 'wpforo-ucf'); ?>"><span class="dashicons dashicons-edit-large"></span></a>
                                            <?php
                                        } else {
                                            ?>
                                            <a href="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS . '&action=edit&type=default&key=' . $key); ?>" class="wpfucf-tab-action wpfucf-tab-action-edit wpfucf-not-clicked" title="<?php _e('Manage tab', 'wpforo-ucf'); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wpfucfDeleteTab&key=' . $key), 'wpfucfDeleteTab'); ?>" class="wpfucf-tab-action wpfucf-tab-action-trash wpfucf-not-clicked" title="<?php _e('Remove tab', 'wpforo-ucf'); ?>"><span class="dashicons dashicons-trash"></span></a>        
                                        <a href="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS . '&action=edit&type=' . $tab['type'] . '&key=' . $key); ?>" class="wpfucf-tab-action wpfucf-tab-action-edit wpfucf-not-clicked" title="<?php _e('Edit tab', 'wpforo-ucf'); ?>"><span class="dashicons dashicons-edit-large"></span></a>
                                        <?php
                                    }
                                    if ($tab['status']) {
                                        ?>
                                        <a href="<?php echo wpforo_home_url(wpforo_settings_get_slug($key)); ?>" target="_blank" class="wpfucf-tab-action wpfucf-tab-action-view wpfucf-not-clicked" title="<?php _e('View tab', 'wpforo-ucf'); ?>"><span class="dashicons dashicons-visibility"></span></a>
                                        <?php
                                    } else {
                                        ?>
                                        <a href="#" class="wpfucf-tab-action wpfucf-tab-action-cant-view wpfucf-not-clicked" title="<?php _e('Disabled tab', 'wpforo-ucf'); ?>"><span class="dashicons dashicons-hidden"></span></a>
                                        <?php
                                    }
                                    ?>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=' . ($tab['status'] ? 'wpfucfDisableTab' : 'wpfucfEnableTab') . '&key=' . $key), ($tab['status'] ? 'wpfucfDisableTab' : 'wpfucfEnableTab')); ?>" class="wpfucf-tab-action wpfucf-tab-action-edit wpfucf-not-clicked" title="<?php $tab['status'] ? _e('Disable', 'wpforo-ucf') : _e('Enable', 'wpforo-ucf'); ?>"><?php echo $tab['status'] ? '<span class="dashicons dashicons-yes-alt"></span>' : '<span class="dashicons dashicons-marker"></span>'; ?></a>
                                </div>
                                <input type="hidden" class="wpfucf-member-tab-order" name="wpforoucf[<?php echo $i; ?>]" value="<?php echo $key; ?>">                
                            </div>
                            <?php
                            $i++;
                        }
                        ?>
                    </div>
                    <div id="wpfucf-rows-anchor" class="wpfucf-clear"></div>
                </div>
                <div class="wpfucf-tab-wrapper">
                    <a href="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS . '&action=add&type=html'); ?>" class="wpfucf-add-tab">
                        <span class="dashicons dashicons-plus"></span><span> <?php _e('HTML', 'wpforo-ucf'); ?></span>
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS . '&action=add&type=builder'); ?>" class="wpfucf-add-tab">
                        <span class="dashicons dashicons-plus"></span> <?php _e('Builder', 'wpforo-ucf'); ?>
                    </a>
                </div> 
            </td>
        </tr>
        <tr>
            <td style="text-align:right;">
                <input type="submit" name="wpfucf-option-submit" class="button button-primary wpfucf-save-options" value="<?php _e('Save', 'wpforo-ucf') ?>">
                <input type="hidden" name="wpfucf-redirect-to" value="<?php echo admin_url('admin.php?page=' . self::UCF_PAGE_MAIN . '&tab=' . self::TAB_MEMBER_TABS); ?>">                
            </td>
        </tr>
    </table>
    <div class="wpfucf-clear"></div>
</form>
