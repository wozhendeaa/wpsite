<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfRadio extends WpforoUcfAbstractField {

    public $label;
    public $title;
    public $description;
    public $values;
    public $isRequired;
    public $isEditable;
    public $isLabelFirst;
    public $isWrapItem;
    public $isDisplayDefaultValues;
    public $canEdit;

    public function __construct($fieldData) {
        parent::__construct($fieldData);
        $this->label = $this->label($fieldData);
        $this->title = $this->title($fieldData);
        $this->description = $this->description($fieldData);
        $this->values = $this->values($fieldData);
        $this->isRequired = $this->isRequired($fieldData);
        $this->isEditable = $this->isEditable($fieldData);
        $this->isLabelFirst = $this->isLabelFirst($fieldData);
        $this->isWrapItem = $this->isWrapItem($fieldData);
        $this->isDisplayDefaultValues = $this->isDisplayDefaultValues($fieldData);
        $this->canEdit = $this->canEdit($fieldData);

        add_action('wpfucf_field_add_edit_label', array(&$this, 'fieldAddEditLabel'));
        add_action('wpfucf_field_add_edit_title', array(&$this, 'fieldAddEditTitle'));
        add_action('wpfucf_field_add_edit_description', array(&$this, 'fieldAddEditDescription'));
        add_action('wpfucf_field_add_edit_values', array(&$this, 'fieldAddEditValues'));
        add_action('wpfucf_field_add_edit_isrequired', array(&$this, 'fieldAddEditIsRequired'));
        add_action('wpfucf_field_add_edit_iseditable', array(&$this, 'fieldAddEditIsEditable'));
        add_action('wpfucf_field_add_edit_islabelfirst', array(&$this, 'fieldAddEditIsLabelFirst'));
        add_action('wpfucf_field_add_edit_iswrapitem', array(&$this, 'fieldAddEditIsWrapItem'));
        add_action('wpfucf_field_add_edit_isdisplaydefaultvalues', array(&$this, 'fieldAddEditIsDisplayDefaultValues'));
        add_action('wpfucf_field_add_edit_canedit', array(&$this, 'fieldAddEditCanEdit'));
    }

    public function validate($value) {
        return !WpforoUcfHelper::isEmpty($this, $value);
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

    public function fieldAddEditValues() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/values.php';
    }

    public function fieldAddEditIsRequired() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-required.php';
    }

    public function fieldAddEditIsEditable() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-editable.php';
    }

    public function fieldAddEditIsLabelFirst() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-label-first.php';
    }

    public function fieldAddEditIsWrapItem() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-wrap-item.php';
    }

    public function fieldAddEditIsDisplayDefaultValues() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-display-default-values.php';
    }

    public function fieldAddEditCanEdit() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/can-edit.php';
    }

}
