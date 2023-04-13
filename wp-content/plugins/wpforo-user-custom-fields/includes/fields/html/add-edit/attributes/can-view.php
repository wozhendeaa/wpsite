<?php
if (!defined('ABSPATH')) {
    exit();
}
$groups = WPF()->usergroup->usergroup_list_data();
ksort($groups);
if ($groups && is_array($groups)) {
    ?>
    <div class="wpfucf-field-property-wrapper" style="display:inline-block;" title="<?php _e('Groups who has view permission', 'wpforo-ucf'); ?>">
        <div class="wpfucf-attribute-title"><?php _e('Who can view?', 'wpforo-ucf'); ?></div>
        <fieldset>
            <?php foreach ($groups as $group) { ?>
                <?php if (intval($group['groupid']) == 1) {continue;} ?>
                <div class="wpfucf-elem-group">
                    <div class="wpfucf-elem-group-input">
                        <input id="wpfucf-view-group-<?php echo esc_attr($group['groupid']); ?>" class="wpfucf-can-view-group" type="checkbox" name="canView[]" value="<?php echo esc_attr($group['groupid']); ?>" <?php checked(in_array($group['groupid'], $this->canView)); ?>/>
                        <label for="wpfucf-view-group-<?php echo esc_attr($group['groupid']); ?>"><?php echo esc_html($group['name']); ?></label>
                    </div>
                </div>
            <?php } ?>
        </fieldset>
    </div>
    <?php
}