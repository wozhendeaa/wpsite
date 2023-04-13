<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfPassword extends WpforoUcfAbstractField {

    public $label;
    public $title;
    public $placeholder;
    public $description;
    public $minLength;
    public $maxLength;
    public $faIcon;
    public $isRequired;
//    public $isEditable;
//    public $canEdit;

    public function __construct($fieldData) {
        parent::__construct($fieldData);
        $this->label = $this->label($fieldData);
        $this->title = $this->title($fieldData);
        $this->placeholder = $this->placeholder($fieldData);
        $this->description = $this->description($fieldData);
        $this->minLength = $this->minLength($fieldData);
        $this->maxLength = $this->maxLength($fieldData);
        $this->faIcon = $this->faIcon($fieldData);
        $this->isRequired = $this->isRequired($fieldData);
//        $this->isEditable = $this->isEditable($fieldData);
//        $this->canEdit = $this->canEdit($fieldData);

        add_action('wpfucf_field_add_edit_label', array(&$this, 'fieldAddEditLabel'));
        add_action('wpfucf_field_add_edit_placeholder', array(&$this, 'fieldAddEditPlaceholder'));
        add_action('wpfucf_field_add_edit_title', array(&$this, 'fieldAddEditTitle'));
        add_action('wpfucf_field_add_edit_description', array(&$this, 'fieldAddEditDescription'));
        add_action('wpfucf_field_add_edit_min_max_length', array(&$this, 'fieldAddEditMinMaxLength'));
        add_action('wpfucf_field_add_edit_faicon', array(&$this, 'fieldAddEditFaIcon'));
        add_action('wpfucf_field_add_edit_isrequired', array(&$this, 'fieldAddEditIsRequired'));
//        add_action('wpfucf_field_add_edit_iseditable', array(&$this, 'fieldAddEditIsEditable'));
    }

    public function validate($value) {
        return !WpforoUcfHelper::isEmpty($this, $value) &&
                WpforoUcfHelper::isValidMinLength($this, $value) &&
                WpforoUcfHelper::isValidMaxLength($this, $value);
    }

    public function fieldAddEditLabel() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/label.php';
    }

    public function fieldAddEditPlaceHolder() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/placeholder.php';
    }

    public function fieldAddEditTitle() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/title.php';
    }

    public function fieldAddEditDescription() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/description.php';
    }

    public function fieldAddEditMinMaxLength() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/min-max-length.php';
    }

    public function fieldAddEditFaIcon() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/fa-icon.php';
    }

    public function fieldAddEditIsRequired() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-required.php';
    }

//    public function fieldAddEditIsEditable() {
//        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-editable.php';
//    }
//
//    public function fieldAddEditCanEdit() {
//        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/can-edit.php';
//    }

}
