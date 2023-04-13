<?php

interface wpForoCrossPostingConst {
    const OPTIONS_SLUG                           = 'cross_posting';
    const CROSSPOST                              = 'wpforo_crosspost';
    const WPFBOARDIDS                            = '_wpforo_boardids';
    const PLUGIN_VERSION                         = 'plugin_version';
    const OPTIONS_TAB                            = 'cross_posting';
    const WPFORO_FORUM_ID                        = 'wpforo_forumid';
    const WPFORO_TOPIC_ID                        = 'wpforo_topicid';
    const WPFORO_TOPIC_TITLE_PREFIX              = 'wpforo_topic_title_prefix';
    const OPTION_POST_TYPES                      = 'post_types';
    const OPTION_POST_CROSSING_EDIT              = 'post_crossing_edit';
    const OPTION_POST_CROSSING_DELETE            = 'post_crossing_delete';
    const OPTION_COMMENT_ADD                     = 'comment_add_crossing';
    const OPTION_COMMENT_EDIT                    = 'comment_edit_crossing';
    const OPTION_COMMENT_DELETE                  = 'comment_delete_crossing';
    const OPTION_FORUM_POST_ADD                  = 'forum_post_add_crossing';
    const OPTION_FORUM_POST_EDIT                 = 'forum_post_edit_crossing';
    const OPTION_FORUM_POST_DELETE               = 'forum_post_delete_crossing';
    const OPTION_NONCE                           = 'wpforo_cp_options_form';
    const OPTION_SUBMIT                          = 'wpforo_cp_save_options';
    const META_POST_FORUM_ID                     = 'crossposting_forumid';
    const META_TOPIC_TITLE_PREFIX                = 'crossposting_topic_title_prefix';
    const META_POST_TOPIC_ID                     = 'crossposting_topicid';
    const META_POST_CROSPOSTING_DISABLED         = 'crossposting_disabled';
    const META_COMMENT_FORUM_POST_ID             = 'crossposting_post';
    const META_COMMENT_SOURCE                    = 'crossposting_source';
    const META_COMMENT_ACTION_SOURCE             = '_crose_posting_action_source';
    const META_COMMENT_SOURCE_BLOG               = 'blog';
    const META_COMMENT_SOURCE_FORUM              = 'forum';
    const META_CROSPOSTING_CONTENT               = 'crossposting_content';
    const OPTION_SHOW_POST_TOPIC_REL             = 'show_post_topic_rel';
    const OPTION_SHOW_COMMENT_REPLY_REL          = 'show_comment_reply_rel';
    const OPTION_SHOW_TOPIC_POST_REL             = 'show_topic_post_rel';
    const OPTION_SHOW_REPLY_COMMENT_REL          = 'show_reply_comment_rel';
    const OPTION_CROSPOSTING_CONTENT             = 'wpforo_crossposting_content';
    const OPTION_CROSPOSTING_CONTENT_FULL        = 1;
    const OPTION_CROSPOSTING_CONTENT_EXCERPT     = 2;
    const OPTION_CROSPOSTING_CONTENT_CUSTOM      = 3;
    const OPTION_AUTO_CROSPOSTING_REL            = 'auto_crossposting_rel';
    const OPTION_CROSPOSTING_FEATURED_IMAGE      = 'wpforo_crossposting_featured_image';
    const OPTION_CROSPOSTING_FIMAGE_SIZE         = 'wpforo_crossposting_fimage_size';
    const OPTION_CROSPOSTING_FIMAGE_THUMBNAIL    = 'thumbnail';
    const OPTION_CROSPOSTING_FIMAGE_MEDIUM       = 'medium';
    const OPTION_CROSPOSTING_FIMAGE_MLARGE       = 'medium_large';
    const OPTION_CROSPOSTING_FIMAGE_LARGE        = 'large';
    const OPTION_CROSPOSTING_FIMAGE_FULL         = 'full';
    const OPTION_CROSPOSTING_TOPIC_CANONICAL_URL = 'wpforo_crossposting_topic_canonical_url';
}