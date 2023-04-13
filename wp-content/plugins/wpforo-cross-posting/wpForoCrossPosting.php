<?php
/*
 * Plugin Name: wpForo - Blog Cross Posting
 * Plugin URI: http://wpforo.com
 * Description: Blog to Forum and Forum to Blog content synchronization. Blog posts with Forum topics and Blog comments with Forum replies.
 * Version: 3.0.1
 * Author: gVectors Team
 * Author URI: https://gvectors.com/
 * Text Domain: wpforo_cross
 * Domain Path: /languages/
 */

if (!defined('ABSPATH')) {
    exit();
}
if (!defined('WPFOROCROSSPOST_VERSION')) {
    define('WPFOROCROSSPOST_VERSION', '3.0.1');
}
if (!defined('WPFOROCROSSPOST_WPFORO_REQUIRED_VERSION')) {
    define('WPFOROCROSSPOST_WPFORO_REQUIRED_VERSION', '2.0.0');
}
define('WPFOROCROSSPOST_FOLDER', rtrim(plugin_basename(dirname(__FILE__)), '/'));

include_once 'includes/wpForoCrossPostingConst.php';
include_once 'options/wpForoCrossPostingOptions.php';
include_once 'tools/wpFAutoCrossTool.php';
include_once 'tools/wpFUtils.php';
include_once 'includes/wpForoCrossPostingDbManager.php';
include_once 'includes/gvt-api-manager.php';

class wpForoCrossPosting {

    private $options;
    private $db;
    public $version;
    public $isGoodbyeCaptchaActive;
    public $goodbyeCaptchaTocken = '';

    public function __construct() {
        $this->init();
    }

    public function init() {
        if (function_exists('WPF')) {
            $this->options = new wpForoCrossPostingOptions();
            $this->db = new wpForoCrossPostingDbManager();
            add_action('add_meta_boxes', [&$this, 'addMetabox']);
            add_filter('wpforo_settings_init_addons_info', [$this, 'init_settings_info']);
            /* ==== POST ==== */
            add_action('wp_insert_post', [&$this, 'savePost'], 10, 2);
            add_action('transition_post_status', [&$this, 'transitionPostStatus'], 12, 3);
            add_action('before_delete_post', [&$this, 'deletePost']);
            add_filter('the_content', [&$this, 'postContent'], 9999);
            add_action('wpforo_after_move_topic', [&$this, 'moveTopic'], 154, 2);
            /* ==== COMMENT ==== */
            add_action('comment_post', [&$this, 'addComment'], 10, 3);
            add_action('transition_comment_status', [&$this, 'changeCommentStatus'], 99, 3);
            add_action('edit_comment', [&$this, 'editComment'], 99, 2);
            add_action('delete_comment', [&$this, 'deleteComment'], 98, 2);
            add_filter('comment_text', [&$this, 'commentText'], 9999, 3);
            add_action('wpd_change_private_status', [&$this, 'changePrivateCommentStatus'], 9999, 2);
            /* ==== FORUM POST ==== */
            add_action('wpforo_after_add_post', [&$this, 'addForumPost'], 99, 2);
            add_action('wpforo_after_edit_post', [&$this, 'editForumPost'], 99, 2);
            add_action('wpforo_after_delete_post', [&$this, 'deleteForumPost']);
            /* ==== CONTENT INFO ===== */
            add_action('wpforo_tpl_post_loop_after_content', [&$this, 'crossPostingInfo'], 99, 2);
            add_filter('wpforo_content_after', [&$this, 'corosspostFeaturedImage'], 1589, 2);
            add_action('wp_enqueue_scripts', [&$this, 'frontEndStylesScripts']);
            add_action('admin_enqueue_scripts', [&$this, 'adminStylesScripts']);
            add_action('admin_init', [&$this, 'checkVersion']);
            add_action('wpforo_editor_post_submit_after', [&$this, 'printGoodbyeCaptchaField']);
            add_action('wpforo_portable_editor_post_submit_after', [&$this, 'printGoodbyeCaptchaField']);
            if ($this->options->crossPostTopicCanonicalUrl === "blog_post") {
                add_filter('wpforo_seo_meta_tags', [&$this, 'topicCanonicalUrl']);
            }
            /* ==== ===== */
            add_action('wp_ajax_wpf_add_autocross', [&$this, 'addAutoCrossRel']);
            add_action('wp_ajax_wpf_autocross_on_off', [&$this, 'autoCrossOnOff']);
            add_action('wp_ajax_wpf_autocross_delete', [&$this, 'autoCrossRelationDelete']);
            add_action('wp_ajax_wpf_autocross_sync', [&$this, 'autoCrossSync']);
            add_action('wp_insert_post', [&$this, 'checkAutoCross'], 16.78, 2);
            load_plugin_textdomain('wpforo_cross', false, dirname(plugin_basename(__FILE__)) . '/languages/');
            new GVT_API_Manager(__FILE__,
                    'wpforo-settings&wpf_tab=wpforo-cross-posting',
                    'wpforo_settings_page_top');
            $this->initGoodbyeCaptchaField();
        }
    }

    private function initGoodbyeCaptchaField() {
        $this->isGoodbyeCaptchaActive = is_callable([
                    'GdbcWordPressPublicModule',
                    'isCommentsProtectionActivated',
                ]) && GdbcWordPressPublicModule::isCommentsProtectionActivated();
        if ($this->isGoodbyeCaptchaActive) {
            $this->goodbyeCaptchaTocken = GdbcWordPressPublicModule::getInstance()->getTokenFieldHtml();
        }
    }

    public function printGoodbyeCaptchaField() {
        echo $this->goodbyeCaptchaTocken;
    }

