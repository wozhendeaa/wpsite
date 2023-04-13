<?php
if (!defined('ABSPATH')) {
    exit();
}
$groups = WPF()->usergroup->usergroup_list_data();
ksort($groups);
if ($groups && is_array($groups)) {
    ?>
    <div class="wpfucf-field-property-wrapper" title="<?php _e('Groups', 'wpforo-ucf'); ?>">
        <div class="wpfucf-attribute-title"><?php _e('Display Usergroups', 'wpforo-ucf'); ?></div>
        <p class="description"><?php _e('This option is only related to User Registration form.<br> In other forms all Usergroups are displayed (only for admins)', 'wpforo-ucf'); ?></p>
        <fieldset>
            <?php foreach ($groups as $group) { ?>
                <?php if ($group['groupid'] != 4) { ?>
                    <div class="wpfucf-elem-group">
                        <div class="wpfucf-elem-group-input">
                            <input id="wpforo-group-<?php echo esc_attr($group['groupid']); ?>" class="wpfucf-allowed-group-ids" type="checkbox" name="allowedGroupIds[]" value="<?php echo esc_attr($group['groupid']); ?>" <?php checked(in_array($group['groupid'], $this->allowedGroupIds)); ?>/>
                            <label for="wpforo-group-<?php echo esc_attr($group['groupid']); ?>"><?php echo esc_html($group['name']); ?></label>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </fieldset>
    </div>
    <?php
}