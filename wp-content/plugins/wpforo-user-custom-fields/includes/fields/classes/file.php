<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfFile extends WpforoUcfAbstractField {

    public $label;
    public $title;
    public $description;
    public $fileSize;
    public $fileExtensions;
    public $isRequired;
    public $isEditable;
    public $canEdit;

    public function __construct($fieldData) {
        parent::__construct($fieldData);
        $this->label = $this->label($fieldData);
        $this->title = $this->title($fieldData);
        $this->description = $this->description($fieldData);
        $this->fileSize = $this->fileSize($fieldData);
        $this->fileExtensions = $this->fileExtensions($fieldData);
        $this->isRequired = $this->isRequired($fieldData);
        $this->isEditable = $this->isEditable($fieldData);
        $this->canEdit = $this->canEdit($fieldData);

        add_action('wpfucf_field_add_edit_label', array(&$this, 'fieldAddEditLabel'));
        add_action('wpfucf_field_add_edit_title', array(&$this, 'fieldAddEditTitle'));
        add_action('wpfucf_field_add_edit_description', array(&$this, 'fieldAddEditDescription'));
        add_action('wpfucf_field_add_edit_filesize', array(&$this, 'fieldAddEditFileSize'));
        add_action('wpfucf_field_add_edit_fileextensions', array(&$this, 'fieldAddEditFileExtensions'));
        add_action('wpfucf_field_add_edit_isrequired', array(&$this, 'fieldAddEditIsRequired'));
        add_action('wpfucf_field_add_edit_iseditable', array(&$this, 'fieldAddEditIsEditable'));
        add_action('wpfucf_field_add_edit_canedit', array(&$this, 'fieldAddEditCanEdit'));
    }

    public function validate($value) {
        $isValid = true;
        $isEmpty = WpforoUcfHelper::isEmpty($this, $value);
        if ($isEmpty) {
            $isValid = false;
        } else {
            if ($value) {
                $extension = pathinfo($value, PATHINFO_EXTENSION);
                $lcExtension = (function_exists('mb_strtolower')) ? mb_strtolower($extension) : strtolower($extension);
                if ($lcExtension && in_array(strtolower($lcExtension), $this->fileExtensions)) {
                    if ($_FILES['data']['size'][$this->name] > ($this->fileSize * 1024 * 1024)) {
                        WPF()->notice->add('File is too large!', 'error');
                        $isValid = false;
                    }
                } else {
                    WPF()->notice->add('Allowed file types: %s', 'error', implode(', ', $this->fileExtensions));
                    $isValid = false;
                }
            }
        }
        return $isValid;
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

    public function fieldAddEditFileSize() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/file-size.php';
    }

    public function fieldAddEditFileExtensions() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/file-extensions.php';
    }

    public function fieldAddEditIsRequired() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-required.php';
    }

    public function fieldAddEditIsEditable() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/is-editable.php';
    }

    public function fieldAddEditCanEdit() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/can-edit.php';
    }

}
