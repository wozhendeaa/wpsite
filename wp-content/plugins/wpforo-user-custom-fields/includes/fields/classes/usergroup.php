<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfUsergroup extends WpforoUcfAbstractField {

    public $label;
    public $title;
    public $description;
    public $faIcon;
    public $isRequired;
    public $allowedGroupIds;

    public function __construct($fieldData) {
        parent::__construct($fieldData);
        $this->label = $this->label($fieldData);
        $this->title = $this->title($fieldData);
        $this->description = $this->description($fieldData);
        $this->faIcon = $this->faIcon($fieldData);
        $this->isRequired = $this->isRequired($fieldData);
        $this->allowedGroupIds = $this->allowedGroupIds($fieldData);

        add_action('wpfucf_field_add_edit_label', array(&$this, 'fieldAddEditLabel'));
        add_action('wpfucf_field_add_edit_title', array(&$this, 'fieldAddEditTitle'));
        add_action('wpfucf_field_add_edit_description', array(&$this, 'fieldAddEditDescription'));
        add_action('wpfucf_field_add_edit_faicon', array(&$this, 'fieldAddEditFaIcon'));
        add_action('wpfucf_field_add_edit_isrequired', array(&$this, 'fieldAddEditIsRequired'));
        add_action('wpfucf_field_add_edit_allowed_group_ids', array(&$this, 'fieldAddEditAllowedGroupIds'));
    }

    public function validate($value) {
        return !WpforoUcfHelper::isEmpty($this, $value) && in_array($value, $this->allowedGroupIds);
    }

    public function fieldAddEditLabel() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/label.php';
    }

    public function fieldAddEditTitle() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/title.php';
    }

    public function fieldAddEditDescription() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/description.php';
    }

    public function fieldAddEditFaIcon() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/fa-icon.php';
    }

    public function fieldAddEditIsRequired() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-required.php';
    }

    public function fieldAddEditAllowedGroupIds() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/allowed-group-ids.php';
    }

}