    public function addMetabox($postType) {
        $boards = WPF()->board->get_boards(['status' => true]);
        foreach ($boards as $board) {
            WPF()->change_board($board['boardid']);
            if (wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER) && in_array($postType, $this->options->postTypes)) {
                $prefix = WPF()->generate_prefix($board['boardid']);
                add_meta_box($prefix . 'forums_list',
                        __('Blog to Forum Cross Posting',
                                'wpforo_cross') . '(' . $board['title'] . ')',
                        function($post) use ($board) {
                    $this->theForumsList($post, $board['boardid']);
                },
                        $postType,
                        'side',
                        'high');
                if ($this->options->crossPostingContent == wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_CUSTOM) {
                    add_meta_box($prefix . 'crossposting_custom_content',
                            __('Cross-posting Custom Content',
                                    'wpforo_cross') . '(' . $board['title'] . ')',
                            function($post) use ($board) {
                        $this->theCustomContent($post, $board['boardid']);
                    },
                            $postType,
                            'advanced',
                            'high');
                }
            }
        }
    }

    public function theForumsList($post, $boardid = 0) {
        WPF()->change_board($boardid);
        $prefix = WPF()->generate_prefix($boardid);
        $forumID = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
        $topicTitlePrefix = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_TOPIC_TITLE_PREFIX, true);
        $isCrossPostingDisabled = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
        ?>
        <style>
            .block-editor .meta-box-sortables #wpforo_forums_list input[type=text] {
                margin-top: 10px;
            }
        </style>
        <?php if ($isCrossPostingDisabled || ( $forumID && $isCrossPostingDisabled )) { ?>
            <?php if ($forumID) { ?>
                <?php if ($post->post_status === "publish") { ?>
                    <p><strong><?php _e('Blog Post to Forum Topic Cross-posting', 'wpforo_cross'); ?></strong></p>
                    <p><?php
                        _e('You can TURN ON this post to forum cross posting by selecting the target forum in this drop-down menu. Once this post is submitted or updated it\'ll be automatically posted or updated in selected forum as topic with the same title and content.',
                                'wpforo_cross');
                        ?></p>
                <?php } else { ?>
                    <p><strong><?php _e('Blog Post to Forum Topic Cross-posting', 'wpforo_cross'); ?></strong></p>
                    <p style="margin:5px 0px 5px 0px;"><?php _e('Awaiting for cross-posting.', 'wpforo_cross'); ?></p>
                    <p style="margin:5px 0px 5px 0px;"><?php
                        _e('This post will be cross-posted once the status is set to Published',
                                'wpforo_cross');
                        ?></p>
                <?php } ?>
            <?php } else { ?>
                <?php ?>
                <p><strong><?php _e('Blog Post to Forum Topic Cross-posting', 'wpforo_cross'); ?></strong></p>
                <p><?php
                    _e('You can TURN ON this post to forum cross posting by selecting the target forum in this drop-down menu. Once this post is submitted or updated it\'ll be automatically posted or updated in selected forum as topic with the same title and content.',
                            'wpforo_cross');
                    ?></p>
            <?php } ?>
            <select id="parent"
                    name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_FORUM_ID; ?>]"
                    class="postform">
                <option value="0"><?php _e('Cross Posting is Off', 'wpforo_cross'); ?> &nbsp;</option>
                <?php WPF()->forum->tree('select_box', false, 0); ?>
            </select>
            <p><input type="text"
                      name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_TOPIC_TITLE_PREFIX; ?>]"
                      value="<?php echo $topicTitlePrefix; ?>"
                      placeholder="<?php esc_attr_e('Topic title prefix', 'wpforo_cross') ?>"/></p>
            <div>-------------------- <?php _e('OR', 'wpforo_cross'); ?> --------------------</div>
            <label class="wpf-cp-label"><?php
                _e('Connect to a certain topic for further cross-posting of comments to topic replies and vice versa',
                        'wpforo_cross');
                ?></label>
            <p><input type="number"
                      name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_TOPIC_ID; ?>]"
                      value="" min="0"
                      placeholder="<?php esc_attr_e('Topic ID', 'wpforo_cross'); ?>"/></p>
            <?php } else { ?>
                <?php
                $topicID = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                $topicURL = '';
                if ($topicID) {
                    $topicURL = wpforo_topic($topicID, 'url');
                }
                if ($topicURL) {
                    ?>
                <p style="margin:5px 0px 5px 0px;"><?php _e('Syncronization Options:', 'wpforo_cross'); ?>
                <ul style="padding:0px; margin:0px 0px 10px 15px;">
                    <li style="list-style:disc; line-height:16px; margin-bottom:2px; color:<?php echo ( $this->options->postCrossingEdit ) ? '#009900' : '#dd0000'; ?>"><?php
                        _e('Update cross-posted Topic',
                                'wpforo_cross');
                        ?><?php echo ( $this->options->postCrossingEdit ) ? '(on)' : '(off)'; ?></li>
                    <li style="list-style:disc; line-height:16px; margin-bottom:3px; color:<?php echo ( $this->options->postCrossingDelete ) ? '#009900' : '#dd0000'; ?>"><?php
                        _e('Delete cross-posted Topic',
                                'wpforo_cross');
                        ?><?php echo ( $this->options->postCrossingDelete ) ? '(on)' : '(off)'; ?></li>
                </ul>
                </p>
                <input type="hidden"
                       name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_FORUM_ID; ?>]"
                       value="<?php echo intval($forumID); ?>" style="vertical-align:bottom;"/>
                <p><label><input type="checkbox" name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][wpfcp_turn_off]"
                                 value="1"
                                 style="vertical-align:bottom;"/> <?php
                                 _e('Turn off Cross Posting',
                                         'wpforo_cross');
                                 ?></label></p>
                <p><a href="<?php echo esc_url($topicURL); ?>" target="_blank"><?php
                        _e('View cross-posted topic',
                                'wpforo_cross');
                        ?>
                        &raquo;</a></p>
            <?php } else { ?>
                <p><strong><?php _e('Blog Post to Forum Topic Cross-posting', 'wpforo_cross'); ?></strong></p>
                <p><?php
                    _e('You can TURN ON this post to forum cross posting by selecting the target forum in this drop-down menu. Once this post is submitted or updated it\'ll be automatically posted or updated in selected forum as topic with the same title and content.',
                            'wpforo_cross');
                    ?></p>
                <select id="parent"
                        name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_FORUM_ID; ?>]"
                        class="postform">
                    <option value="0"><?php _e('Cross Posting is Off', 'wpforo_cross'); ?> &nbsp;</option>
                    <?php WPF()->forum->tree('select_box', false, [$forumID]); ?>
                </select>
                <p><input type="text"
                          name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_TOPIC_TITLE_PREFIX; ?>]"
                          value="<?php echo $topicTitlePrefix; ?>"
                          placeholder="<?php esc_attr_e('Topic title prefix', 'wpforo_cross') ?>"/></p>
                <div>-------------------- <?php _e('OR', 'wpforo_cross'); ?> --------------------</div>
                <label class="wpf-cp-label"><?php
                    _e('Connect to a certain topic for further cross-posting of comments to topic replies and vice versa',
                            'wpforo_cross');
                    ?></label>
                <p><input type="number"
                          name="<?php echo wpForoCrossPostingOptions::CROSSPOST; ?>[<?php echo $boardid; ?>][<?php echo wpForoCrossPostingOptions::WPFORO_TOPIC_ID; ?>]"
                          value=""
                          min="0" placeholder="<?php esc_attr_e('Topic ID', 'wpforo_cross'); ?>"/></p>
                <?php } ?>
            <?php } ?>

        <?php
    }

    public function theCustomContent($post, $boardid = 0) {
        $prefix = WPF()->generate_prefix($boardid);
        $metaKey = $prefix . wpForoCrossPostingOptions::META_CROSPOSTING_CONTENT;
        $name = wpForoCrossPostingOptions::CROSSPOST . '[' . $boardid . '][' . wpForoCrossPostingOptions::META_CROSPOSTING_CONTENT . ']';

        $content = get_post_meta($post->ID, $metaKey, true);
        wp_editor($content, $name, ['textarea_rows' => 10]);
    }

    /* ============ POST ===================== */

    public function savePost($postID, $post) {
        if (wp_is_post_revision($postID)) {
            return;
        }
        $boardids = [];
        if (isset($_POST[wpForoCrossPostingOptions::CROSSPOST]) && !empty($_POST[wpForoCrossPostingOptions::CROSSPOST]) && is_array($_POST[wpForoCrossPostingOptions::CROSSPOST])) {
            foreach ($_POST[wpForoCrossPostingOptions::CROSSPOST] as $boardid => $args) {
                $this->_savePost($postID, $post, $boardid, $args);
                $boardids[] = $boardid;
            }
            update_post_meta($postID, wpForoCrossPostingOptions::WPFBOARDIDS, $boardids);
        }
    }

    private function _savePost($postID, $post, $boardid, $args) {
        $board = WPF()->board->get_board(intval($boardid));
        if (!$board) {
            return;
        }

        WPF()->change_board($boardid);

        if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER) && !in_array($post->post_type, $this->options->postTypes)) {
            return;
        }

        $prefix = WPF()->generate_prefix($boardid);
        $postStatus = $post->post_status;
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
        $customContent = isset($args[wpForoCrossPostingOptions::META_CROSPOSTING_CONTENT]) ? $args[wpForoCrossPostingOptions::META_CROSPOSTING_CONTENT] : '';
        $customContent = trim($customContent);
        if ($this->options->crossPostingContent == wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_CUSTOM && $customContent) {
            update_post_meta($postID, $prefix . wpForoCrossPostingOptions::META_CROSPOSTING_CONTENT, $customContent);
        }
        if ($action === 'inline-save') {
            $turnOff = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
            $forumID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
            $topicTitlePrefix = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_TOPIC_TITLE_PREFIX, true);
            $savedForumID = null;
        } else {
            $turnOff = isset($args['wpfcp_turn_off']) ? 1 : 0;
            $forumID = isset($args[wpForoCrossPostingOptions::WPFORO_FORUM_ID]) ? $args[wpForoCrossPostingOptions::WPFORO_FORUM_ID] : '';
            $topicTitlePrefix = isset($args[wpForoCrossPostingOptions::WPFORO_TOPIC_TITLE_PREFIX]) ? $args[wpForoCrossPostingOptions::WPFORO_TOPIC_TITLE_PREFIX] : '';
            $savedForumID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
            if ($turnOff) {
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 1);
            }
        }
        $topicID = isset($args[wpForoCrossPostingOptions::WPFORO_TOPIC_ID]) ? $args[wpForoCrossPostingOptions::WPFORO_TOPIC_ID] : '';
        if ($forumID && !$turnOff) {
            update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, $forumID);
            update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_TOPIC_TITLE_PREFIX, $topicTitlePrefix);
            $topicID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
            $topic = WPF()->topic->get_topic($topicID);
            if ($postStatus === 'publish') {
                $postClone = clone $post;
                $postClone->post_title = $topicTitlePrefix . $post->post_title;
                if ($topic) {
                    $this->editTopic($postClone, $forumID, $topic, $prefix);
                } else {
                    $this->addTopic($postClone, $forumID, $prefix);
                }
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 0);
                $this->synchComments($postID, $prefix);
            } elseif ($postStatus === 'draft') {
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 1);
            } elseif ($postStatus === 'trash') {
                WPF()->moderation->post_unapprove($topic['first_postid']);
                WPF()->notice->clear();
            }
            if ($savedForumID && $forumID && $forumID != $savedForumID && $topic && $this->options->postCrossingEdit) {
                WPF()->topic->move($topicID, $forumID);
            }
            WPF()->notice->clear();
        } else if ($topicID) {
            if ($postStatus === 'publish') {
                update_post_meta($postID, $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, $topicID);
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 0);
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, wpforo_topic($topicID, 'forumid'));
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_TOPIC_TITLE_PREFIX, $topicTitlePrefix);
                $this->synchComments($postID, $prefix);
            } elseif ($postStatus === 'draft') {
                update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 1);
            } elseif ($postStatus === 'trash') {
                $topic = WPF()->topic->get_topic($topicID);
                WPF()->moderation->post_unapprove($topic['first_postid']);
                WPF()->notice->clear();
            }
        }
    }

    public function transitionPostStatus($new_status, $old_status, $post) {
       
        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($post->ID);
         
        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }

            WPF()->change_board($boardid);

            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                return;
            }
            $prefix = WPF()->generate_prefix($boardid);

            $forumID = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
            $turnOff = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
            if (!$forumID || $turnOff) {
                continue;
            }
            
            if ($old_status == 'future' && $new_status == 'publish') {
                $this->removeAccessFilters();
                $topicID = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                $topic = WPF()->topic->get_topic($topicID);
                if ($topic) {
                    $this->editTopic($post, $forumID, $topic, $prefix);
                } else {
                    $this->addTopic($post, $forumID, $prefix);
                }
                update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 0);
                $this->synchComments($post->ID, $prefix);
                WPF()->notice->clear();
            } else if ($new_status == 'trash') {
                if (!$this->options->postCrossingDelete) {
                    continue;
                }
                $topicID = get_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                $topic = WPF()->topic->get_topic($topicID);
                if ($topic) {
                    WPF()->moderation->post_unapprove($topic['first_postid']);
                    WPF()->notice->clear();
                }
            } else if ($new_status != 'publish' && $old_status == 'publish') {
                update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 1);
            }
        }
    }

    private function synchComments($postID, $prefix) {
        if ($postID) {
            if ($this->options->commentCrossingAdd) {
                $comments = get_comments([
                    'post_id' => $postID,
                    'orderby' => 'comment_date',
                    'order' => 'ASC',
                    'status' => 'approve',
                ]);
                if ($comments) {
                    foreach ($comments as $comment) {
                        $forum_post_id = get_comment_meta($comment->comment_ID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
                        $is_add = true;
                        if ($forum_post_id) {
                            $forum_post = WPF()->post->get_post($forum_post_id, false);
                            $is_add = $forum_post ? false : true;
                        }
                        if ($is_add) {
                            $this->addComment($comment->comment_ID, 1, (array) $comment);
                        }
                    }
                }
            }

            if ($this->options->forumPostCrossingAdd) {
                $topicID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                if ($topicID) {
                    $replies = WPF()->post->get_posts(['topicid' => $topicID, 'is_first_post' => 0]);
                    if ($replies) {
                        foreach ($replies as $reply) {
                            $blogCommentId = $this->db->getBlogCommentID($reply['postid'], $prefix);
                            if (!$blogCommentId) {
                                $topic = WPF()->topic->get_topic($topicID);
                                $this->addForumPost($reply, $topic);
                            }
                        }
                    }
                }
            }
        }
    }

    private function addTopic($post, $forumID, $prefix) {
        $args = $this->getRequestData($forumID, $post, $prefix);
        if ($args) {
            $args['body'] = addslashes($args['body']);
            $args['tags'] = wpFAutoCrossTool::getPostTags($post->ID);
            $newTopicID = WPF()->topic->add($args);
            if ($newTopicID) {
                update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, $forumID);
                update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, $newTopicID);
            }
        }
    }

    private function editTopic($post, $forumID, $topic, $prefix) {
        if (!$this->options->postCrossingEdit) {
            return false;
        }
        $args = $this->getRequestData($forumID, $post, $prefix);
        if ($args && $topic) {
            $args['body'] = addslashes($args['body']);
            $topicTags = explode(',', $topic['tags']);
            $postTags = wpFAutoCrossTool::getPostTags($post->ID);
            if ($topicTags) {
                $topic['tags'] = array_unique(array_merge($topicTags, $postTags));
            } else {
                $topic['tags'] = $postTags;
            }

            $args = wp_parse_args($args, $topic);
            WPF()->topic->edit($args);
        }
    }

    private function getRequestData($forumID, $post, $prefix) {
        $args = [
            'forumid' => $forumID,
            'title' => $post->post_title,
            'userid' => $post->post_author,
            'body' => $this->crosspostingContent($post, $prefix),
            'created' => $post->post_date_gmt,
            'slug' => $post->post_name,
            'status' => 0,
        ];
        $user = get_user_by("id", $post->post_author);
        $args['name'] = !empty($user->display_name) ? $user->display_name : "Anonymous";
        $args['email'] = !empty($user->user_email) ? $user->user_email : "anonymous@example.com";
        return $args;
    }

    private function crosspostingContent($post, $prefix) {
        $content = $post->post_content;
        if ($this->options->crossPostingContent == wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_EXCERPT) {
            $excerpt = $post->post_excerpt;
            if (trim($excerpt)) {
                $content = $excerpt;
            } else {
                $excerpt_length = apply_filters('excerpt_length', 55);
                $content = wp_trim_words($content, $excerpt_length);
            }
        } elseif ($this->options->crossPostingContent == wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_CUSTOM) {
            $customContent = get_post_meta($post->ID, $prefix . wpForoCrossPostingOptions::META_CROSPOSTING_CONTENT, true);
            if (trim($customContent)) {
                $content = $customContent;
            }
        }

        return $content;
    }

    public function corosspostFeaturedImage($conten, $post) {
        if ($this->options->crossPostFeaturedImage && !empty($post['is_first_post'])) {
            $boardid = WPF()->board->get_current('boardid');
            $prefix = WPF()->generate_prefix($boardid);
            $blogPostID = $this->db->getBlogPostID($post['topicid'], $prefix);
            if ($blogPostID) {
                $postTumbnail = get_the_post_thumbnail_url($blogPostID, $this->options->crossPostFImageSize);
                $postTumbnailFull = get_the_post_thumbnail_url($blogPostID, wpForoCrossPostingOptions::OPTION_CROSPOSTING_FIMAGE_FULL);
                if ($postTumbnail && $postTumbnailFull) {
                    $html = '<div class="wpf-cross-image"><a href="' . $postTumbnailFull . '" data-gallery="#wpf-content-blueimp-gallery"><img alt="wpf-cross-image" src="' . $postTumbnail . '"></a></div>';
                    $conten = $html . $conten;
                }
            }
        }

        return $conten;
    }

    public function deletePost($postID) {
        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }

            WPF()->change_board($boardid);

            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                continue;
            }

            $prefix = WPF()->generate_prefix($boardid);

            if ($this->options->postCrossingDelete) {
                $forumID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
                $topicID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                $isCrossPostingDisabled = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
                if ($forumID && $topicID && !$isCrossPostingDisabled) {
                    WPF()->topic->delete($topicID);
                    WPF()->notice->clear();
                }
            }
        }
    }

    public function postContent($content) {
        global $post;
        $postID = $post->ID;
        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }

            WPF()->change_board($boardid);

            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                continue;
            }

            $prefix = WPF()->generate_prefix($boardid);

            if ($this->options->showPostTopicRel && is_singular()) {

                $topicID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                $isCrossPostingDisabled = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
                if ($topicID && !$isCrossPostingDisabled) {
                    $linkText = wpforo_phrase('This article is also published as a forum topic here', false);
                    if ($this->options->crossPostingContent != wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_FULL) {
                        $linkText = wpforo_phrase('More discussion', false);
                    }
                    $topicURL = wpforo_topic($topicID, 'url');
                    $content .= '<div class="wpfcp-article-info">';
                    $content .= '<a href="' . esc_url($topicURL) . '">' . $linkText . ' &raquo;</a>';
                    $content .= '</div><div class="wpfcpcb"></div>';
                }
            }
        }
        return $content;
    }

    /* ============ COMMENT ===================== */

    public function addComment($commentID, $commentApproved, $data) {
        if ($commentApproved === 1 && !isset($data['referrer']) && isset($data['comment_post_ID'])) {
            $postID = $data['comment_post_ID'];
            $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

            foreach ($boardids as $boardid) {
                $board = WPF()->board->get_board(intval($boardid));
                if (!$board) {
                    continue;
                }
                WPF()->change_board($boardid);
                if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER) && !$this->options->commentCrossingAdd) {
                    continue;
                }
                $prefix = WPF()->generate_prefix($boardid);
                $comment = $this->arrayToObject($data);
                $this->_addComment($commentID, $comment, $prefix);
            }
        }
    }

    public function changeCommentStatus($newStatus, $oldStatus, $comment) {
        $commentID = $comment->comment_ID;
        $postID = $comment->comment_post_ID;
        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }
            WPF()->change_board($boardid);
            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER) && !$this->options->commentCrossingAdd) {
                continue;
            }
            $prefix = WPF()->generate_prefix($boardid);
            $forumPostID = get_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
            if ($newStatus == 'approved' && !$forumPostID) {
                $this->_addComment($commentID, $comment, $prefix);
            }
        }
    }

    public function changePrivateCommentStatus($commentsId, $status) {
        foreach ($commentsId as $commentId) {
            $comment = get_comment($commentId);
            if (!$comment) {
                continue;
            }

            $postID = $comment->comment_post_ID;
            $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

            foreach ($boardids as $boardid) {
                $board = WPF()->board->get_board(intval($boardid));
                if (!$board) {
                    continue;
                }
                WPF()->change_board($boardid);
                if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                    continue;
                }
                $prefix = WPF()->generate_prefix($boardid);

                if ($status === 'private') {
                    $this->_addComment($commentId, $comment, $prefix);
                } else {
                    $forumPostID = get_comment_meta($commentId, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
                    $forumPost = WPF()->post->get_post($forumPostID);
                    if ($forumPost && $forumPostID) {
                        $args = $this->buildBlogCommentArgs($comment, $prefix);
                        if ($args) {
                            update_comment_meta($commentId, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE, wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG);
                            WPF()->post->delete($forumPostID);
                            WPF()->notice->clear();
                        }
                    }
                }
            }
        }
    }

    private function _addComment($commentID, $comment, $prefix) {
        if ($comment->comment_type === "" || $comment->comment_type === "comment") {
            $args = $this->buildBlogCommentArgs($comment, $prefix);
            if ($args) {
                $forumPostID = WPF()->post->add($args);
                if ($forumPostID) {
                    update_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, $forumPostID);
                    update_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_SOURCE, wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG);
                }
                WPF()->notice->clear();
            }
        }
    }

    public function editComment($commentID, $data) {

        $postID = $data['comment_post_ID'];
        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }
            WPF()->change_board($boardid);
            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                continue;
            }
            $prefix = WPF()->generate_prefix($boardid);

            $forumPostID = get_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
            $actionSource = get_comment_meta($commentID, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE, true);
            if ($actionSource == wpForoCrossPostingOptions::META_COMMENT_SOURCE_FORUM) {
                delete_comment_meta($commentID, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE);
                return;
            }
            if ($forumPostID && $this->options->commentCrossingEdit) {
                $comment = $this->arrayToObject($data);
                $args = $this->buildBlogCommentArgs($comment, $prefix);
                if ($args) {
                    $args['postid'] = $forumPostID;
                    update_comment_meta($commentID, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE, wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG);
                    WPF()->post->edit($args);
                    WPF()->notice->clear();
                }
            }
        }
    }

    public function deleteComment($commentID, $comment) {

        $postID = $comment->comment_post_ID;
        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }
            WPF()->change_board($boardid);
            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                continue;
            }
            $prefix = WPF()->generate_prefix($boardid);

            $forumPostID = get_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
            $forumPost = WPF()->post->get_post($forumPostID);
            if ($forumPost && $forumPostID && $this->options->commentCrossingDelete) {
                $args = $this->buildBlogCommentArgs($comment, $prefix);
                if ($args) {
                    update_comment_meta($commentID, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE, wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG);
                    WPF()->post->delete($forumPostID);
                    WPF()->notice->clear();
                }
            }
        }
    }

    private function buildBlogCommentArgs($comment, $prefix) {
        $args = [];
        if (!$comment) {
            return $args;
        }
        $postID = $comment->comment_post_ID;
        $blogPost = get_post($postID);
        $commentParent = $comment->comment_parent;
        $commentContent = $comment->comment_content;
        $userID = isset($comment->user_id) ? $comment->user_id : 0;
        $userName = $comment->comment_author;
        $userEmail = $comment->comment_author_email;
        $topicID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
        $forumID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
        $forumPostParent = get_comment_meta($commentParent, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
        $isCrossPostingDisabled = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
        if ($topicID && $forumID && !$isCrossPostingDisabled) {
            $args['forumid'] = $forumID;
            $args['topicid'] = $topicID;
            $args['title'] = wpforo_phrase('RE:', false) . ' ' . $blogPost->post_title;
            $args['body'] = $commentContent;
            $args['parentid'] = $forumPostParent ? $forumPostParent : 0;
            $args['userid'] = $userID;
            $args['name'] = $userName;
            $args['email'] = $userEmail;
            $args['referrer'] = wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG;
            $args['created'] = $comment->comment_date_gmt;
        }

        return $args;
    }

    public function commentText($commentText, $comment = null, $args = []) {
        $html = '';
        if (!is_string($commentText)) {
            return $commentText;
        }
        $wpdiscuzLoaded = false;
        if (function_exists("wpDiscuz")) {
            $wpdiscuz = wpDiscuz();
            $wpdiscuzLoaded = $wpdiscuz->isWpdiscuzLoaded;
        }
        if ($comment && ( is_singular() || $wpdiscuzLoaded || !empty($args['is_wpdiscuz_comment']) )) {

            $postID = $comment->comment_post_ID;
            $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);

            foreach ($boardids as $boardid) {
                $board = WPF()->board->get_board(intval($boardid));
                if (!$board) {
                    continue;
                }
                WPF()->change_board($boardid);
                if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                    continue;
                }
                $prefix = WPF()->generate_prefix($boardid);

                $forumPostID = get_comment_meta($comment->comment_ID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
                $isCrossPostingDisabled = get_post_meta($comment->comment_post_ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
                $url = wpforo_post($forumPostID, 'url');
                if ($forumPostID && $url && !$isCrossPostingDisabled) {
                    WPF()->current_object['forumid'] = get_post_meta($comment->comment_post_ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
                    WPF()->current_object['forum'] = wpforo_forum(WPF()->current_object['forumid']);
                    $commentText = wpforo_content_filter($commentText);
                    if ($this->options->showCommentReplyRel) {
                        $html .= '<div class="wpfcp-comment-info" title="' . wpforo_phrase('This comment is also posted as a reply in related forum topic.', false) . '">';
                        $html .= '<a href="' . esc_url($url) . '">' . wpforo_phrase('forum reply', false) . ' &raquo;</a>';
                        $html .= '</div><div class="wpfcpcb"></div>';
                    }
                }
            }
        }

        return $commentText . $html;
    }

    /* ==== FORUM POST ==== */

    public function addForumPost($post, $topic) {
        if (!isset($post['referrer']) && $this->options->forumPostCrossingAdd) {
            $boardid = WPF()->board->get_current('boardid');
            $prefix = WPF()->generate_prefix($boardid);
            $blogPostID = $this->db->getBlogPostID($topic['topicid'], $prefix);
            if ($blogPostID) {
                $commentParent = 0;
                $userDisplayName = '';
                $userEmail = '';
                if ($post['userid']) {
                    $user = get_user_by('ID', $post['userid']);
                    $userDisplayName = $user->display_name;
                    $userEmail = $user->user_email;
                } elseif ($post['name'] && $post['email']) {
                    $userDisplayName = $post['name'];
                    $userEmail = $post['email'];
                } else {
                    return;
                }

                if ($post['parentid']) {
                    $commentParent = $this->db->getBlogCommentID($post['parentid'], $prefix);
                }
                $data = [
                    'comment_post_ID' => $blogPostID,
                    'comment_author' => $userDisplayName,
                    'comment_author_email' => $userEmail,
                    'comment_author_url' => '',
                    'comment_content' => $post['body'],
                    'comment_parent' => $commentParent,
                    'user_ID' => $post['userid'],
                    'comment_type' => version_compare(get_bloginfo("version"),
                            "5.5",
                            ">=") ? "comment" : "",
                    'comment_date_gmt' => $post['created'],
                    'referrer' => wpForoCrossPostingOptions::META_COMMENT_SOURCE_FORUM,
                ];
                $commentID = wp_new_comment($data);
                if ($commentID) {
                    update_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, $post['postid']);
                    update_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_SOURCE, wpForoCrossPostingOptions::META_COMMENT_SOURCE_FORUM);
                }
            }
        }
    }

    public function editForumPost($filteredArgs) {
        if ($this->options->forumPostCrossingEdite) {
            $boardid = WPF()->board->get_current('boardid');
            $prefix = WPF()->generate_prefix($boardid);
            $blogPostID = $this->db->getBlogPostID($filteredArgs['topicid'], $prefix);
            $commentID = $this->db->getBlogCommentID($filteredArgs['postid'], $prefix);
            $actionSource = get_comment_meta($commentID, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE, true);
            if ($blogPostID && $commentID && $actionSource != wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG) {
                update_comment_meta($commentID,
                        wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE,
                        wpForoCrossPostingOptions::META_COMMENT_SOURCE_FORUM);
                $commentArgs = [
                    'comment_ID' => $commentID,
                    'comment_content' => $filteredArgs['body'],
                    'referrer' => wpForoCrossPostingOptions::META_COMMENT_SOURCE_FORUM,
                ];
                wp_update_comment($commentArgs);
            } else {
                delete_comment_meta($commentID, wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE);
            }
        }
    }

    public function moveTopic($topic, $forumid) {
        $topicID = $topic['topicid'];
        $boardid = WPF()->board->get_current('boardid');
        $prefix = WPF()->generate_prefix($boardid);
        $postID = $this->db->getBlogPostID($topicID, $prefix);
        if ($postID) {
            update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, $forumid);
        }
    }

    public function deleteForumPost($post) {
        if ($this->options->forumPostCrossingDelete) {
            $forumPostID = $post['postid'];
            $boardid = WPF()->board->get_current('boardid');
            $prefix = WPF()->generate_prefix($boardid);
            $blogPostID = $this->db->getBlogPostID($post['topicid'], $prefix);
            if ($blogPostID) {
                $blogCommentID = $this->db->getBlogCommentID($forumPostID, $prefix);
                $actionSource = get_comment_meta($blogCommentID,
                        wpForoCrossPostingOptions::META_COMMENT_ACTION_SOURCE,
                        true);
                if ($actionSource != wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG) {
                    wp_delete_comment($blogCommentID);
                }
                $this->deleteBlogPostMeta($blogPostID, $post, $prefix);
            }
        }
    }

    private function deleteBlogPostMeta($blogPostID, $post, $prefix) {
        if (!empty($post['is_first_post'])) {
            delete_post_meta($blogPostID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID);
            delete_post_meta($blogPostID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID);
        }
    }

    public function init_settings_info($addons) {
        $addons['wpforo-cross-posting'] = [
            'title' => esc_html__('"Forum - Blog" Cross Posting', "wpforo"),
            'title_original' => '"Forum - Blog" Cross Posting',
            'icon' => '<img src="' . WPFORO_URL . '/assets/addons/cross/header.png' . '" alt="Polls Logo">',
            'description' => __('Blog to Forum and Forum to Blog content synchronization. Blog posts with Forum topics and Blog comments with Forum replies.',
                    'wpforo'),
            'description_original' => 'Blog to Forum and Forum to Blog content synchronization. Blog posts with Forum topics and Blog comments with Forum replies.',
            'docurl' => '',
            'status' => 'ok',
            'with_custom_form' => true,
            'base' => false,
            'callback_for_page' => function() {
                require plugin_dir_path(__FILE__) . 'options/options.php';
            },
            'options' => [
            ],
        ];

        return $addons;
    }

    public function getOptions() {
        return $this->options;
    }

    private function arrayToObject($array) {
        if (!is_array($array)) {
            return $array;
        }
        $object = new \stdClass();
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $name => $value) {
                $name = trim($name);
                if (!empty($name)) {
                    $object->$name = $this->arrayToObject($value);
                }
            }

            return $object;
        } else {
            return false;
        }
    }

    public function crossPostingInfo($post, $member) {
        if (!isset($post['is_first_post'])) {
            return false;
        }
        if ($post['is_first_post']) {
            $id = $post['topicid'];
            $type = 'topic';
        } else {
            $id = $post['postid'];
            $type = 'reply';
        }
        $crossURL = '';
        $crossObjectID = $this->crossObjectID($id, $type);
        if ($crossObjectID) {
            if ($type == 'topic') {
                $crossURL = get_permalink($crossObjectID);
            } else {
                $crossURL = get_comment_link($crossObjectID);
                wpFCrossPostUtils::wpdiscuzMediaUploder($crossObjectID);
            }
        }


        if (isset($id) && $crossURL) {
            if ($post['is_first_post'] && $crossURL && $this->options->showTopicPostRel):
                $linkText = __('This topic is also published as an article here', 'wpforo_cross');
                if ($this->options->crossPostingContent != wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_FULL) {
                    $linkText = wpforo_phrase('Read more', false);
                }
                ?>
                <div class="wpfcp-topic-info"><a
                        href="<?php echo esc_url($crossURL); ?>"><?php wpforo_phrase($linkText); ?> &raquo;</a>
                </div>
                <div class="wpfcpcr"></div>
            <?php elseif (!$post['is_first_post'] && $this->options->showReplyCommentRel): ?>
                <div class="wpfcp-post-info"
                     title="<?php wpforo_phrase('This reply is also posted as a comment under related article.'); ?>">
                    <a href="<?php echo esc_url($crossURL); ?>"><i
                            class="far fa-comment"></i>&nbsp;&nbsp;<?php wpforo_phrase('comment'); ?></a></div>
                <div class="wpfcpcr"></div>
                <?php
            endif;
        }
    }

    public function crossObjectID($id, $type = 'topic') {
        if (!$id) {
            return false;
        }
        $wpforo = WPF();

        $boardid = WPF()->board->get_current('boardid');
        $prefix = WPF()->generate_prefix($boardid);

        if ($type == 'topic') {
            $sql = $wpforo->db->prepare("SELECT `post_id` FROM `{$wpforo->db->postmeta}` WHERE `meta_key` = %s AND `meta_value` = %s", '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, $id);
            $postID = $wpforo->db->get_var($sql);
            $isCrossPostingDisabled = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
            if ($postID && !$isCrossPostingDisabled) {
                return $postID;
            }
        } elseif ($type == 'reply') {
            $sql = $wpforo->db->prepare("SELECT `comment_id` FROM `{$wpforo->db->commentmeta}` WHERE `meta_key` = %s AND `meta_value` = %s", '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, $id);
            $commentID = $wpforo->db->get_var($sql);
            $isCrossPostingDisabled = true;
            if ($commentID && ( $comment = get_comment($commentID) )) {
                $isCrossPostingDisabled = get_post_meta($comment->comment_post_ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
            }
            if ($commentID && !$isCrossPostingDisabled) {
                return $commentID;
            }
        }

        return false;
    }

    public function frontEndStylesScripts() {
        wp_register_style('wpforo-cross-css', plugins_url('/assets/css/wpforo-cross.css', __FILE__));
        wp_enqueue_style('wpforo-cross-css');
        if (is_rtl()) {
            wp_register_style('wpforo-cross-rtl-css', plugins_url('/assets/css/wpforo-cross-rtl.css', __FILE__));
            wp_enqueue_style('wpforo-cross-rtl-css');
        }
        if (class_exists('WpdiscuzMediaUploader') && !function_exists('WPF_ATTACH')) {
            wp_register_style('wpf-wpdiscuz-uploader',
                    plugins_url('/assets/css/wpf-wpdiscuz-uploader.css', __FILE__));
            wp_enqueue_style('wpf-wpdiscuz-uploader');
        }
    }

    public function adminStylesScripts() {
        if (wpfval($_GET, 'page') === wpforo_prefix_slug('settings')) {
            wp_register_style('wpforo-cross-admin-css',
                    plugins_url('/assets/css/wpforo-cross-admin.css', __FILE__));
            wp_enqueue_style('wpforo-cross-admin-css');
            wp_register_script("wpforo-cross-admin-js", plugins_url('/assets/js/wpforo-cross-admin.js', __FILE__),
                    ['jquery']);
            wp_enqueue_script("wpforo-cross-admin-js");
            wp_localize_script("wpforo-cross-admin-js", 'wpfCrossAdmin',
                    ['ajaxURL' => admin_url('admin-ajax.php'),]);
        }
    }

    public function checkVersion() {
        $pluginData = get_plugin_data(__FILE__);
        $this->version = '1.0.0';
        if (version_compare($pluginData['Version'], $this->version, '>')) {
            if ($this->version === '1.0.0') {
                add_option(wpForoCrossPostingOptions::PLUGIN_VERSION, $pluginData['Version']);
            } else {
                update_option(wpForoCrossPostingOptions::PLUGIN_VERSION, $pluginData['Version']);
            }
            if ($pluginData['Version'] === '2.0.1') {
                $this->db->deleteAutocrossOptions();
            }
        }
    }

    private function initCurrentUser() {
        $userID = 0;
        if (empty(WPF()->current_user['userid'])) {
            $adminUser = WPF()->member->get_members(['groupid' => 1, 'row_count' => 1]);
            $userID = $adminUser[0]['userid'];
            wp_set_current_user($adminUser[0]['userid'], $adminUser[0]['user_login']);
            WPF()->member->init_current_user();
        }

        return $userID;
    }

    public function addAutoCrossRel() {
        if (!current_user_can('manage_options')) {
            exit();
        }
        $row = '';
        $boardid = isset($_POST['board_id']) ? (int) $_POST['board_id'] : 0;
        $board = WPF()->board->get_board(intval($boardid));
        if (!$board) {
            $row = '<div class="wpfcp-error"><span class="dashicons dashicons-warning" style="line-height: inherit; font-size: 18px; color: #d06363;"></span> ' . __('Board not found.',
                            'wpforo_cross') . '</div>';
            wp_die($row);
        }
        WPF()->change_board($boardid);
        $postType = filter_input(INPUT_POST, "post_type", FILTER_SANITIZE_STRING);
        $termID = filter_input(INPUT_POST, "term_id", FILTER_SANITIZE_NUMBER_INT);
        $forumID = filter_input(INPUT_POST, "forum_id", FILTER_SANITIZE_NUMBER_INT);
        if ($this->addRelation($postType, $termID, $forumID)) {
            $row = wpFAutoCrossTool::addAutoCrossRow($termID, $forumID, $postType);
        } else {
            $row = '<div class="wpfcp-error"><span class="dashicons dashicons-warning" style="line-height: inherit; font-size: 18px; color: #d06363;"></span> ' . __('This cross-posting rule already exists.',
                            'wpforo_cross') . '</div>';
        }
        wp_die($row);
    }

    private function addRelation($postType, $termID, $forumID) {
        if ($postType && $forumID && is_numeric($termID)) {
            $autoCrossPostRelations = get_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), []);
            if (isset($autoCrossPostRelations[$postType]['relations'])) {
                $relations = $autoCrossPostRelations[$postType]['relations'];
                foreach ($relations as $rel) {
                    if ($rel['term_id'] == $termID && $rel['forum_id'] == $forumID) {
                        return false;
                    }
                }
            }
            $autoCrossPostRelations[$postType]['relations'][] = [
                'term_id' => $termID,
                'forum_id' => $forumID,
                'enabled' => 1,
            ];

            return update_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), $autoCrossPostRelations);
        }
    }

    public function checkAutoCross($postID, $post) {

        if (wp_is_post_revision($postID)) {
            return;
        }

        $boardids = wpFCrossPostUtils::getRelatedBoardIDs($postID);
        foreach ($boardids as $boardid) {
            $board = WPF()->board->get_board(intval($boardid));
            if (!$board) {
                continue;
            }
            WPF()->change_board($boardid);
            if (!wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
                continue;
            }

            $prefix = WPF()->generate_prefix($boardid);
            $autoCrossPostRelations = get_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL));
            $relations = isset($autoCrossPostRelations[$post->post_type]['relations']) ? $autoCrossPostRelations[$post->post_type]['relations'] : false;
            $savedForumID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, true);
            if (!$savedForumID && $relations) {
                $taxonomies = get_object_taxonomies($post->post_type);
                $this->removeAccessFilters();
                if ($taxonomies) {
                    foreach ($taxonomies as $taxonomy) {
                        $terms = wp_get_post_terms($postID, $taxonomy, ['fields' => 'ids']);
                        if ($terms && !is_wp_error($terms)) {
                            foreach ($relations as $relation) {
                                if ($relation['enabled'] && in_array($relation['term_id'], $terms)) {
                                    if ($post->post_status == 'publish') {
                                        $this->addTopic($post, $relation['forum_id'], $prefix);
                                    } elseif ($post->post_status == 'future') {
                                        update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, $relation['forum_id']);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    foreach ($relations as $relation) {
                        if ($relation['enabled'] && $relation['term_id'] == 0) {
                            if ($post->post_status == 'publish') {
                                $this->addTopic($post, $relation['forum_id'], $prefix);
                            } elseif ($post->post_status == 'future') {
                                update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, $relation['forum_id']);
                            }
                            break;
                        }
                    }
                }
            }
            WPF()->notice->clear();
        }
    }

    public function autoCrossOnOff() {
        if (!current_user_can('manage_options')) {
            exit();
        }
        $boardid = isset($_POST['board_id']) ? (int) $_POST['board_id'] : 0;
        $board = WPF()->board->get_board(intval($boardid));
        if (!$board) {
            $row = '<div class="wpfcp-error"><span class="dashicons dashicons-warning" style="line-height: inherit; font-size: 18px; color: #d06363;"></span> ' . __('Board not found.',
                            'wpforo_cross') . '</div>';
            wp_die($row);
        }
        WPF()->change_board($boardid);
        $postType = filter_input(INPUT_POST, "post_type", FILTER_SANITIZE_STRING);
        $termID = filter_input(INPUT_POST, "term_id", FILTER_SANITIZE_NUMBER_INT);
        $forumID = filter_input(INPUT_POST, "forum_id", FILTER_SANITIZE_NUMBER_INT);
        $enable = filter_input(INPUT_POST, "enable", FILTER_SANITIZE_NUMBER_INT);
        $autoCrossPostRelations = get_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), []);
        if (isset($autoCrossPostRelations[$postType]['relations'])) {
            $relations = $autoCrossPostRelations[$postType]['relations'];
            foreach ($relations as $key => $relation) {
                if ($relation['term_id'] == $termID && $relation['forum_id'] == $forumID) {
                    $autoCrossPostRelations[$postType]['relations'][$key]['enabled'] = $enable;
                    update_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), $autoCrossPostRelations);
                    break;
                }
            }
        }
        exit();
    }

    public function autoCrossSync() {
        if (!current_user_can('manage_options')) {
            exit();
        }
        $boardid = isset($_POST['board_id']) ? (int) $_POST['board_id'] : 0;
        $board = WPF()->board->get_board(intval($boardid));
        if (!$board) {
            $row = '<div class="wpfcp-error"><span class="dashicons dashicons-warning" style="line-height: inherit; font-size: 18px; color: #d06363;"></span> ' . __('Board not found.',
                            'wpforo_cross') . '</div>';
            wp_die($row);
        }
        WPF()->change_board($boardid);
        $suffix = $boardid ? $boardid . '_' : '';
        $postType = filter_input(INPUT_POST, "post_type", FILTER_SANITIZE_STRING);
        $termID = filter_input(INPUT_POST, "term_id", FILTER_SANITIZE_NUMBER_INT);
        $taxanomy = filter_input(INPUT_POST, "taxanomy", FILTER_SANITIZE_STRING);
        $forumID = filter_input(INPUT_POST, "forum_id", FILTER_SANITIZE_NUMBER_INT);
        $postQueryArgs = [
            'post_type' => $postType,
            'post_status' => 'publish',
            'fields' => 'ids',
            'numberposts' => - 1,
            'orderby' => 'ID',
            'order' => 'ASC',
            'posts_per_page' => - 1,
        ];

        if ($termID && $taxanomy) {
            $postQueryArgs['tax_query'] = [
                [
                    'taxonomy' => $taxanomy,
                    'field' => 'term_id',
                    'terms' => $termID,
                    'include_children' => false,
                ],
            ];
        }
        $savedPostsOptionKey = '_wpf_' . $suffix . 'autocross_posts_' . $termID . '_' . $forumID;
        $savedPosts = get_option($savedPostsOptionKey);
        if ($savedPosts) {
            $response = wpFAutoCrossTool::autoSync($savedPosts, $termID, $forumID, $this->options);
            wp_send_json_success($response);
        } else {
            $query = new WP_Query($postQueryArgs);
            if ($query && !is_wp_error($query)) {
                $response = [];
                if ($query->posts) {
                    add_option($savedPostsOptionKey, $query->posts, '', 'no');
                    $response = wpFAutoCrossTool::autoSync($query->posts, $termID, $forumID, $this->options);
                } else {
                    $response['complete'] = 1;
                    $response['message'] = wpFAutoCrossTool::generateStatisticMessage([
                                'cross_posted' => 0,
                                'skipped' => 0,
                    ]);
                }
                wp_send_json_success($response);
            } else {
                wp_send_json_error($query->get_error_messages());
            }
        }
    }

    public function autoCrossRelationDelete() {
        if (!current_user_can('manage_options')) {
            exit();
        }
        $boardid = filter_input(INPUT_POST, "board_id", FILTER_SANITIZE_NUMBER_INT);
        $board = WPF()->board->get_board(intval($boardid));
        if (!$board) {
            $row = '<div class="wpfcp-error"><span class="dashicons dashicons-warning" style="line-height: inherit; font-size: 18px; color: #d06363;"></span> ' . __('Board not found.',
                            'wpforo_cross') . '</div>';
            wp_die($row);
        }
        WPF()->change_board($boardid);
        $postType = filter_input(INPUT_POST, "post_type", FILTER_SANITIZE_STRING);
        $termID = filter_input(INPUT_POST, "term_id", FILTER_SANITIZE_NUMBER_INT);
        $forumID = filter_input(INPUT_POST, "forum_id", FILTER_SANITIZE_NUMBER_INT);
        $autoCrossPostRelations = get_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), []);
        if (isset($autoCrossPostRelations[$postType]['relations'])) {
            $relations = $autoCrossPostRelations[$postType]['relations'];
            foreach ($relations as $key => $relation) {
                if ($relation['term_id'] == $termID && $relation['forum_id'] == $forumID) {
                    unset($autoCrossPostRelations[$postType]['relations'][$key]);
                    update_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), $autoCrossPostRelations);
                    break;
                }
            }
        }
        exit();
    }

    private function removeAccessFilters() {
        add_filter("wpforo_permissions_forum_can", "__return_true");
        remove_filter('wpforo_add_topic_data_filter', [WPF()->moderation, 'auto_moderate']);
    }

    public function topicCanonicalUrl($meta_tags) {
        if (!empty(WPF()->current_object['topic']['topicid'])) {
            $crossObjectID = $this->crossObjectID(WPF()->current_object['topic']['topicid'], 'topic');
            if ($crossObjectID) {
                $crossURL = get_permalink($crossObjectID);
                $meta_tags = preg_replace('@<link rel="canonical" href="([^"]+)" />@is',
                        '<link rel="canonical" href="' . $crossURL . '" />',
                        $meta_tags);
            }
        }

        return $meta_tags;
    }

}

add_action('wpforo_core_inited', function() {
    if (version_compare(WPFORO_VERSION,
                    WPFOROCROSSPOST_WPFORO_REQUIRED_VERSION,
                    '>=') && wpforo_is_module_enabled(WPFOROCROSSPOST_FOLDER)) {
        $GLOBALS['wpForoCrossPosting'] = new wpForoCrossPosting();
    }
});
