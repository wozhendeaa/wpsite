<?php
if (!defined('ABSPATH')) {
    exit();
}
$structure = $this->options->getFields();
$isPasswordDisabled = '';
$isAvatarDisabled = '';
$isUsergroupDisabled = '';
if ($structure) {
    $fields = $structure;
    foreach ($fields as $k => $v) {
        if (!$isPasswordDisabled && array_search('user_pass', $v)) {
            $isPasswordDisabled = 'disabled="disabled"';
        }
        if (!$isAvatarDisabled && array_search('avatar', $v)) {
            $isAvatarDisabled = 'disabled="disabled"';
        }
        if (!$isUsergroupDisabled && array_search('groupid', $v)) {
            $isUsergroupDisabled = 'disabled="disabled"';
        }
    }
}
?>
<a id='wpfucfPopupFieldTypesAnchor' style='display:none;' href='#wpfucfPopupFieldTypes' data-wpfucf-lity></a>
<div id='wpfucfPopupFieldTypes' style='overflow:auto;background:#f1f1f1;padding:20px;width:600px;max-width:100%;border-radius:6px' class='lity-hide'>
    <h2 class="wpfucf-title"><?php _e('Fields', 'wpforo-ucf'); ?><div class="wpfucf-action-msg wpfucf-hide"><?php _e('Field added successfully', 'wpforo-ucf'); ?></div></h2>
    <span class="wpfucf-loading wpfucf-gone"><i class="fas fa-pulse fa-spinner"></i></span>
    <div class="wpfucf-fields-wrapper">
        <div class="wpfucf-field-type-wrapper"><button type="button" id="email" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="far fa-envelope"></i><?php _e('Email', 'wpforo-ucf'); ?></button></div>
        <!--<div class="wpfucf-field-type-wrapper"><button type="button" id="password" class="button button-secondary wpfucf-field-type wpfucf-not-clicked" <?php // echo $isPasswordDisabled; ?>><i class="fas fa-unlock"></i><?php // _e('Password', 'wpforo-ucf'); ?></button></div>-->
        <div class="wpfucf-field-type-wrapper"><button type="button" id="text" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-align-justify"></i><?php _e('Text', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="textarea" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-align-justify"></i><?php _e('Textarea', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="tel" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-phone"></i><?php _e('Phone', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="number" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-sort-numeric-down"></i><?php _e('Number', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="select" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-caret-down"></i><?php _e('Drop Down', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="url" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-link"></i><?php _e('Url', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="date" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-calendar-alt"></i><?php _e('Date', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="radio" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="far fa-dot-circle"></i><?php _e('Radio', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="checkbox" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="far fa-check-circle"></i><?php _e('Checkbox', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="html" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-code"></i><?php _e('HTML', 'wpforo-ucf'); ?></button></div>
        <!--<div class="wpfucf-field-type-wrapper"><button type="button" id="avatar" class="button button-secondary wpfucf-field-type wpfucf-not-clicked" <?php // echo $isAvatarDisabled; ?>><i class="fas fa-user"></i><?php // _e('Avatar', 'wpforo-ucf'); ?></button></div>-->
        <!--<div class="wpfucf-field-type-wrapper"><button type="button" id="usergroup" class="button button-secondary wpfucf-field-type wpfucf-not-clicked" <?php // echo $isUsergroupDisabled; ?>><i class="fas fa-users"></i><?php // _e('Usergroup', 'wpforo-ucf'); ?></button></div>-->
        <div class="wpfucf-field-type-wrapper"><button type="button" id="file" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-upload"></i><?php _e('File Upload', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="color" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-paint-brush"></i><?php _e('Color Picker', 'wpforo-ucf'); ?></button></div>
        <div class="wpfucf-field-type-wrapper"><button type="button" id="range" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-sliders-h"></i><?php _e('Range', 'wpforo-ucf'); ?></button></div>
        <!--<div class="wpfucf-field-type-wrapper"><button type="button" id="numberInterval" class="button button-secondary wpfucf-field-type wpfucf-not-clicked"><i class="fas fa-exchange-alt"></i><?php // _e('Number Interval', 'wpforo-ucf'); ?></button></div>-->
    </div>
</div>