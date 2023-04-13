<?php

if (!defined('ABSPATH')) {
    exit();
}

include_once 'classes/checkbox.php';
include_once 'classes/date.php';
include_once 'classes/email.php';
include_once 'classes/number.php';
include_once 'classes/password.php';
include_once 'classes/radio.php';
include_once 'classes/select.php';
include_once 'classes/text.php';
include_once 'classes/textarea.php';
include_once 'classes/url.php';
include_once 'classes/html.php';
//include_once 'classes/avatar.php';
include_once 'classes/tel.php';
include_once 'classes/usergroup.php';
include_once 'classes/file.php';
include_once 'classes/search.php';
include_once 'classes/color.php';
include_once 'classes/range.php';
include_once 'classes/numberInterval.php';
include_once 'classes/secondary_groups.php';
include_once 'classes/custom.php';

abstract class WpforoUcfAbstractField {

    public $type; // global
    public $isDefault; // global
    public $isRemovable; // global
    public $isSearchable; // global
    public $name; //global
    public $info; // global
    public $cantBeInactive; // register,profile,account,search
    public $canView; //global
    public $fieldKey; //global
    public $isTrashed; // global

    public function __construct($fieldData) {

        $this->type = $this->type($fieldData);
        $this->isDefault = $this->isDefault($fieldData);
        $this->isRemovable = $this->isRemovable($fieldData);
        $this->isSearchable = $this->isSearchable($fieldData);
        $this->name = $this->name($fieldData);
        $this->info = $this->info($fieldData);
        $this->cantBeInactive = $this->cantBeInactive($fieldData);
        $this->canView = $this->canView($fieldData);
        $this->fieldKey = $this->fieldKey($fieldData);
        $this->isTrashed = $this->isTrashed($fieldData);

        add_action('wpfucf_field_add_edit_fieldname', array(&$this, 'fieldAddEditFieldName'));
        add_action('wpfucf_field_add_edit_info', array(&$this, 'fieldAddEditInfo'));
        if ($this->type !== 'password') {
            add_action('wpfucf_field_add_edit_canview', array(&$this, 'fieldAddEditCanView'));
        }
    }

    /**
     * @return String Current Field HTML
     */
    public function fieldsData($fKey) {
        ob_start();
        $currentTab = ($tab = filter_input(INPUT_GET, 'tab')) ? $tab : WpforoUcfConstants::TAB_FIELDS;
        $requiredHtml = '<span class="wpfucf-required-icon" style="color:#f00;" title="' . __('Required', 'wpforo-ucf') . '">*</span>';
        $defaultClass = $this->isDefault ? 'wpfucf-field-default' : '';
        $removableClass = $this->isRemovable ? 'wpfucf-field-removable wpfucf-sortable-child-item' : '';

        $inactiveClass = in_array($currentTab, $this->cantBeInactive) ? 'wpfucf-cant-be-inactive' : '';


        $duplicateClass = in_array($this->name, array('user_login', 'groupid', 'avatar', 'user_pass')) || !class_exists(WpforoUcfHelper::getFieldClassName($this->type)) ? 'wpfucf-no-duplicate' : 'wpfucf-not-clicked';
        include 'html/field-data/data.php';
        $html = ob_get_clean();
        return $html;
    }

    /**
     * @param $i row number
     * @param $j column number in row
     * @param $k field number in col
     * @return String Current Field HTML
     */
    public function formFieldData($i, $j, $k) {
        ob_start();
        $currentTab = ($tab = filter_input(INPUT_GET, 'tab')) ? $tab : WpforoUcfConstants::TAB_FIELDS;
        if ($currentTab === 'member_tabs') {
            $member_tab = ($t = filter_input(INPUT_GET, 'key')) ? $t : '';
            if (in_array($member_tab, array('profile', 'account'))) {
                $currentTab = $member_tab;
            }
        }
        $includeRequired = array(WpforoUcfConstants::TAB_REGISTER, WpforoUcfConstants::TAB_ACCOUNT);
        $requiredHtml = '<span class="wpfucf-required-icon" style="color:#f00;" title="' . __('Required', 'wpforo-ucf') . '">*</span>';
        $defaultClass = $this->isDefault ? 'wpfucf-field-default' : '';
        $removableClass = $this->isRemovable ? 'wpfucf-field-removable' : '';
        $inactiveClass = in_array($currentTab, $this->cantBeInactive) ? 'wpfucf-cant-be-inactive' : '';
        include 'html/field-data/structure.php';
        $html = ob_get_clean();
        return $html;
    }

