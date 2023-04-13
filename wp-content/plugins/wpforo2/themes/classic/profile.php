<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

$user = wpforo_get_current_object_user();
?>

<div class="wpforo-profile-wrap">
	<?php if( $user && ( WPF()->current_userid == $user['userid'] || WPF()->usergroup->can( 'vprf' ) ) ) :
		$rating_enabled = wpforo_setting( 'rating', 'rating' ) && in_array( $user['groupid'], wpforo_setting( 'rating', 'rating_badge_ug' ), true );
		$secondary_group_names = ( $user['secondary_groupids'] ) ? WPF()->usergroup->get_secondary_group_names( $user['secondary_groupids'] ) : [];
		?>
        <div class="wpforo-profile-head-wrap">
			<?php $avatar_image_html = wpforo_user_avatar( $user, 150, '', true );
			$avatar_image_url        = wpforo_avatar_url( $avatar_image_html );
			$bg                      = ( $avatar_image_url ) ? "background-image:url('" . esc_url( $avatar_image_url ) . "');" : ''; ?>
            <div class="wpforo-profile-head-bg" style="<?php echo $bg ?>">
                <div class="wpfx"></div>
            </div>
            <div id="m_" class="wpforo-profile-head">
                <div class="h-header">
                    <div class="wpfy" <?php if( ! $rating_enabled ) echo ' style="height:140px;" ' ?>></div>
                    <div class="wpf-profile-info-wrap">
                        <div class="h-picture">
							<?php if( WPF()->usergroup->can( 'va' ) && wpforo_setting( 'profiles', 'avatars' ) ): ?>
                                <div class="wpf-profile-img-wrap">
									<?php echo $avatar_image_html; ?>
                                </div>
							<?php endif; ?>
                            <div class="wpf-profile-data-wrap">
                                <div class="profile-display-name">
									<?php WPF()->member->show_online_indicator( $user['userid'] ) ?>
									<?php wpforo_user_dname( $user, true ) ?>
                                    <div class="profile-stat-data-item"><?php wpforo_phrase( 'Group' ) ?>: <?php wpforo_phrase( $user['group_name'] ) ?></div>
									<?php if( ! empty( $secondary_group_names ) ): ?>
                                        <div class="profile-stat-data-item"><?php wpforo_phrase( 'Secondary Groups' ) ?>: <?php echo implode( ', ', $secondary_group_names ); ?></div>
									<?php endif; ?>
	                                <?php if( WPF()->current_object['user_is_same_current_user'] || WPF()->usergroup->can( 'vmrd' ) ) : ?>
                                        <div class="profile-stat-data-item"><?php wpforo_phrase( 'Joined' ) ?>: <?php wpforo_date( $user['user_registered'], 'date' ) ?></div>
	                                <?php endif ?>
                                    <div class="profile-stat-data-item"><?php wpforo_member_title( $user, true, wpforo_phrase( 'Title', false ) . ': ' ); ?></div>
                                </div>
                            </div>
                            <div class="wpf-cl"></div>
                        </div>

                        <div class="h-header-info">
                            <div class="h-top">
                                <div class="profile-stat-data">
									<?php do_action( 'wpforo_profile_data_item', WPF()->current_object ) ?>
									<?php if( $rating_enabled ): ?>
                                        <div class="profile-rating-bar">
                                            <div class="profile-rating-bar-wrap" title="<?php wpforo_phrase( 'Member Rating' ) ?>">
												<?php $levels = WPF()->member->levels(); ?>
												<?php for( $a = 1; $a <= $user['rating']['level']; $a ++ ): ?>
                                                    <div class="rating-bar-cell" style="background-color:<?php echo esc_attr( $user['rating']['color'] ); ?>;">
                                                        <i class="<?php echo WPF()->member->rating( $a, 'icon' ) ?>"></i>
                                                    </div>
												<?php endfor; ?>
												<?php for( $i = ( $user['rating']['level'] + 1 ); $i <= ( count( $levels ) - 1 ); $i ++ ): ?>
                                                    <div class="wpfbg-7 rating-bar-cell">
                                                        <i class="<?php echo WPF()->member->rating( $i, 'icon' ) ?>"></i>
                                                    </div>
												<?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="wpf-profile-badge" title="<?php wpforo_phrase( 'Rating Badge' ) ?>" style="background-color:<?php echo esc_attr( $user['rating']['color'] ); ?>;">
											<?php echo WPF()->member->rating_badge( $user['rating']['level'] ); ?>
                                        </div>
									<?php endif; ?>
									<?php do_action( 'wpforo_after_member_badge', $user ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="wpf-clear"></div>
                    </div>
                </div>

                <div class="wpf-profile-panel">
                    <?php wpforo_template_profile_action_buttons( $user ) ?>
                </div>

                <div class="h-footer">
                    <div class="h-bottom">
						<?php wpforo_member_tabs() ?>
                        <div class="wpf-clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpforo-profile-content">
			<?php wpforo_member_template() ?>
        </div>
	<?php elseif( $user ) : ?>
        <div class="wpforo-profile-content wpfbg-7">
            <div class="wpfbg-7 wpf-page-message-wrap">
                <div class="wpf-page-message-text">
					<?php wpforo_phrase( 'You do not have permission to view this page' ) ?>
                </div>
            </div>
        </div>
	<?php else : ?>
        <div class="wpforo-profile-content wpfbg-7">
            <div class="wpfbg-7 wpf-page-message-wrap">
                <div class="wpf-page-message-text">
					<?php WPF()->tpl->member_error() ?>
                </div>
            </div>
        </div>
	<?php endif; ?>
</div>
