<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

$members = WPF()->current_object['members'];
?>
<h2 id="wpforo-title"><?php wpforo_phrase( 'Forum Members' ) ?></h2>
<div class="wpforo-members-wrap">
	<?php if( WPF()->usergroup->can( 'vmem' ) ): ?>
        <div class="wpf-table wpforo-members-search wpfbg-9"><?php wpforo_member_search_form() ?></div>
        <p>&nbsp;</p>
        <div class="wpforo-members-content wpfbg-7">
            <table style="width:100%; display:table;">
                <tr class="wpfbg-3">
					<?php if( WPF()->usergroup->can( 'va' ) && wpforo_setting( 'profiles', 'avatars' ) ): ?>
                        <th class="wpf-members-avatar"><?php wpforo_phrase( 'Avatar' ) ?></th>
					<?php endif; ?>
                    <th class="wpf-members-info1"><?php wpforo_phrase( 'Member information' ) ?></th>
                    <th class="wpf-members-regdate"><?php wpforo_phrase( 'Registered date' ) ?></th>
                </tr>
				<?php if( ! empty( $members ) ) : ?>
					<?php $bg = false;
					foreach( $members as $member ) : ?>
                        <tr<?php echo( $bg ? ' style="background:#F7F7F7"' : '' ) ?>>
							<?php if( WPF()->usergroup->can( 'va' ) && wpforo_setting( 'profiles', 'avatars' ) ): ?>
                                <td class="wpf-members-avatar"><?php echo wpforo_user_avatar( $member ); ?></td>
							<?php endif; ?>
                            <td class="wpf-members-info">
                                <span style="font-size:16px;"><?php WPF()->member->show_online_indicator( $member['userid'] ) ?>&nbsp;</span>
								<?php wpforo_member_link( $member, '', 50, ' wpf-member-name ' ); ?> <?php wpforo_member_nicename( $member, '@' ); ?>
								<?php do_action( 'wpforo_after_member_badge', $member ) ?><br>
								<?php $enabled_for_usergroup = in_array( $member['groupid'], wpforo_setting( 'rating', 'rating_badge_ug' ), true ); ?>
                                <span class="wpf-member-info wpfcl-1"> <i class="fas fa-users" title="<?php wpforo_phrase( 'Usergroup' ) ?>"></i>&nbsp; <?php wpforo_phrase( $member['group_name'] ) ?> | <?php if( wpforo_setting( 'rating', 'rating' ) && $enabled_for_usergroup ): ?><i class="fas fa-star" title="<?php wpforo_phrase( 'Rating' ) ?>"></i>&nbsp;<?php echo $member['rating']['level'] ?>/10  |<?php endif; ?> <?php wpforo_phrase( 'Posts' ) ?>: <?php echo intval( $member['posts'] ) ?></span> |
                                <div class="wpf-member-profile-buttons" style="display:inline-block;">
									<?php wpforo_member_buttons( $member ) ?>
                                </div>
                            </td>
                            <td class="wpf-members-regdate wpfcl-1"><?php wpforo_date( $member['user_registered'], 'date' ) ?></td>
                        </tr>
						<?php $bg = ! $bg; endforeach; ?>
				<?php else : ?>
                    <tr>
                        <td colspan="3"><p class="wpf-p-error"> <?php wpforo_phrase( 'Members not found' ) ?> </p></td>
                    </tr>
				<?php endif ?>
            </table>
        </div>
        <div class="wpf-members-foot">
			<?php wpforo_template_pagenavi() ?>
        </div>
	<?php else : ?>
        <p class="wpf-p-error"> <?php wpforo_phrase( 'You do not have permission to view this page' ) ?> </p>
	<?php endif; ?>
</div>
