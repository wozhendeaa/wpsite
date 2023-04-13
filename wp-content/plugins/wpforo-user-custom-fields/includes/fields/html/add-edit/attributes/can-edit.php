<?php
if (!defined('ABSPATH')) {
    exit();
}
$groups = WPF()->usergroup->usergroup_list_data();
ksort($groups);
if ($groups && is_array($groups)) {
    ?>
    <div class="wpfucf-field-property-wrapper" style="display:inline-block;margin-left:50px; vertical-align: top;" title="<?php _e('Usergroups who have user editing permission', 'wpforo-ucf'); ?>">
        <div class="wpfucf-attribute-title"><?php _e('Who can moderate?', 'wpforo-ucf'); ?></div>
        <fieldset>
            <?php foreach ($groups as $group) { ?>
                <?php if( intval($group['groupid']) == 4 || intval($group['groupid']) == 1 ) {continue;} ?>
                <div class="wpfucf-elem-group">
                    <div class="wpfucf-elem-group-input">
                        <input id="wpfucf-edit-group-<?php echo esc_attr($group['groupid']); ?>" class="wpfucf-can-edit-group" type="checkbox" name="canEdit[]" value="<?php echo esc_attr($group['groupid']); ?>" <?php checked(in_array($group['groupid'], $this->canEdit)); ?>/>
                        <label for="wpfucf-edit-group-<?php echo esc_attr($group['groupid']); ?>"><?php echo esc_html($group['name']); ?></label>
                    </div>
                </div>
            <?php } ?>
        </fieldset>
    </div>
    <?php
}