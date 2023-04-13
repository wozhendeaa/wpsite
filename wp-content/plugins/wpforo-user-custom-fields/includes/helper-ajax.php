<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfHelperAjax implements WpforoUcfConstants {

    private $dbManager;
    private $options;
    private $helper;

    public function __construct($dbManager, $options, $helper) {
        $this->dbManager = $dbManager;
        $this->options = $options;
        $this->helper = $helper;
        add_action('wp_ajax_wpfucfAddPopup', array(&$this, 'addPopup'));
        add_action('wp_ajax_wpfucfEditPopup', array(&$this, 'editPopup'));
        add_action('wp_ajax_wpfucfAddField', array(&$this, 'addField'));
        add_action('wp_ajax_wpfucfSaveField', array(&$this, 'saveField'));
        add_action('wp_ajax_wpfucfDuplicateField', array(&$this, 'duplicateField'));
        add_action('wp_ajax_wpfucfResetTimezoneLocation', array(&$this, 'resetTimezoneLocation'));
        add_action('wp_ajax_wpfucfFieldsToImportPopup', array(&$this, 'wpfucfFieldsToImportPopup'));
    }

    public function addPopup() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $type = isset($data['type']) && ($value = trim($data['type'])) ? $value : false;
            $object = WpforoUcfHelper::getFieldObject($data);
            if ($object) {
                $object->setName(WpforoUcfHelper::getFieldName());
                $response['code'] = 1;
                $response['data'] = $object->addFieldPopupHtml();
            } else {
                $response['data'] = __('Unknown error occured', 'wpforo-ucf');
            }
        }
        wp_die(json_encode($response));
    }

    public function editPopup() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $type = isset($data['type']) && ($value = trim($data['type'])) ? $value : false;
            $object = WpforoUcfHelper::getFieldObject($data);
            if ($object) {
                $response['code'] = 1;
                $response['data'] = $object->editFieldPopupHtml();
            } else {
                $response['data'] = __('Unknown error occured', 'wpforo-ucf');
            }
        }
        wp_die(json_encode($response));
    }

    public function addField() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $type = isset($data['type']) && ($value = trim($data['type'])) ? $value : false;
            $object = WpforoUcfHelper::getFieldObject($data);
            $name = isset($data['name']) && ($value = trim($data['name'])) ? $value : false;
//            if ($name && $this->helper->isValidFieldName($name)) {
            if ($name) {
                $response['code'] = 1;
                $response['data'] = $object->fieldsData($name);
            } else {
                $response['error'] = __('Unknown error occured', 'wpforo-ucf');
            }
//            } else {
//                $response['error'] = __('Field with this name already exists!', 'wpforo-ucf');
//            }
        }
        wp_die(json_encode($response));
    }

    public function saveField() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $type = isset($data['type']) && ($value = trim($data['type'])) ? $value : false;
            $object = WpforoUcfHelper::getFieldObject($data);
            $name = isset($data['name']) && ($value = trim($data['name'])) ? $value : false;
//            if ($name && $this->helper->isValidFieldName($name)) {
            if ($object && $name) {
                $response['code'] = 1;
                $response['name'] = $name;
                $response['data'] = $object->fieldsData($name);
            } else {
                $response['error'] = __('Unknown error occured', 'wpforo-ucf');
            }
//            } else {
//                $response['error'] = __('Field with this name already exists!', 'wpforo-ucf');
//            }
        }
        wp_die(json_encode($response));
    }

    public function duplicateField() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $name = WpforoUcfHelper::getFieldName();
            $data['isDefault'] = 0;
            $data['isRemovable'] = 1;
            $data['name'] = $name;
            $data['fieldKey'] = $name;
            $object = WpforoUcfHelper::getFieldObject($data);
            if ($object) {
                $response['code'] = 1;
                $response['data'] = $object->fieldsData($name);
            } else {
                $response['error'] = __('Unknown error occured', 'wpforo-ucf');
            }
        }
        wp_die(json_encode($response));
    }

    public function resetTimezoneLocation() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $type = isset($data['type']) && ($value = trim($data['type'])) ? $value : false;
            $name = isset($data['name']) && ($value = trim($data['name'])) ? $value : false;
            $isDefault = isset($data['isDefault']) && ($value = intval($data['isDefault'])) ? $value : false;
            $object = WpforoUcfHelper::getFieldObject($data);
            if ($object && ($type == 'select' && $isDefault && ($name == 'timezone' || $name == 'location'))) {
                $wpforoField = WPF()->member->get_field($name);
                if ($wpforoField) {
                    $values = isset($wpforoField['values']) ? $wpforoField['values'] : '';
                    if ($values) {
                        $response['code'] = 1;
                        $response['data'] = WpforoUcfHelper::createOptgroupFromDD($values);
                    }
                }
            } else {
                $response['error'] = __('Unknown error occured', 'wpforo-ucf');
            }
        }
        wp_die(json_encode($response));
    }

    public function wpfucfFieldsToImportPopup() {
        $response = array('code' => 0);
        $postData = filter_input(INPUT_POST, 'wpfucfAjaxData');
        if ($postData) {
            $data = json_decode($postData, true);
            $plugin = isset($data['plugin']) && ($value = trim($data['plugin'])) ? $value : false;
            $className = WpforoUcfHelper::getFieldClassName($plugin);
            if (class_exists($className)) {
                $object = new $className($this->dbManager, $this->options, $this->helper);
                $response['code'] = 1;
                $response['data'] = $object->getAvailableFields();
            } else {
                $response['error'] = __('Class not exists', 'wpforo-ucf');
            }
        }
        wp_die(json_encode($response));
    }

}
