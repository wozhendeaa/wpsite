<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<a id='wpfucf_shortcodes' style='display:none;' href='#wpfucf_fields_shortcodes_dialog' data-wpfucf-lity></a>
<div id='wpfucf_fields_shortcodes_dialog' style='overflow:auto;background:#f1f1f1;padding:20px;width:885px;max-width:100%;border-radius:6px' class='lity-hide'>
    <h2 class="wpfucf-title"><?php _e('Fields', 'wpforo-ucf'); ?><div class="wpfucf-action-msg wpfucf-hide"><?php _e('Field added successfully', 'wpforo-ucf'); ?></div></h2>
    <div class="wpfucf-fields-wrapper">
        <?php
        $fields = $this->options->getFields();
        foreach ($fields as $field) {
            if (WPF()->form->can_show_value($field)) {
                $fieldObject = WpforoUcfHelper::getFieldObject($field);
                ?>
                <button type="button" class="button button-secondary wpfucf-field-shortcode wpfucf-field-type" data-key="<?php echo $fieldObject->fieldKey; ?>" title="type: <?php echo $fieldObject->type; ?>"><?php echo isset($fieldObject->label) ? $fieldObject->label : $fieldObject->name; ?></button>
                <?php
            }
        }
        ?>
    </div>
</div>