    public function addFieldPopupHtml() {
        $title = __(sprintf('Add Field: <i>%s</i>', ucfirst($this->type)), 'wpforo-ucf');
        $submitValue = __('Add', 'wpforo-ucf');
        $cancelValue = __('Cancel', 'wpforo-ucf');
        $submitClass = 'wpfucf-field-add-submit';
        $action = 'add';
        ob_start();
        include_once $this->addEditPopupPath();
        $html = ob_get_clean();
        return $html;
    }

    public function editFieldPopupHtml() {
        $title = __('Edit Field', 'wpforo-ucf');
        $submitValue = __('Set', 'wpforo-ucf');
        $cancelValue = __('Cancel', 'wpforo-ucf');
        $submitClass = 'wpfucf-field-edit-submit';
        $action = 'edit';
        ob_start();
        include_once $this->addEditPopupPath();
        $html = ob_get_clean();
        return $html;
    }

    public abstract function validate($value);

    public function setName($name) {
        $this->name = $name;
    }

    protected function type($fieldData) {
        return isset($fieldData['type']) && ($value = trim($fieldData['type'])) ? $value : 'custom';
    }

    protected function isDefault($fieldData) {
        return isset($fieldData['isDefault']) && ($value = absint($fieldData['isDefault'])) ? $value : 0;
    }

    protected function isRemovable($fieldData) {
        return isset($fieldData['isRemovable']) && (($value = absint($fieldData['isRemovable'])) >= 0) ? $value : 1;
    }

    public function isSearchable($fieldData) {
//        print_r($fieldData);die;
        $notSearchableTypes = array('html', 'color', 'file');
        if (in_array($fieldData['type'], $notSearchableTypes)) {
            return false;
        }
        return isset($fieldData['isSearchable']) && (($value = absint($fieldData['isSearchable'])) >= 0) ? $value : 1;
    }

    protected function name($fieldData) {
        return isset($fieldData['name']) && ($value = trim($fieldData['name'])) ? sanitize_text_field($value) : '';
    }

    protected function info($fieldData) {
        return isset($fieldData['info']) && ($value = trim($fieldData['info'])) ? sanitize_text_field($value) : '';
    }

    protected function cantBeInactive($fieldData) {
        if (empty($fieldData['cantBeInactive'])) {
            $cantBeInactive = array();
        } else {
            if (is_array($fieldData['cantBeInactive'])) {
                $tabs = WpforoUcfHelper::trimData($fieldData['cantBeInactive']);
                $cantBeInactive = array_map('strtolower', $tabs);
            } else {
                $cantBeInactive = explode(',', trim($fieldData['cantBeInactive']));
            }
        }
        return array_filter($cantBeInactive);
    }

    protected function label($fieldData) {
        return isset($fieldData['label']) && ($value = trim($fieldData['label'])) ? sanitize_text_field($value) : '';
    }

    protected function title($fieldData) {
        return isset($fieldData['title']) && ($value = trim($fieldData['title'])) ? sanitize_text_field($value) : '';
    }

    protected function placeholder($fieldData) {
        return isset($fieldData['placeholder']) && ($value = trim($fieldData['placeholder'])) ? sanitize_text_field($value) : '';
    }

    protected function description($fieldData) {
        return isset($fieldData['description']) && ($value = trim($fieldData['description'])) ? $value : '';
    }

    protected function fileSize($fieldData) {
        return isset($fieldData['fileSize']) && ($value = absint($fieldData['fileSize'])) ? $value : '';
    }

    protected function fileExtensions($fieldData) {
        if (isset($fieldData['fileExtensions']) && $fieldData['fileExtensions']) {
            if (is_array($fieldData['fileExtensions'])) {
                $extensions = WpforoUcfHelper::trimData($fieldData['fileExtensions']);
                $fileExtensions = array_map('strtolower', $extensions);
            } else {
                $fileExtensions = explode(',', trim($fieldData['fileExtensions']));
            }
        } else {
            $fileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        }
        return array_filter($fileExtensions);
    }

    protected function isRequired($fieldData) {
        return isset($fieldData['isRequired']) && ($value = absint($fieldData['isRequired'])) ? $value : 0;
    }

