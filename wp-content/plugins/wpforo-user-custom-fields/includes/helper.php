<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfHelper implements WpforoUcfConstants {

    private $dbManager;
    private $options;

    public function __construct($dbManager, $options) {
        $this->dbManager = $dbManager;
        $this->options = $options;
//        add_filter('wpforo_get_fields', array(&$this->options, 'getFields'));
        add_filter('wpforo_member_after_init_fields', array(&$this->options, 'initFields'));
        add_filter('wpforo_get_register_fields', function($fields) {
            return get_option(self::UCF_OPTION_REGISTER_FIELDS, $fields);
        });
        add_filter('wpforo_get_account_fields', function($fields) {
            return get_option(self::UCF_OPTION_ACCOUNT_FIELDS, $fields);
        });
        add_filter('wpforo_get_profile_fields', function($fields) {
            return get_option(self::UCF_OPTION_PROFILE_FIELDS, $fields);
        });
        add_filter('wpforo_get_search_fields', function($fields) {
            return get_option(self::UCF_OPTION_SEARCH_FIELDS, $fields);
        });

        //add_filter('wpforo_create_profile', array(&$this, 'validateFieldsRegister'));
        //add_filter('wpforo_edit_profile', array(&$this, 'validateFieldsAccount'));
        //add_filter('wpforo_change_password_validate', array(&$this, 'validatePassword'), 10, 5);
        //add_action('wpforo_create_profile_after', array(&$this, 'createProfileAfter'));
        //add_action('wpforo_edit_profile_after', array(&$this, 'editProfileAfter'));

        add_filter('wpforo_login_min_length', array(&$this, 'loginMinLength'));
        add_filter('wpforo_login_max_length', array(&$this, 'loginMaxLength'));
        add_filter('wpforo_pass_min_length', array(&$this, 'passwordMinLength'));
        add_filter('wpforo_pass_max_length', array(&$this, 'passwordMaxLength'));
    }

    public function getEmptyTemplate($type = 'row') {
        ob_start();
        include_once WPFUCF_DIR_PATH . "/includes/fields/html/templates/empty-$type.php";
        $html = ob_get_clean();
        return $html;
    }

    public static function getFieldClassName($type) {
        return $type && is_string($type) ? self::UCF_CLASS_PREFIX . ucfirst($type) : '';
    }

    public static function getFieldName() {
        return 'field_' . substr(md5(rand() . uniqid()), 0, 7);
    }

    public static function getFieldObject($fieldData) {
        $type = isset($fieldData['type']) ? $fieldData['type'] : '';
        $className = WpforoUcfHelper::getFieldClassName($type);
        return class_exists($className) ? new $className($fieldData) : new WpforoUcfCustom($fieldData);
    }

    public static function arraySearchByValue($array, $searchValue, $keys = array()) {
        foreach ($array as $key => $value) {
            if (is_array($value) && !isset($value['fieldKey'])) {
                $sub = self::arraySearchByValue($value, $searchValue, array_merge($keys, array($key)));
                if (count($sub)) {
                    return $sub;
                }
            } elseif ($value['fieldKey'] === $searchValue) {
                return array_merge($keys, array($key));
            }
        }
        return array();
    }

    /**
     * currently unused...
     * @param type $name the field name
     * @return boolean true if field name is unique false otherwise
     */
    public function isValidFieldName($name) {
        $isValid = true;
        $fields = $this->options->getFields();
        if ($fields && is_array($fields)) {
            if (array_search($name, $fields)) {
                $isValid = false;
            }
        }
        return $isValid;
    }

    public static function isEmpty($fieldObj, $value) {
        $isEmpty = false;
        $isset = isset($fieldObj->isRequired);
        if ($isset && $fieldObj->isRequired && !$value) {
            WPF()->notice->clear();
            if (isset($fieldObj->label) && $fieldObj->label) {
                WPF()->notice->add('%s is required field', 'error', $fieldObj->label);
            } else {
                WPF()->notice->add('Please fill out required fields', 'error');
            }
            $isEmpty = true;
        }
        return $isEmpty;
    }

    public static function isValidMinLength($fieldObj, $value) {
        $isValid = true;
        if (isset($fieldObj->minLength) && $fieldObj->minLength && $value) {
            $value = self::decodeData($value);
            $vMinLength = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
            if ($fieldObj->minLength > $vMinLength) {
                WPF()->notice->clear();
                if (isset($fieldObj->label) && $fieldObj->label) {
                    WPF()->notice->add('%s length must be at least %d characters!', 'error', array($fieldObj->label, $fieldObj->minLength));
                } else {
                    WPF()->notice->add('Length must be at least %d characters!', 'error', $fieldObj->minLength);
                }
                $isValid = false;
            }
        }
        return $isValid;
    }

    public static function isValidMaxLength($fieldObj, $value) {
        $isValid = true;
        if (isset($fieldObj->maxLength) && $fieldObj->maxLength && $value) {
            $value = self::decodeData($value);
            $vMaxLength = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
            if ($fieldObj->maxLength < $vMaxLength) {
                WPF()->notice->clear();
                if (isset($fieldObj->label) && $fieldObj->label) {
                    WPF()->notice->add('%s length can not be greater than %d characters!', 'error', array($fieldObj->label, $fieldObj->maxLength));
                } else {
                    WPF()->notice->add('Length can not be greater than %d characters!', 'error', $fieldObj->maxLength);
                }
                $isValid = false;
            }
        }
        return $isValid;
    }

    public static function isValidMinValue($fieldObj, $value) {
        $isValid = true;
        if (isset($fieldObj->minLength) && $fieldObj->minLength && $value) {
            $vMinValue = intval($value);
            if ($fieldObj->minLength > $vMinValue) {
                WPF()->notice->clear();
                if (isset($fieldObj->label) && $fieldObj->label) {
                    WPF()->notice->add('%s min value must be at least %d !', 'error', array($fieldObj->label, $fieldObj->minLength));
                } else {
                    WPF()->notice->add('Min value must be at least %d !', 'error', $fieldObj->minLength);
                }
                $isValid = false;
            }
        }
        return $isValid;
    }

    public static function isValidMaxValue($fieldObj, $value) {
        $isValid = true;
        if (isset($fieldObj->maxLength) && $fieldObj->maxLength && $value) {
            $vMaxLength = intval($value);
            if ($fieldObj->maxLength < $vMaxLength) {
                WPF()->notice->clear();
                if (isset($fieldObj->label) && $fieldObj->label) {
                    WPF()->notice->add('%s max value can not be greater then %d !', 'error', array($fieldObj->label, $fieldObj->minLength));
                } else {
                    WPF()->notice->add('Max value can not be greater then %d !', 'error', $fieldObj->maxLength);
                }
                $isValid = false;
            }
        }
        return $isValid;
    }

    /**
     * @deprecated since 1.1.2
     * @deprecated No longer used by addon and wpForo core.
     * Custom field data validating is moved to WPF()->form->validate()
     */
    public function validateFieldsRegister($args) {
        $data = isset($_POST['data']) && $_POST['data'] && is_array($_POST['data']) ? $_POST['data'] : array();
        $fileData = isset($_FILES['data']['name']) && $_FILES['data']['name'] && is_array($_FILES['data']['name']) ? $_FILES['data']['name'] : array();
        $merged = array_merge($args, $data);
        $merged = array_merge($merged, $fileData);
        $fields = $this->options->getFields();
        $registerFields = $this->options->getRegisterFields();
        if ($merged && is_array($merged)) {
            foreach ($merged as $k => $v) {
                if ($k == 'user_pass1' || $k == 'user_pass2') {
                    $k = 'user_pass';
                }
                $keys = WpforoUcfHelper::arraySearchByValue($registerFields, $k);

                if ($keys && isset($fields[$k])) {
                    $field = $fields[$k];
                    if ($field && is_array($field)) {
                        $fieldObject = WpforoUcfHelper::getFieldObject($field);
                        if (!$fieldObject->validate($v)) {
                            $args['error'] = true;
                        }
                    }
                }
            }
        }
        if (!session_id()) {
            session_start();
        }
        $_SESSION['wpfucf']['template'] = 'register';
        return $args;
    }

    /**
     * @deprecated since 1.1.2
     * @deprecated No longer used by addon and wpForo core.
     * Custom field data validating is moved to WPF()->form->validate()
     */
    public function validateFieldsAccount($args) {

        if (isset($_SESSION['wpfucf']['template']) && $_SESSION['wpfucf']['template'] == 'register') {
            $args['template'] = $_SESSION['wpfucf']['template'];
            return $args;
        }
        $data = isset($_POST['data']) && $_POST['data'] && is_array($_POST['data']) ? $_POST['data'] : array();
        $merged = array_merge($args, $data);
        $fields = $this->options->getFields();
        $accountFields = $this->options->getAccountFields();
        $ignoreFields = array('user_pass1', 'user_pass2', 'old_pass');

        if ($merged && is_array($merged)) {
            foreach ($merged as $k => $v) {
                if (in_array($k, $ignoreFields)) {
                    continue;
                }
                $keys = WpforoUcfHelper::arraySearchByValue($accountFields, $k);
                if ($keys && isset($fields[$k])) {
                    $field = $fields[$k];
                    if ($field && is_array($field)) {
                        $fieldObject = WpforoUcfHelper::getFieldObject($field);
                        if ($field['type'] == 'password') {
                            $fieldObject->isRequired = false;
                        }
                        if (!$fieldObject->validate($v)) {
                            $args['error'] = true;
                            break;
                        }
                    }
                }
            }
        }
        if (isset($_SESSION['wpfucf']['template'])) {
            $args['template'] = $_SESSION['wpfucf']['template'];
        }
        return $args;
    }

    /**
     * @deprecated since 1.1.2
     * @deprecated No longer used by addon and wpForo core.
     * Password validation is moved to WPF()->form->validate_password()
     */
    public function validatePassword($isValid, $oldPassw, $newPassw, $user) {
        if ($newPassw) {
            $fields = $this->options->getFields();
            $accountFields = $this->options->getAccountFields();
            $k = 'user_pass';
            $keys = WpforoUcfHelper::arraySearchByValue($accountFields, $k);
            if ($keys && isset($fields[$k])) {
                $field = $fields[$k];
                if ($field && is_array($field)) {
                    $fieldObject = WpforoUcfHelper::getFieldObject($field);
                    if (!$fieldObject->validate($newPassw)) {
                        $isValid = false;
                    }
                }
            }
        }
        return $isValid;
    }

    /**
     * @deprecated since 1.1.2
     * @deprecated No longer used by addon and wpForo core.
     * Custom field data inserting is moved to WPF()->member->update()
     */
    public function createProfileAfter($args) {
        if ($args && isset($args['userid']) && intval($args['userid'])) {
            add_filter('wpforo_allow_edit_profile_groupid', array(&$this, 'allowGroupEditing'));
            $member = wp_parse_args($args, wpforo_member($args));
            WPF()->member->reset($args['userid']);
            WPF()->notice->clear();
        }
    }

    /**
     * @deprecated since 1.1.2
     * @deprecated No longer used by addon and wpForo core.
     * Custom field data updating is moved to WPF()->member->update()
     */
    public function editProfileAfter($args) {
        $data = isset($_POST['data']) && $_POST['data'] && is_array($_POST['data']) ? $_POST['data'] : array();
        $fileData = isset($_FILES['data']['name']) && $_FILES['data']['name'] && is_array($_FILES['data']['name']) ? array_filter($_FILES['data']['name']) : array();
        $merged = array();
        $merged = array_merge($merged, $data);
        $userId = intval($args['userid']);
        if ($fileData) {
            $files = array();
            $filesArray = array();
            $wpUploadsDir = wp_upload_dir();
            $wpBaseDir = $wpUploadsDir['basedir'];
            foreach ($fileData as $fieldName => $fileName) {
                $fieldNameFolder = substr($fieldName, 6);
                $ucfUploadsDir = $wpBaseDir . self::UCF_UPLOAD_DIR . $userId . '/' . $fieldNameFolder . '/';
                $filePath = $ucfUploadsDir . $fileName;
                $filesArray[$fieldName] = self::UCF_UPLOAD_DIR . $userId . '/' . $fieldNameFolder . '/' . $fileName;
                $files[$fieldName] = $filePath;
                wp_mkdir_p($ucfUploadsDir);
            }
            $merged = array_merge($data, $filesArray);
        }

        if ($merged) {
            $this->dbManager->addFields($userId, $merged);
            if ($files) {
                foreach ($files as $fieldName => $filePath) {
                    if ($_FILES['data']['tmp_name'][$fieldName] && !move_uploaded_file($_FILES['data']['tmp_name'][$fieldName], $filePath)) {
                        WPF()->notice->add('Sorry, there was an error uploading your file.', 'error');
                    }
                }
            }
        }
        if (wpforo_member($args)) {
            WPF()->member->upload_avatar($userId);
        }
        if (isset($_SESSION['wpfucf']['template'])) {
            unset($_SESSION['wpfucf']['template']);
        }
    }

    public function removeNonExistFields($data) {
        $wpfucfFields = $this->options->getFields();
        $updateFields = array();
        if ($wpfucfFields && is_array($wpfucfFields) && $data && is_array($data)) {
            foreach ($data as $fKey => $fVal) {
                if (isset($wpfucfFields[$fKey])) {
                    $updateFields[$fKey] = $fVal;
                }
            }
        }
        return $updateFields;
    }

    public function loginMinLength($length) {
        $fields = $this->options->getFields();
        $k = 'user_login';
        if ($fields && is_array($fields) && isset($fields[$k])) {
            $field = $fields[$k];
            if (isset($field['minLnegth']) && ($l = absint($field['minLnegth']))) {
                $length = $l;
            }
        }
        return $length;
    }

    public function loginMaxLength($length) {
        $fields = $this->options->getFields();
        $k = 'user_login';
        if ($fields && is_array($fields) && isset($fields[$k])) {
            $field = $fields[$k];
            if (isset($field['maxLnegth']) && ($l = absint($field['maxLnegth']))) {
                $length = $l;
            }
        }
        return $length;
    }

    public function passwordMinLength($length) {
        $fields = $this->options->getFields();
        $k = 'user_pass';
        if ($fields && is_array($fields) && isset($fields[$k])) {
            $field = $fields[$k];
            if (isset($field['minLnegth']) && ($l = absint($field['minLnegth']))) {
                $length = $l;
            }
        }
        return $length;
    }

    public function passwordMaxLength($length) {
        $fields = $this->options->getFields();
        $k = 'user_pass';
        if ($fields && is_array($fields) && isset($fields[$k])) {
            $field = $fields[$k];
            if (isset($field['maxLnegth']) && ($l = absint($field['maxLnegth']))) {
                $length = $l;
            }
        }
        return $length;
    }

    public function escAttrRecursive($data) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    return $this->escAttrRecursive($v);
                } else {
                    return esc_attr(trim($v));
                }
            }
        } else {
            return esc_attr(trim($data));
        }
    }

    public static function unescapeData($data) {
        $data = is_array($data) ? array_map(array('WpforoUcfHelper', 'unescapeData'), $data) : stripslashes($data);
        return $data;
    }

    public static function encodeData($data) {
        $data = is_array($data) ? array_map(array('WpforoUcfHelper', 'encodeData'), $data) : htmlspecialchars($data, ENT_QUOTES);
        return $data;
    }

    public static function decodeData($data) {
        $data = is_array($data) ? array_map(array('WpforoUcfHelper', 'decodeData'), $data) : htmlspecialchars_decode($data, ENT_QUOTES);
        return $data;
    }

    public static function trimData($data) {
        $data = is_array($data) ? array_map(array('WpforoUcfHelper', 'trimData'), $data) : trim($data);
        return $data;
    }

    public static function jsonDecodeData($data) {
        $data = is_array($data) ? array_map(array('WpforoUcfHelper', 'jsonDecodeData'), $data) : json_decode($data, JSON_UNESCAPED_UNICODE);
        return $data;
    }

    public static function allowedGroupIds($fieldData) {
        if (isset($fieldData['allowedGroupIds']) && $fieldData['allowedGroupIds']) {
            if (is_array($fieldData['allowedGroupIds'])) {
                return WpforoUcfHelper::trimData($fieldData['allowedGroupIds']);
            } else {
                return explode(',', trim($fieldData['allowedGroupIds']));
            }
        } else {
            return array();
        }
    }

    public function allowGroupEditing($isAllow) {
        return true;
    }

    public static function createNestedDD($values) {
        $newValues = array();
        if ($values && is_array($values)) {
            $openOptgroup = "#\[optgroup=([^\]]+)\]#isu";
            $closeOptgroup = "[/optgroup]";
            $lastKey = '';
            foreach ($values as $value) {
                $matches = array();
                $isOptgroupExists = preg_match($openOptgroup, $value, $matches);
                if ($isOptgroupExists) {
                    if (!isset($newValues[$matches[1]])) {
                        $lastKey = $matches[1];
                        $newValues[$matches[1]] = array();
                        continue;
                    }
                } else {
                    if (strpos($closeOptgroup, $value) === false && $lastKey) {
                        $newValues[$lastKey][] = $value;
                    } else {
                        if (strpos($closeOptgroup, $value) !== false) {
                            $lastKey = '';
                        } else {
                            $newValues[] = $value;
                        }
                    }
                }
            }
        }
        return array_filter($newValues);
    }

    public static function createOptgroupFromDD($values) {
        $optgroupValues = '';
        if ($values && is_array($values)) {
            foreach ($values as $k => $v) {
                if (is_array($v)) {
                    $optgroupValues .= "[optgroup=$k]" . PHP_EOL;
                    foreach ($v as $_k => $_v) {
                        $optgroupValues .= $_v . PHP_EOL;
                    }
                    $optgroupValues .= "[/optgroup]" . PHP_EOL;
                } else {
                    $optgroupValues .= $v . PHP_EOL;
                }
            }
        }
        return $optgroupValues;
    }

}
