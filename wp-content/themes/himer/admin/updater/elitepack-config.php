<?php
/* ElitePack Theme Updater */

/* Access the object */
function himer_updater() {
	global $himer_updater;
	if (!isset($himer_updater)) {
		// Include ElitePack SDK.
		include(dirname(__FILE__).'/ep-updater-admin-class.php');

		// Loads the updater classes
		$himer_updater = new ElitePack_Theme_Updater_Admin (
			// Config settings
			$config = array(
				'api_url'      => 'https://2code.info/support/',
				'item_id'      => '35460294',
				'name'         => himer_theme_name.' - Social Questions and Answers WordPress Theme',
				'version'      => himer_theme_version,
				'capability'   => 'manage_options',
				'notice_pages' => false,
				'redirect_url' => add_query_arg(array('page' => 'registration'),admin_url('admin.php')),
				'theme_folder' => 'himer',
			),
			// Strings
			$strings = array(
				'purchase-license'            => esc_html__('Purchase a License','himer'),
				'renew-support'               => esc_html__('Renew Support','himer'),
				'need-help'                   => esc_html__('Need Help?','himer'),
				'try-again'                   => esc_html__('Try Again','himer'),
				'register-item'               => esc_html__('Register %s','himer'),
				'register-message'            => esc_html__('Thank you for choosing %s! Your product must be registered to see the theme options, import our awesome demos, install bundled plugins, receive automatic updates, and access to support.','himer'),
				'register-button'             => esc_html__('Register Now!','himer'),
				'register-success-title'      => esc_html__('Congratulations','himer'),
				'register-success-text'       => esc_html__('Your License is now registered!, theme options, demo import, and bundeled plugins is now unlocked.','himer'),
				'api-error'                   => esc_html__('An error occurred, please try again.','himer'),
				'inline-register-item-notice' => esc_html__('Register the theme to unlock automatic updates.','himer'),
				'inline-renew-support-notice' => esc_html__('Renew your support to unlock automatic updates.','himer'),
				'date-at-time'                => esc_html__('%1$s at %2$s','himer'),
				'support-expiring-notice'     => esc_html__('Your support will Expire in %s. Renew your license today and save 25%% to keep getting auto updates and premium support, remember purchasing a new license extends support for all licenses.','himer'),
				'support-update-failed'       => esc_html__('Failed, try again later.','himer'),
				'support-not-updated'         => esc_html__('Did not updated, Your support expires on %s','himer'),
				'support-updated'             => esc_html__('Updated successfully, your support expires on %s','himer'),
				'update-available'            => esc_html__('There is a new version of %1$s available.','himer'),
				'update-available-changelog'  => esc_html__('There is a new version of %1$s available, %2$sView version %3$s details%4$s.','himer'),
				'update-now'                  => esc_html__('update now','himer'),
				'revoke-license-success'      => esc_html__('License Deactivated Successfully.','himer'),
				'cancel'                      => esc_html__('Cancel','himer'),
				'skip'                        => esc_html__('Skip & Switch','himer'),
				'send'                        => esc_html__('Send & Switch','himer'),
				'feedback'                    => esc_html__('%s feedback','himer'),
				'deactivation-share-reason'   => esc_html__('May we have a little info about why you are switching?','himer'),
			)
		);
	}
	return $himer_updater;
}
himer_updater();
/* Disable the notices in the Registeration page */
add_filter('himer/notice/show','himer_updater_notices',10,2);
function himer_updater_notices($status,$id) {
	if (get_current_screen()->id == 'theme-options_page_options') {
		if ($id == 'himer-activated' || $id == 'himer-activation-notice') {
			return false;
		}
	}
	return $status;
}
/* Add the Registeration custom page */
add_action('admin_menu','himer_registeration_menu',14);
function himer_registeration_menu() {
	$support_activate = himer_updater()->is_active();
	if ($support_activate) {
		add_submenu_page('options','Registration','Registration','manage_options','registration','himer_registeration_page');
	}else {
		add_menu_page('Register '.himer_theme_name,'Register '.himer_theme_name,'manage_options','registration','himer_registeration_page','dashicons-admin-site');
	}
}
/* The registeration page content */
function himer_registeration_page() {
	$support_activate = himer_updater()->is_active();
	if ($support_activate) {
		$intro = esc_html__('Thank you for choosing '.himer_theme_name.'! Your product is already registered, so you have access to:','himer');
		$title = esc_html__('Congratulations! Your product is registered now.','himer');
		$icon  = 'yes';
		$class = 'is-registered';
	}else {
		$intro = esc_html__('Thank you for choosing '.himer_theme_name.'! Your product must be registered to:','himer');
		$title = esc_html__('Click on the button below to begin the registration process.','himer');
		$icon  = 'no';
		$class = 'not-registered';
	}
	$foreach = array(
		"admin-site"       => esc_html__('See the theme options','himer'),
		"admin-appearance" => esc_html__('Import our awesome demos','himer'),
		"admin-plugins"    => esc_html__('Install the included plugins','himer'),
		"update"           => esc_html__('Receive automatic updates','himer'),
		"businessman"      => esc_html__('Access to support','himer')
	);?>
	<div id="framework-registration-wrap" class="framework-demos-container <?php echo esc_attr($class)?>">
		<div class="framework-dash-container framework-dash-container-medium">
			<div class="postbox">
				<h2><span><?php echo esc_html__('Welcome to','himer').' '.himer_theme_name.'!';?></span></h2>
				<div class="inside">
					<div class="main">
						<h3 class="framework-dash-margin-remove"><span class="dashicons dashicons-<?php echo esc_attr($icon);?> library-icon-key"></span> <?php echo stripcslashes($title);?></h3>
						<p class="framework-dash-text-lead"><?php echo stripcslashes($intro);?></p>
						<ul>
							<?php foreach ($foreach as $icon => $item) {
								if ($icon == "admin-site") {
									$link = admin_url('admin.php?page=options');
								}else if ($icon == "admin-appearance") {
									$link = admin_url('admin.php?page=demo-import');
								}else if ($icon == "businessman") {
									$link = 'https://2code.info/support/';
								}else {
									$link = "";
								}?>
								<li><i class="dashicons dashicons-<?php echo esc_attr($icon)?>"></i><?php echo ("" != $link?"<a target='_blank' href='".$link."'>":"").($item).($link != ""?"</a>":"")?></li>
							<?php }?>
						</ul>
					</div>
				</div>
				<div class="community-events-footer">
					<?php if (!$support_activate) {?>
						<div class="framework-registration-wrap">
							<a href="<?php echo himer_updater()->activate_link();?>" class="button button-primary"><?php esc_html_e('Register Now!','himer');?></a>
							<a href="<?php echo himer_updater()->purchase_url();?>" class="button" target="_blank"><?php esc_html_e('Purchase a License','himer');?></a>
						</div>
					<?php }else {?>
						<div class="framework-support-status framework-support-status-active">
							<?php esc_html_e('License Status:','himer');?> <span><?php esc_html_e('Active','himer')?></span>
							<a class="button" href="<?php echo himer_updater()->deactivate_license_link()?>"><?php esc_html_e('Revoke License','himer')?></a>
						</div>
						<?php $support_info = himer_updater()->support_period_info();
						if (!empty($support_info['status'])) {
							switch ($support_info['status']) {
								case 'expiring':
									$support_message = sprintf(esc_html__('Expiring! will expire on %s','himer'),$support_info['date']);
									break;
								case 'active':
									$support_message = esc_html__('Active','himer');
									break;
								default:
									$support_message = esc_html__('Expired','himer');
									break;
							}
						}
						if (!empty($support_message)) {?>
							<div class="framework-support-status framework-support-status-<?php echo stripcslashes($support_info['status'])?>">
								<?php esc_html_e('Support Status:','himer');?> <span><?php echo stripcslashes($support_message)?></span>
								<a class="button" href="<?php echo himer_updater()->refresh_support_expiration_link()?>"><?php esc_html_e('Refresh Expiration Date','himer')?></a>
							</div>
						<?php }
					}?>
				</div>
			</div>
		</div><!-- framework-dash-container -->
	</div><!-- framework-demos-container -->
	<?php
}?>