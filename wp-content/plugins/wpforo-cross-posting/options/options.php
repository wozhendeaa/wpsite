<?php
if (!defined('ABSPATH'))
    exit;

global $wpForoCrossPosting;
$wpForoCPOptions = $wpForoCrossPosting->getOptions();
?>

<div class="wpf-crossposting">
    <div class="wpf-crossposting-tab">
        <button class="wpf-crossposting-tablinks active" data-content="wpf-crossposting-settings"><?php _e('Settings', 'wpforo_cross'); ?></button>
        <button class="wpf-crossposting-tablinks" data-content="wpf-crossposting-autocrossposting"><?php _e('Auto Cross Posting', 'wpforo_cross'); ?></button>
    </div>
    <div id="wpf-crossposting-settings" class="wpf-crossposting-tabcontent" style="display: block;">
        <?php include_once plugin_dir_path(__FILE__) . 'options-main.php'; ?>
    </div>

    <div id="wpf-crossposting-autocrossposting" class="wpf-crossposting-tabcontent">
        <?php include_once plugin_dir_path(__FILE__) . 'options-autoposting.php'; ?>
    </div>
</div>
