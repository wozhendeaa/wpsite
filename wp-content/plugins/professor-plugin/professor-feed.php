<?php
/*
Plugin Name: 低级黑维度
Description: 小明教授的专门功能
Version: 1.0
Author: 小明教授
*/

class ProfessorExtention {
    function __construct() {

        add_filter('theme_page_templates', array($this, 'professor_template'));
        
        add_action('admin_menu', array($this, 'addSetting'));
        add_action('admin_init', array($this, 'pluginSettings'));
        
    }


    function professor_template( $page_templates ) {
        $page_templates['professor-feed.php'] = '真相引擎主页';
        return $page_templates;
    }

    function pluginSettings() {
        add_settings_section('wcp_first_section', null, null, 'professor-plugin-setting');
        add_settings_field('展示哪些人的内容', '展示哪些人的内容', array($this, 'settingHTML'), 'professor-plugin-setting', 'wcp_first_section');
        register_setting('feedplugin', '展示哪些人的内容', array('sanitize_callback' => 'sanitize_text_field', 'default' => '专栏作家'));
    }
    
    function addSetting() {
        add_options_page('module settings', 'module setting', 'manage_options', 'professor-plugin-setting', array($this, 'setup_page'));
    }
    
    function settingHTML() {
        $setting_value = get_option('展示哪些人的内容', '专栏作家');
        ?>
       <select name="展示哪些人的内容">
        <!-- Check if the saved value is '专栏作家', and if so, set the option as selected -->
        <option value="专栏作家" <?php selected($setting_value, '专栏作家'); ?>>专栏作家</option>
        <!-- Check if the saved value is '所有人', and if so, set the option as selected -->
        <option value="所有人" <?php selected($setting_value, '所有人'); ?>>所有人</option>
    </select>
    <?php
    }
    
    function setup_page() {
        ?>
        <div class="wrap">
            <h1>展示哪些人的内容</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('feedplugin');
                do_settings_sections('professor-plugin-setting');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

}

$professorEXT = new ProfessorExtention();



?>
