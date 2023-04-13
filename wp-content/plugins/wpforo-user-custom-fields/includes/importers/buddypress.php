<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfBuddypress implements WpforoUcfConstants {

    private $dbManager;
    private $options;
    private $helper;

    public function __construct($dbManager, $options, $helper) {
        $this->dbManager = $dbManager;
        $this->options = $options;
        $this->helper = $helper;
    }

    public function getAvailableFields() {
        $html = '';
        if ($this->isTablesExists()) {
            $fields = $this->getBPFields();
            if ($fields && is_array($fields)) {
                ob_start();
                $wpfucfFields = $this->options->getFields();
                $sourcePlugin = self::UCF_BUDDYPRESS;
                $oldNewRelation = get_option(self::UCF_FIELDS_RELATION . $sourcePlugin);
                $action = 'wpfucfBPImportFields';
                include_once 'available-fields.php';
                $html = ob_get_clean();
            } else {
                $html = __('No custom fields found', 'wpforo-ucf');
            }
        } else {
            $html = __('Plugin tables don\'t exist in current WordPress database.', 'wpforo-ucf');
        }
        return $html;
    }

    private function getTables() {
        $tables = array(
            'bp_xprofile_data',
            'bp_xprofile_fields',
            'bp_xprofile_groups',
            'bp_xprofile_meta',
        );
        return $tables;
    }

    public function wpfucfBPImportFields() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $plugin = isset($data['plugin']) && ($value = trim($data['plugin'])) ? $value : false;
            $fieldIds = isset($data['fieldIds']) && ($value = array_map('trim', $data['fieldIds'])) ? $value : false;
            if ($fieldIds && is_array($fieldIds)) {
                $fields = $this->getBPFields($fieldIds);
                if ($this->addPluginFields($fields, $plugin)) {
                    $response['code'] = 1;
                    $response['data'] = __('Fields are imported', 'wpforo-ucf');
                    $response['updateAction'] = 'wpfucfBPUpdateUsersData';
                }
            } else {
                $response['data'] = __('Please check some fields to start importing', 'wpforo-ucf');
            }
        }
        wp_die(json_encode($response));
    }

    private function isTablesExists() {
        global $wpdb;
        $tables = $this->getTables();
        $isTablesExists = true;
        foreach ($tables as $table) {
            if (!($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'"))) {
                $isTablesExists = false;
                break;
            }
        }
        return $isTablesExists;
    }

    private function addPluginFields($fields, $plugin) {
        $isImported = false;
        $fieldsStructure = array();
        $oldNewRelation = array();
        if ($fields && is_array($fields)) {
            foreach ($fields as $field) {
                switch ($field['type']) {
                    case 'checkbox':
                    case 'radio':
                        $className = WpforoUcfHelper::getFieldClassName($field['type']);
                        if (class_exists($className)) {
                            $field['name'] = WpforoUcfHelper::getFieldName();
                            $field['fieldKey'] = $field['name'];
                            $field['values'] = $this->getBPFieldOptions($field['id']);
                            $field['canView'] = $this->getViewPermissions($field);
                            $fieldsStructure[$field['name']] = $field;
                            $oldNewRelation[$field['id']] = $field['name'];
                        }
                        break;
                    case 'selectbox':
                        $field['type'] = 'select';
                        $className = WpforoUcfHelper::getFieldClassName($field['type']);
                        if (class_exists($className)) {
                            $field['name'] = WpforoUcfHelper::getFieldName();
                            $field['fieldKey'] = $field['name'];
                            $field['values'] = $this->getBPFieldOptions($field['id']);
                            $field['canView'] = $this->getViewPermissions($field);
                            $fieldsStructure[$field['name']] = $field;
                            $oldNewRelation[$field['id']] = $field['name'];
                        }
                        break;
                    case 'multiselectbox':
                        $field['type'] = 'select';
                        $field['isMultiChoice'] = 1;
                        $className = WpforoUcfHelper::getFieldClassName($field['type']);
                        if (class_exists($className)) {
                            $field['name'] = WpforoUcfHelper::getFieldName();
                            $field['fieldKey'] = $field['name'];
                            $field['values'] = $this->getBPFieldOptions($field['id']);
                            $field['canView'] = $this->getViewPermissions($field);
                            $fieldsStructure[$field['name']] = $field;
                            $oldNewRelation[$field['id']] = $field['name'];
                        }
                        break;
                    default :
                        $field['type'] = trim($field['type'], 'box');
                        $className = WpforoUcfHelper::getFieldClassName($field['type']);
                        if (class_exists($className)) {
                            $field['name'] = WpforoUcfHelper::getFieldName();
                            $field['fieldKey'] = $field['name'];
                            $field['canView'] = $this->getViewPermissions($field);
                            $fieldsStructure[$field['name']] = $field;
                            $oldNewRelation[$field['id']] = $field['name'];
                        }
                        break;
                }
            }
        }
        $fieldsStructure = array_filter($fieldsStructure);
        if ($fieldsStructure) {
            $savedFields = $this->options->getFields();
            $merged = array_merge($savedFields, $fieldsStructure);
            if ($merged && is_array($merged)) {
                update_option(self::UCF_OPTION_CUSTOM_FIELDS, $merged);
                update_option(self::UCF_FIELDS_RELATION . $plugin, $oldNewRelation);
                $isImported = true;
            }
        }
        return $isImported;
    }

    private function getViewPermissions($field) {
        $canView = array(1);
        if ($field['canView'] != 'adminsonly') {
            $groups = WPF()->usergroup->usergroup_list_data();
            foreach ($groups as $group) {
                if (($field['canView'] == 'loggedin' && $group['groupid'] == 4) || $group['groupid'] == 1) {
                    continue;
                }
                $canView[] = $group['groupid'];
            }
        }
        return array_filter($canView);
    }

    public function wpfucfBPUpdateUsersData() {
        $response = array('code' => 0, 'plugin' => '', 'offset' => 0, 'itemsPerRequest' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $itemsPerRequest = isset($data['itemsPerRequest']) && ($value = absint($data['itemsPerRequest'])) ? $value : 150;
            $plugin = isset($data['plugin']) && ($value = trim($data['plugin'])) ? $value : '';
            $step = isset($data['step']) && ($value = absint($data['step'])) ? $value : 0;
            $oldNewRelations = get_option('wpfucf_old_new_relation_' . $plugin);
            if ($oldNewRelations) {
                $userIds = $this->getUserIds($itemsPerRequest, $step * $itemsPerRequest);
                foreach ($userIds as $userId) {
                    $userFields = $this->getBPUserFields($userId);
                    if ($userFields && is_array($userFields)) {
                        $wpfucfUserNewFields = array();
                        foreach ($userFields as $userField) {
                            $fieldId = intval($userField->field_id);
                            $fieldValue = @maybe_unserialize($userField->value);
                            if (array_key_exists($fieldId, $oldNewRelations) && $oldNewRelations[$fieldId]) {
                                $wpfucfFieldKey = $oldNewRelations[$fieldId];
                                $wpfucfUserNewFields[$wpfucfFieldKey] = $fieldValue;
                            }
                        }
                        if ($wpfucfUserNewFields) {
                            $wpfucfUserFields = array_merge($wpfucfUserNewFields, wpfucfGetFields($userId));
                            $this->dbManager->addFields($userId, $wpfucfUserFields);
                        }
                    }
                }

                $usersCount = $this->getUsersCount();
                if ($step > 0) {
                    $progress = intval($step * $itemsPerRequest * 100 / $usersCount);
                } else {
                    $progress = intval($itemsPerRequest * 100 / $usersCount);
                }
                $response['code'] = 1;
                $response['step'] = ++$step;
                $response['progress'] = $progress ? $progress : 1;
                $response['plugin'] = $plugin;
            }
        }
        wp_die(json_encode($response));
    }

    /* === DATABASE FUNCTIONS === */

    public function getBPFields($ids = array()) {
        global $wpdb;
        $idsStr = $ids && is_array($ids) ? implode(',', $ids) : '';
        $include = $idsStr ? " AND `f`.`id` IN ($idsStr)" : '';
        $sql = "SELECT `f`.`id`, `f`.`type`, `f`.`name` AS `label`, `f`.`name` AS `title`, `f`.`description`, `f`.`is_required` AS `isRequired`, `fm`.`meta_value` AS `canView` FROM `{$wpdb->prefix}bp_xprofile_fields` AS `f`, `{$wpdb->prefix}bp_xprofile_meta` AS `fm` WHERE `f`.`id` = `fm`.`object_id` AND `f`.`parent_id` = 0 AND `fm`.`object_type` = 'field' AND `fm`.`meta_key` = 'default_visibility' $include ORDER BY `f`.`field_order` ASC;";
        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function getBPFieldOptions($fieldId = 0) {
        global $wpdb;
        $sql = "SELECT `name` AS `options` FROM `{$wpdb->prefix}bp_xprofile_fields` WHERE `parent_id` = %d AND `type` = 'option';";
        $sql = $wpdb->prepare($sql, $fieldId);
        $data = $wpdb->get_col($sql);
        return $data;
    }

    public function getBPUserFields($userId = 0) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT `field_id`, `value` FROM `{$wpdb->prefix}bp_xprofile_data` WHERE `user_id` = %d;", $userId);
        return $wpdb->get_results($sql);
    }

    public function getUserIds($limit, $offset) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT `userid` FROM `{$wpdb->prefix}wpforo_profiles` ORDER BY `userid` ASC LIMIT %d, %d;", $offset, $limit);
        return $wpdb->get_col($sql);
    }

    public function getUsersCount() {
        if (($usersCount = get_transient(self::UCF_USERS_COUNT)) === false) {
            global $wpdb;
            $sql = "SELECT COUNT(`userid`) FROM `{$wpdb->prefix}wpforo_profiles`;";
            $usersCount = $wpdb->get_var($sql);
            set_transient(self::UCF_USERS_COUNT, $usersCount, DAY_IN_SECONDS);
        }
        return $usersCount;
    }

}
