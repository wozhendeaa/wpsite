<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<form action="" method="POST" class="wpfucf-add-edit-form">
    <div class="wpfucf-field-add-edit-wrapper">
        <h2 class="wpfucf-title"><?php echo $title; ?></h2>    
        <div class="wpfucf-field-add-edit-cols">        
            <div class="wpfucf-field-col-left">
                <?php do_action('wpfucf_field_add_edit_label'); ?>
                <?php do_action('wpfucf_field_add_edit_placeholder'); ?>
                <?php do_action('wpfucf_field_add_edit_title'); ?>
                <?php do_action('wpfucf_field_add_edit_description'); ?>           
                <?php do_action('wpfucf_field_add_edit_values'); ?>
                <?php do_action('wpfucf_field_add_edit_min_max_length'); ?>
                <?php do_action('wpfucf_field_add_edit_faicon'); ?>
                <?php do_action('wpfucf_field_add_edit_html'); ?>
                <?php do_action('wpfucf_field_add_edit_filesize'); ?>
                <?php do_action('wpfucf_field_add_edit_fileextensions'); ?>
                <?php do_action('wpfucf_field_add_edit_fieldname'); ?>
            </div>
            <div class="wpfucf-field-col-right">
                <?php do_action('wpfucf_field_add_edit_isrequired'); ?>
                <?php do_action('wpfucf_field_add_edit_isdisplaydefaultvalues'); ?>
                <?php do_action('wpfucf_field_add_edit_iseditable'); ?>            
                <?php do_action('wpfucf_field_add_edit_ismultichoice'); ?>
                <?php do_action('wpfucf_field_add_edit_isconfirmpassword'); ?>
                <?php do_action('wpfucf_field_add_edit_islabelfirst'); ?>
                <?php do_action('wpfucf_field_add_edit_iswrapitem'); ?>
                <?php do_action('wpfucf_field_add_edit_info'); ?>
                <?php do_action('wpfucf_field_add_edit_allowed_group_ids'); ?>
                <?php do_action('wpfucf_field_add_edit_canview'); ?>
                <?php do_action('wpfucf_field_add_edit_canedit'); ?>
            </div>
            <div class="wpfucf-clear"></div>
        </div>
        <div class="wpfucf-field-add-edit-actions">            
            <button type="button" class="button button-secondary wpfucf-popup-close"><?php echo $cancelValue; ?></button>
            <button type="submit" class="button button-primary wpfucf-add-edit-submit wpfucf-not-clicked"><?php echo $submitValue; ?></button>
            <span class="wpfucf-loading wpfucf-gone"><i class="fas fa-pulse fa-spinner"></i></span>
        </div>        
        <input type="hidden" name="type" value="<?php echo $this->type; ?>" />        
        <input type="hidden" name="isDefault" value="<?php echo $this->isDefault; ?>" />
        <input type="hidden" name="isRemovable" value="<?php echo $this->isRemovable; ?>" />        
        <input type="hidden" name="action" value="<?php echo $action; ?>" class="wpfucf-add-edit-action"/>
    </div>
</form>