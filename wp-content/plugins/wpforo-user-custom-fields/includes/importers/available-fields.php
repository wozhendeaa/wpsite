<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<form action="" method="post" class="wpfucf-available-fields-form">
    <div class="wpfucf-available-fields">
        <?php
        foreach ($fields as $field) {
            $fieldId = $field['id'];
            $fieldType = ucfirst($field['type']);
            $fieldName = $field['label'];
            $fieldDesc = $field['description'];
            $fieldNewKey = array_key_exists($fieldId, $oldNewRelation) ? $oldNewRelation[$fieldId] : '';
            $fieldExists = $fieldNewKey && array_key_exists($fieldNewKey, $wpfucfFields);            
            ?>
            <div class="wpfucf-field-info-wrap">                
                <div class="wpfucf-field-info">
                    <div class="wpfucf-field-info-label"><?php _e('Type:', 'wpforo-ucf'); ?></div>
                    <div class="wpfucf-field-info-text"><i><strong><?php echo $fieldType; ?></strong></i></div>
                </div>
                <div class="wpfucf-field-info">
                    <div class="wpfucf-field-info-label"><?php _e('Name:', 'wpforo-ucf'); ?></div>
                    <div class="wpfucf-field-info-text"><?php echo $fieldName; ?></div>
                </div>
                <div class="wpfucf-field-info">
                    <div class="wpfucf-field-info-label"><?php _e('Description:', 'wpforo-ucf'); ?></div>
                    <div class="wpfucf-field-info-text"><?php echo $fieldDesc; ?></div>
                </div>
                <?php if ($fieldExists) { ?>
                    <div class="wpfucf-field-info">
                        <!--<div class="wpfucf-field-info-label"><?php // _e('Description:', 'wpforo-ucf');       ?></div>-->
                        <div class="wpfucf-field-info-text" style="color:#FF3300;"><strong><?php _e('Field already exists', 'wpforo-ucf'); ?></strong></div>
                    </div>
                <?php } ?>
                <hr/>
                <div class="wpfucf-field-info">
                    <fieldset>                    
                        <div class="wpf-switch-field">            
                            <input value="<?php echo $fieldId; ?>" name="fieldIds[<?php echo $fieldId; ?>]" id="wpfucf-fieldids-<?php echo $fieldId; ?>-1" type="radio" checked="checked" /><label for="wpfucf-fieldids-<?php echo $fieldId; ?>-1"><?php _e('Import', 'wpforo-ucf'); ?></label>
                            <input value="0" name="fieldIds[<?php echo $fieldId; ?>]" id="wpfucf-fieldids-<?php echo $fieldId; ?>-0" type="radio" /><label for="wpfucf-fieldids-<?php echo $fieldId; ?>-0"><?php _e('Exclude', 'wpforo-ucf'); ?></label>
                        </div>
                    </fieldset>                    
                </div>                
            </div>
            <?php
        }
        ?>
        <div class="wpfucf-clear"></div>
    </div>
    <div class="wpfucf-import-fields-actions">
        <button type="button" class="button button-secondary wpfucf-popup-close"><?php _e('Cancel', 'wpforo-ucf'); ?></button>
        <button type="submit" class="button button-primary wpfucf-import-fields wpfucf-not-clicked"><?php _e('Import', 'wpforo-ucf'); ?></button>
        <span class="wpfucf-loading wpfucf-gone"><i class="fas fa-pulse fa-spinner"></i></span>
    </div>
    <input type="hidden" value="<?php echo $sourcePlugin; ?>" name="plugin" id="wpfucf-source-plugin" />
    <input type="hidden" value="<?php echo $action; ?>" name="action" id="wpfucf-action" />
</form>