    protected function isEditable($fieldData) {
        return isset($fieldData['isEditable']) && ($value = absint($fieldData['isEditable'])) >= 0 ? $value : 1;
    }

    protected function isMultiChoice($fieldData) {
        return isset($fieldData['isMultiChoice']) && ($value = absint($fieldData['isMultiChoice'])) ? $value : 0;
    }

    protected function isDisplayDefaultValues($fieldData) {
        return isset($fieldData['isDisplayDefaultValues']) && ($value = absint($fieldData['isDisplayDefaultValues'])) ? $value : 0;
    }

    protected function faIcon($fieldData) {
        $icon = isset($fieldData['faIcon']) && ($value = trim($fieldData['faIcon'])) ? $value : '';
        return $icon && strpos($icon, ' ') === false ? 'fas ' . $icon : $icon;
    }

    protected function minLength($fieldData) {
        return isset($fieldData['minLength']) && ($value = absint($fieldData['minLength'])) ? $value : '';
    }

    protected function maxLength($fieldData) {
        return isset($fieldData['maxLength']) && ($value = absint($fieldData['maxLength'])) && ($value > $this->minLength) ? $value : '';
    }

    protected function values($fieldData) {
        $values = array();
        if (isset($fieldData['values']) && $fieldData['values']) {
            if (is_array($fieldData['values'])) {
                $values = WpforoUcfHelper::trimData($fieldData['values']);
            } else {
                $values = preg_split("#(" . PHP_EOL . ")#isu", WpforoUcfHelper::trimData($fieldData['values']));
                if ($fieldData['type'] == 'select') {
                    $values = WpforoUcfHelper::createNestedDD($values);
                }
            }
        }
        return array_filter($values);
    }

    protected function html($fieldData) {
        return isset($fieldData['html']) && ($value = trim($fieldData['html'])) ? $value : '';
    }

    protected function isLabelFirst($fieldData) {
        return isset($fieldData['isLabelFirst']) && ($value = absint($fieldData['isLabelFirst'])) ? $value : 0;
    }

    protected function isWrapItem($fieldData) {
        return isset($fieldData['isWrapItem']) && ($value = absint($fieldData['isWrapItem'])) ? $value : 1;
    }

    protected function canView($fieldData) {
        $canView = array();
        if (isset($fieldData['canView']) && $fieldData['canView']) {
            if (is_array($fieldData['canView'])) {
                $canView = WpforoUcfHelper::trimData($fieldData['canView']);
            } else {
                $canView = explode(',', trim($fieldData['canView']));
            }
        } else {
            $groups = WPF()->usergroup->usergroup_list_data();
            if ($groups && is_array($groups)) {
                ksort($groups);
                $canView = array_keys($groups);
            } else {
                $canView = array(1);
            }
        }
        return array_filter($canView);
    }

    protected function canEdit($fieldData) {
        if (isset($fieldData['canEdit']) && $fieldData['canEdit']) {
            if (is_array($fieldData['canEdit'])) {
                $canEdit = WpforoUcfHelper::trimData($fieldData['canEdit']);
            } else {
                $canEdit = explode(',', trim($fieldData['canEdit']));
            }
        } else {
            $canEdit = array(1);
        }
        return array_filter($canEdit);
    }

    public function allowedGroupIds($fieldData) {
        $allowedGroupIds = array();
        if (isset($fieldData['allowedGroupIds']) && $fieldData['allowedGroupIds']) {
            if (is_array($fieldData['allowedGroupIds'])) {
                $allowedGroupIds = WpforoUcfHelper::trimData($fieldData['allowedGroupIds']);
            } else {
                $allowedGroupIds = explode(',', trim($fieldData['allowedGroupIds']));
            }
        }
        return array_filter($allowedGroupIds);
    }

    public function fieldKey($fieldData) {
        return isset($fieldData['fieldKey']) && ($value = trim($fieldData['fieldKey'])) ? $value : $this->name;
    }

    protected function isTrashed($fieldData) {
        return isset($fieldData['isTrashed']) && ($value = absint($fieldData['isTrashed'])) >= 0 ? $value : 0;
    }

    public function fieldAddEditFieldName() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/field-name.php';
    }

    public function fieldAddEditInfo() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/info.php';
    }

    public function fieldAddEditCanView() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/can-view.php';
    }

    protected function addEditPopupPath() {
        return WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/add-edit.php';
    }

}
