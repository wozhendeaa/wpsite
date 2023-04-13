<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpforoUcfHtml extends WpforoUcfAbstractField {

    public $html;

    public function __construct($fieldData) {
        parent::__construct($fieldData);
        $this->html = $this->html($fieldData);

        add_action('wpfucf_field_add_edit_html', array(&$this, 'fieldAddEditHtml'));
    }

    public function validate($value) {
        $isValid = apply_filters('wpfucf_field_html_validate', true);
        return $isValid;
    }

    public function fieldAddEditHtml() {
        include WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/attributes/html.php';
    }

    protected function addEditPopupPath() {
        return WPFUCF_DIR_PATH . '/includes/fields/html/add-edit/add-edit-for-html.php';
    }

}
