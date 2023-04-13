<?php

if (!defined('ABSPATH')) {
    exit();
}

interface WpforoUcfConstants {

    const UCF_VERSION                   = 'wpfucf_version';
    
    const UCF_PAGE_MAIN                 = 'wpforo-user-custom-fields';
    const UCF_OPTION_CUSTOM_FIELDS      = 'wpfucf_custom_fields';
    const UCF_OPTION_REGISTER_FIELDS    = 'wpfucf_register_fields';
    const UCF_OPTION_ACCOUNT_FIELDS     = 'wpfucf_account_fields';
    const UCF_OPTION_PROFILE_FIELDS     = 'wpfucf_profile_fields';
    const UCF_OPTION_SEARCH_FIELDS      = 'wpfucf_search_fields';
    const UCF_OPTION_TRASHED_FIELDS     = 'wpfucf_trashed_fields';
    const UCF_OPTION_MEMBER_TABS        = 'wpfucf_tabs';
    const UCF_CLASS_PREFIX              = 'WpforoUcf';
    const UCF_UPLOAD_DIR                = '/wpforo/users/fields/';
    const UCF_OPTIONS_DIR               = '/wpforo/fields/';
    const WPFORO_PAGE_MAIN              = 'wpforo-overview';
    const WPFORO_PAGE_SETTINGS          = 'wpforo-settings';
    const WPFORO_PAGE_MEMBERS           = 'wpforo-members';
    
    /* OPTIONS PAGE TABS */
    const TAB_FIELDS                    = 'fields';
    const TAB_REGISTER                  = 'register';
    const TAB_PROFILE                   = 'profile';
    const TAB_ACCOUNT                   = 'account';
    const TAB_SEARCH                    = 'search';
    const TAB_TOOLS                     = 'tools';
    const TAB_MEMBER_TABS               = 'member_tabs';
    
    /* BACKUP FILE */
    const UCF_BACKUP_FILE_NAME          = 'wpfucf-options';
    
    /* FIELDS IMPORTER */
    const UCF_FIELDS_RELATION           = 'wpfucf_old_new_relation_';
    const UCF_USERS_COUNT               = 'wpfucf_users_count';
    const UCF_BUDDYPRESS                = 'BuddyPress';
    
    /* ==== */
    const UCF_TAB_CAN_PREFIX            = 'vmt_';
}
