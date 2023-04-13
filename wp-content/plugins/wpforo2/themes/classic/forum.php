<?php
if( ! $forum = WPF()->current_object['forum'] ) : ?>
    <h1 id="wpforo-title">
		<?php echo esc_html( WPF()->board->get_current( 'settings' )['title'] ) ?>
        <div class="wpforo-feed">
            <span class="wpf-unread-posts">
                <a href="<?php echo esc_url( wpforo_home_url( wpforo_settings_get_slug( 'recent' ) . '?view=unread' ) ) ?>">
                    <i class="fas fa-layer-group" style="padding-right: 1px; font-size: 13px;"></i> <span><?php wpforo_phrase( 'Unread Posts' ) ?></span>
                </a>
            </span>
			<?php wpforo_feed_link( 'home' ) ?>
        </div>
    </h1>
<?php elseif( $forum['is_cat'] ) : ?>
    <div class="wpf-head-bar">
        <div class="wpf-head-bar-left">
            <h1 id="wpforo-title"><?php echo esc_html( $forum['title'] ) ?></h1>
			<?php if( $forum['description'] ): ?>
                <div id="wpforo-description"><?php echo $forum['description'] ?></div>
			<?php endif; ?>
            <div class="wpf-action-link">
	            <?php do_action( 'wpforo_template_forum_head_bar_action_links', $forum ); ?>
				<?php wpforo_feed_link( 'forum' ) ?>
            </div>
        </div>
        <div class="wpf-clear"></div>
    </div>
<?php endif;
if( $cats = WPF()->current_object['categories'] ) :
	$forum_template = 'forum.php';
	if( WPF()->current_object['template'] == 'topic' && WPF()->current_object['layout'] == 4 ) {
		$forum_template = 'forum-sub.php';
	}
	foreach( $cats as $key => $cat ) {
		if( WPF()->perm->forum_can( 'vf', $cat['forumid'] ) ) {
			$args = [ "parentid" => $cat['forumid'], "type" => 'forum' ];
			$forums = WPF()->forum->get_forums( $args );
			if( ! empty( $forums ) ) {
				$layout = ( $cat['layout'] ? intval( $cat['layout'] ) : 1 );
				do_action( 'wpforo_category_loop_start', $cat, $key );
				include( wpftpl( 'layouts/' . $layout . '/' . $forum_template ) );
				do_action( 'wpforo_category_loop_end', $cat, $key );
			}else{
				do_action( 'wpforo_category_loop_no_forums', $cat, $key );
            }
		}
	}
else : ?>
    <p class="wpf-p-error">
		<?php wpforo_phrase( 'No forums were found here.' ) ?>
    </p>
<?php endif;
