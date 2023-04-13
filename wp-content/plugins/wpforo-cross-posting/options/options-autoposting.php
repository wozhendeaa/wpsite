<div class="wpf-autocross-panel">
    <div style="font-size: 14px;line-height: 20px;font-style: italic;">
        <ul style="list-style: disc; margin-left: 20px;">
            <li style="border-bottom: 1px #ccc dashed; padding-bottom: 10px;"><?php _e('You can create an auto cross-posting rule below. Choose the Post Category and the target Forum to enable automatic post to topic cross-posting. Once the auto cross-posting rule is created and enabled, all future posts (not old) in selected category will be cross-posted as topics in according forums. These rules work for manually created posts as well as for auto-generated posts.', 'wpforo_cross') ?></li>
            <li style="border-bottom: 1px #ccc dashed; padding-bottom: 10px;"><?php _e('If you want to cross-post old posts just use the [Synchronize] button. It\'ll find all non-cross-posted posts of selected category and cross-post to according forum as topics.', 'wpforo_cross') ?></li>
            <li style="border-bottom: 1px #ccc dashed; padding-bottom: 10px;"><?php _e('The auto cross-posting and the synchronization processes are also include post comments to topic replies cross-posting.', 'wpforo_cross') ?></li>
        </ul>
    </div>
    <?php $boardid = WPF()->board->get_current('boardid');?>
    <span id="wpf-cross-board-id" data-boardid="<?php echo $boardid;?>"></span>
    <?php
    $autoCrossRelations = get_option(wpforo_prefix(wpForoCrossPostingOptions::OPTION_AUTO_CROSPOSTING_REL), array());
    foreach ($wpForoCPOptions->postTypes as $postType) {
        ?>
        <h4 class="wpf-addon-header"><?php echo __('Content Type: ', 'wpforo_cross') . $postType ?></h4>
        <div id="wpf-autocross-<?php echo $postType ?>" class="wpf-cross-post-block">
            <?php
            $taxonomies = array();
            $autoCrosspostingSymbol = '&nbsp; > &nbsp';
            if ($postType == 'post') {
                $taxonomies = array('category');
            } else {
                $allTaxonomies = get_object_taxonomies($postType, 'object');
                foreach ($allTaxonomies as $obj) {
                    if ($obj->public == 1) {
                        $taxonomies[] = $obj->name;
                    }
                }
            }
            if (isset($autoCrossRelations[$postType]['relations'])) {
                $relations = $autoCrossRelations[$postType]['relations'];
                foreach ($relations as $relation) {
                    echo wpFAutoCrossTool::addAutoCrossRow($relation['term_id'], $relation['forum_id'], $postType, $relation['enabled']);
                }
            }
            if (!$taxonomies) {
                ?>
                <div id="wpf-autocross-add-<?php echo $postType ?>" class="wpf-autopost-add-block">
                    <span><?php echo $postType ?></span>  <?php echo $autoCrosspostingSymbol; ?>
                    <span><?php _e('Forums', 'wpforo_cross'); ?> :</span>
                    <select class="wpforo-furums">
                        <?php WPF()->forum->tree('select_box', FALSE); ?>
                    </select>
                    <button class="wpf-autocross-add button-primary" data-posttype="<?php echo $postType ?>" data-hasterm="0"><?php _e('Add Cross-Posting Rule'); ?></button>
                </div>
                <?php
            } else {
                $terms = get_terms(array(
                    'taxonomy' => $taxonomies,
                    'orderby' => 'id',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'hierarchical' => false,)
                );
                if ($terms && !is_wp_error($terms)) {
                    ?>
                    <div id="wpf-autocross-add-<?php echo $postType; ?>" class="wpf-autopost-add-block">
                        <?php ?>
                        <span><?php _e('Blog Category / Term', 'wpforo_cross'); ?> :</span>
                        <select class="blog-terms">
                            <?php
                            foreach ($terms as $term) {
                                ?>
                                <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <?php echo $autoCrosspostingSymbol; ?> <span><?php _e('Forum', 'wpforo_cross'); ?> :</span>
                        <select  class="wpforo-furums">
                            <?php WPF()->forum->tree('select_box', FALSE); ?>
                        </select>
                        <button class="wpf-autocross-add button-primary" data-posttype="<?php echo $postType ?>" data-hasterm="1"><?php _e('Add Cross-Posting Rule'); ?></button>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>
