<?php
if( WPF()->perm->forum_can( 'vt' ) && WPF()->perm->forum_can( 'ent' ) ):

	if( ( $forum = WPF()->current_object['forum'] ) && ( $topic = WPF()->current_object['topic'] ) && ( $posts = WPF()->current_object['posts'] ) ) :
		if( ! ( $topic['private'] && ! wpforo_is_owner( $topic['userid'], $topic['email'] ) && ! WPF()->perm->forum_can( 'vp' ) ) ): ?>

            <div class="wpf-head-bar">
				<?php wpforo_single_title( 'post', $topic, '<h1 id="wpforo-title">', '</h1>' ); ?>
                <div class="wpf-action-link">
	                <?php do_action( 'wpforo_template_post_head_bar_action_links', $forum, $topic, $posts ); ?>
                </div>
            </div>

			<?php
			wpforo_template_pagenavi( 'wpf-navi-post-top' );
			if( $forum['layout'] == 3 ) include_once( wpftpl( 'layouts/3/comment.php' ) );
			include( wpftpl( 'layouts/' . $forum['layout'] . '/post.php' ) );
			wpforo_template_pagenavi( 'wpf-navi-post-bottom' );

			if( WPF()->perm->forum_can( 'cr' ) || ( wpforo_is_owner( $topic['userid'], $topic['email'] ) && WPF()->perm->forum_can( 'ocr' ) ) ) {
				WPF()->tpl->reply_form( $topic );
			} elseif( ! WPF()->current_userid ) {
				WPF()->tpl->please_login();
			}
			do_action( 'wpforo_post_list_footer' );

		else: ?>
            <p class="wpf-p-error">
				<?php wpforo_phrase( 'Topic are private, please register or login for further information' ) ?>
            </p>
		<?php endif;
	endif;

else : ?>
    <p class="wpf-p-error">
		<?php if( WPF()->current_userid ): ?>
			<?php echo apply_filters( 'wpforo_no_topic_access_message_for_users', wpforo_phrase( "Your user level does not have appropriate permission to view the content", false ) ); ?>
		<?php else: ?>
			<?php echo apply_filters( 'wpforo_no_topic_access_message_for_guests', wpforo_phrase( "You do not have permission to view the content", false ) . '<br>' . wpforo_get_login_or_register_notice_text() ); ?>
		<?php endif; ?>
    </p>
<?php endif;
