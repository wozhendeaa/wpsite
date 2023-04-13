<form method="POST" >
    <?php
    if (function_exists('wp_nonce_field')) {
        wp_nonce_field($wpForoCPOptions::OPTION_NONCE);
    }
    ?>
    <table class="wpf-addon-table">
        <tr>
            <th scope="row" style="width:43%"><label><?php _e('Enable cross-posting for post types', 'wpforo_cross') ?></label></th>
            <td>
                <?php
                $blogPostTypes = get_post_types(array('public' => true));
                foreach ($blogPostTypes as $key => $value) {
                    if ($key == 'attachment') {
                        continue;
                    }
                    $checked = in_array($key, $wpForoCPOptions->postTypes) ? 'checked' : '';
                    echo '<label  for="wpforo_cp_post_types-' . $key . '">';
                    echo '<input type="checkbox" name="' . $wpForoCPOptions::OPTION_POST_TYPES . '[]" value="' . $value . '" id="wpforo_cp_post_types-' . $key . '"  ' . $checked . '>';
                    echo '<span>' . $value . '</span>';
                    echo '</label> &nbsp;&nbsp; ';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Blog Post to Forum Topic synchronization', 'wpforo_cross') ?></label>
            </th>
            <td>
                <label  for="wpforo_cp_post_crossing_edit">
                    <input <?php checked($wpForoCPOptions->postCrossingEdit == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_POST_CROSSING_EDIT; ?>" value="1" id="wpforo_cp_post_crossing_edit"> 
                    <span><?php _e('Edit', 'wpforo_cross'); ?></span>
                </label> &nbsp;&nbsp; 
                <label  for="wpforo_cp_post_crossing_delete">
                    <input <?php checked($wpForoCPOptions->postCrossingDelete == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_POST_CROSSING_DELETE; ?>" value="1" id="wpforo_cp_post_crossing_delete"> 
                    <span><?php _e('Delete', 'wpforo_cross'); ?></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Blog Comment to Forum Reply synchronization', 'wpforo_cross') ?></label>
            </th>
            <td>
                <label  for="wpforo_cross_comment_crossing_add">
                    <input <?php checked($wpForoCPOptions->commentCrossingAdd == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_COMMENT_ADD; ?>" value="1" id="wpforo_cross_comment_crossing_add"> 
                    <span><?php _e('Add', 'wpforo_cross'); ?></span>
                </label> &nbsp;&nbsp; 
                <label  for="wpforo_cross_comment_crossing_edit">
                    <input <?php checked($wpForoCPOptions->commentCrossingEdit == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_COMMENT_EDIT; ?>" value="1" id="wpforo_cross_comment_crossing_edit"> 
                    <span><?php _e('Edit', 'wpforo_cross'); ?></span>
                </label> &nbsp;&nbsp; 
                <label  for="wpforo_cross_comment_crossing_delete">
                    <input <?php checked($wpForoCPOptions->commentCrossingDelete == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_COMMENT_DELETE; ?>" value="1" id="wpforo_cp_comment_crossing_delete"> 
                    <span><?php _e('Delete', 'wpforo_cross'); ?></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Forum Reply to Blog Comment synchronization', 'wpforo_cross') ?></label>
            </th>
            <td>
                <label  for="wpforo_forum_post_crossing_add">
                    <input <?php checked($wpForoCPOptions->forumPostCrossingAdd == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_FORUM_POST_ADD; ?>" value="1" id="wpforo_forum_post_crossing_add"> 
                    <span><?php _e('Add', 'wpforo_cross'); ?></span>
                </label> &nbsp;&nbsp; 
                <label  for="wpforo_forum_post_crossing_edit">
                    <input <?php checked($wpForoCPOptions->forumPostCrossingEdite == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_FORUM_POST_EDIT; ?>" value="1" id="wpforo_forum_post_crossing_edit"> 
                    <span><?php _e('Edit', 'wpforo_cross'); ?></span>
                </label> &nbsp;&nbsp; 
                <label  for="wpforo_forum_post_crossing_delete">
                    <input <?php checked($wpForoCPOptions->forumPostCrossingDelete == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_FORUM_POST_DELETE; ?>" value="1" id="wpforo_forum_post_crossing_delete"> 
                    <span><?php _e('Delete', 'wpforo_cross'); ?></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="wpforo_show_topic_post_rel"><?php _e('Display Cross-posted Post Link on Forum Topics', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->showTopicPostRel == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_SHOW_TOPIC_POST_REL; ?>" value="1" id="wpforo_show_topic_post_rel"> 
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="wpforo_show_reply_comment_rel"><?php _e('Display Cross-posted Comment Link on Forum Replies', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->showReplyCommentRel == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_SHOW_REPLY_COMMENT_REL; ?>" value="1" id="wpforo_show_reply_comment_rel"> 
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="wpforo_show_post_topic_rel"><?php _e('Display Cross-posted Topic Link on Blog Post', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->showPostTopicRel == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_SHOW_POST_TOPIC_REL; ?>" value="1" id="wpforo_show_post_topic_rel"> 
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="wpforo_show_comment_reply_rel"><?php _e('Display Cross-posted Reply Link on Blog Comments', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->showCommentReplyRel == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_SHOW_COMMENT_REPLY_REL; ?>" value="1" id="wpforo_show_comment_reply_rel"> 
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Cross-posting Content', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->crossPostingContent == $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT_FULL); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT_FULL; ?>" id="wpforo_crosposting_content_full"><label for="wpforo_crosposting_content_full"><?php _e('Full Content', 'wpforo_cross') ?></label><br> 
                <input <?php checked($wpForoCPOptions->crossPostingContent == $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT_EXCERPT); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT_EXCERPT; ?>" id="wpforo_crosposting_content_excerpt"><label for="wpforo_crosposting_content_excerpt"><?php _e('Excerpt', 'wpforo_cross') ?></label><br>  
                <input <?php checked($wpForoCPOptions->crossPostingContent == $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT_CUSTOM); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_CONTENT_CUSTOM; ?>" id="wpforo_crosposting_content_custom"><label for="wpforo_crosposting_content_custom"><?php _e('Custom', 'wpforo_cross') ?></label>  
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FEATURED_IMAGE; ?>"><?php _e('Cross-posting Featured Image', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->crossPostFeaturedImage == 1); ?> type="checkbox" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FEATURED_IMAGE; ?>" value="1" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FEATURED_IMAGE; ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Cross-posting Featured Image Size', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->crossPostFImageSize == $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_THUMBNAIL); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_SIZE; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_THUMBNAIL; ?>" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_THUMBNAIL; ?>"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_THUMBNAIL; ?>"><?php _e('thumbnail', 'wpforo_cross');
                echo ' (' . intval(get_option("thumbnail_size_w")) . 'x' . intval(get_option("thumbnail_size_h")) . ')'; ?></label><br>
                <input <?php checked($wpForoCPOptions->crossPostFImageSize == $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MEDIUM); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_SIZE; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MEDIUM; ?>" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MEDIUM; ?>"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MEDIUM; ?>"><?php _e('medium', 'wpforo_cross');
                echo ' (' . intval(get_option("medium_size_w")) . 'x' . intval(get_option("medium_size_h")) . ')'; ?></label><br>
                <input <?php checked($wpForoCPOptions->crossPostFImageSize == $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MLARGE); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_SIZE; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MLARGE; ?>" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MLARGE; ?>"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_MLARGE; ?>"><?php _e('medium_large', 'wpforo_cross');
                echo ' (' . intval(get_option("medium_large_size_w")) . 'x' . intval(get_option("medium_large_size_h")) . ')'; ?></label><br>
                <input <?php checked($wpForoCPOptions->crossPostFImageSize == $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_LARGE); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_SIZE; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_LARGE; ?>" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_LARGE; ?>"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_LARGE; ?>"><?php _e('large', 'wpforo_cross');
                echo ' (' . intval(get_option("large_size_w")) . 'x' . intval(get_option("large_size_h")) . ')'; ?></label><br>
                <input <?php checked($wpForoCPOptions->crossPostFImageSize == $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_FULL); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_SIZE; ?>" value="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_FULL; ?>" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_FULL; ?>"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_FIMAGE_FULL; ?>"><?php _e('full (original size)', 'wpforo_cross'); ?></label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Cross-posted Topic Canonical URL', 'wpforo_cross') ?></label>
            </th>
            <td>
                <input <?php checked($wpForoCPOptions->crossPostTopicCanonicalUrl === "blog_post"); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL; ?>" value="blog_post" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL; ?>-blog_post"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL; ?>-blog_post"><?php _e('Blog Post', 'wpforo_cross'); ?><br />
                <input <?php checked($wpForoCPOptions->crossPostTopicCanonicalUrl === "topic"); ?> type="radio" name="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL; ?>" value="topic" id="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL; ?>-topic"> <label for="<?php echo $wpForoCPOptions::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL; ?>-topic"><?php _e('Topic', 'wpforo_cross'); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right;">
                <input name="<?php echo $wpForoCPOptions::OPTION_SUBMIT; ?>" type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo_cross') ?>">
            </td>
        </tr>
    </table>
</form>