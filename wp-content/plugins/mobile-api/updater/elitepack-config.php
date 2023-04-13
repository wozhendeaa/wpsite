<?php
/* ElitePack Theme Updater */

/* Access the object */
function mobile_api_updater() {
	global $mobile_api_updater;
	if (!isset($mobile_api_updater)) {
		// Include ElitePack SDK.
		include(dirname(__FILE__).'/ep-updater-admin-class.php');

		// Loads the updater classes
		$mobile_api_updater = new ElitePack_Theme_Updater_Admin (
			// Config settings
			$config = array(
				'api_url'      => 'https://2code.info/support/',
				'item_id'      => '35644640',
				'name'         => 'WPQA APIs Addon For The WordPress themes',
				'version'      => mobile_api_version,
				'capability'   => 'manage_options',
				'notice_pages' => false,
				'redirect_url' => add_query_arg(array('page' => 'registration-apps'),admin_url('admin.php')),
				'is_plugin'    => true,
				'plugin_file'  => 'mobile-api/mobile.php',
			),
			// Strings
			$strings = array(
				'purchase-license'            => esc_html__('Purchase a License','mobile-api'),
				'renew-support'               => esc_html__('Renew Support','mobile-api'),
				'need-help'                   => esc_html__('Need Help?','mobile-api'),
				'try-again'                   => esc_html__('Try Again','mobile-api'),
				'register-item'               => esc_html__('Register %s','mobile-api'),
				'register-message'            => esc_html__('Thank you for choosing %s! Your product must be registered to see the theme options, import our awesome demos, install bundled plugins, receive automatic updates, and access to support.','mobile-api'),
				'register-button'             => esc_html__('Register Now!','mobile-api'),
				'register-success-title'      => esc_html__('Congratulations','mobile-api'),
				'register-success-text'       => esc_html__('Your License is now registered!, theme options, demo import, and bundeled plugins is now unlocked.','mobile-api'),
				'api-error'                   => esc_html__('An error occurred, please try again.','mobile-api'),
				'inline-register-item-notice' => esc_html__('Register the theme to unlock automatic updates.','mobile-api'),
				'inline-renew-support-notice' => esc_html__('Renew your support to unlock automatic updates.','mobile-api'),
				'date-at-time'                => esc_html__('%1$s at %2$s','mobile-api'),
				'support-expiring-notice'     => esc_html__('Your support will Expire in %s. Renew your license today and save 25%% to keep getting auto updates and premium support, remember purchasing a new license extends support for all licenses.','mobile-api'),
				'support-update-failed'       => esc_html__('Failed, try again later.','mobile-api'),
				'support-not-updated'         => esc_html__('Did not updated, Your support expires on %s','mobile-api'),
				'support-updated'             => esc_html__('Updated successfully, your support expires on %s','mobile-api'),
				'update-available'            => esc_html__('There is a new version of %1$s available.','mobile-api'),
				'update-available-changelog'  => esc_html__('There is a new version of %1$s available, %2$sView version %3$s details%4$s.','mobile-api'),
				'update-now'                  => esc_html__('update now','mobile-api'),
				'revoke-license-success'      => esc_html__('License Deactivated Successfully.','mobile-api'),
				'cancel'                      => esc_html__('Cancel','mobile-api'),
				'skip'                        => esc_html__('Skip & Switch','mobile-api'),
				'send'                        => esc_html__('Send & Switch','mobile-api'),
				'feedback'                    => esc_html__('%s feedback','mobile-api'),
				'deactivation-share-reason'   => esc_html__('May we have a little info about why you are switching?','mobile-api'),
			)
		);
	}
	return $mobile_api_updater;
}
mobile_api_updater();
/* Disable the notices in the Registeration page */
add_filter('mobile/notice/show','mobile_api_updater_notices',10,2);
function mobile_api_updater_notices($status,$id) {
	if (get_current_screen()->id == 'theme-options_page_options') {
		if ($id == 'mobile-activated' || $id == 'mobile-activation-notice') {
			return true;
		}
	}
	return $status;
}
/* Add the Registeration custom page */
add_action('admin_menu','mobile_registeration_menu',15);
function mobile_registeration_menu() {
	if (function_exists("mobile_api_updater")) {
		$support_activate = mobile_api_updater()->is_active();
		if (true) {
			add_submenu_page('options','Registration APPs','Registration APPs','manage_options','registration-apps','mobile_registeration_page');
		}else {
			add_menu_page('Register APPs','Register APPs','manage_options','registration-apps','mobile_registeration_page','dashicons-admin-site');
		}
	}
}
/* The registeration page content */
function mobile_registeration_page() {
	$support_activate = mobile_api_updater()->is_active();
	if (true) {
		$intro = esc_html__('Thank you for choosing Mobile APP! Your product is already registered, so you have access to:','mobile-api');
		$title = esc_html__('Congratulations! Your product is registered now.','mobile-api');
		$icon  = 'yes';
		$class = 'is-registered';
	}else {
		$intro = esc_html__('Thank you for choosing Mobile APP! Your product must be registered to:','mobile-api');
		$title = esc_html__('Click on the button below to begin the registration process.','mobile-api');
		$icon  = 'no';
		$class = 'not-registered';
	}
	$foreach = array(
		"admin-site"       => esc_html__('Request your APP','mobile-api'),
		"admin-plugins"    => esc_html__('See the mobile APP options','mobile-api'),
		"update"           => esc_html__('Receive automatic updates','mobile-api'),
		"businessman"      => esc_html__('Access to support','mobile-api')
	);?>
	<div id="framework-registration-wrap" class="framework-demos-container <?php echo esc_attr($class)?>">
		<div class="framework-dash-container framework-dash-container-medium">
			<div class="postbox">
				<h2><span><?php echo esc_html__('Welcome to','mobile-api').' Mobile APP!';?></span></h2>
				<div class="inside">
					<div class="main">
						<h3 class="framework-dash-margin-remove"><span class="dashicons dashicons-<?php echo esc_attr($icon);?> library-icon-key"></span> <?php echo ($title);?></h3>
						<p class="framework-dash-text-lead"><?php echo ($intro);?></p>
						<ul>
							<?php foreach ($foreach as $icon => $item) {
								if ($icon == "admin-site") {
									$link = "https://2code.info/support/mobile-app/";
								}else if ($icon == "admin-plugins") {
									$link = admin_url('admin.php?page=options');
								}else if ($icon == "admin-appearance") {
									$link = admin_url('admin.php?page=demo-import');
								}else if ($icon == "businessman") {
									$link = 'https://2code.info/support/';
								}else {
									$link = "";
								}?>
								<li><i class="dashicons dashicons-<?php echo esc_attr($icon)?>"></i><?php echo ($link != ""?"<a target='_blank' href='".$link."'>":"").($item).($link != ""?"</a>":"")?></li>
							<?php }?>
						</ul>
					</div>
				</div>
				<div class="community-events-footer">
					<?php if (!$support_activate) {?>
						<div class="framework-registration-wrap">
							<a href="<?php echo mobile_api_updater()->activate_link();?>" class="button button-primary"><?php esc_html_e('Register Now!','mobile-api');?></a>
							<a href="<?php echo mobile_api_updater()->purchase_url();?>" class="button" target="_blank"><?php esc_html_e('Purchase a License','mobile-api');?></a>
						</div>
					<?php }else {?>
						<div class="framework-support-status framework-support-status-active">
							<?php esc_html_e('License Status:','mobile-api');?> <span><?php esc_html_e('Active','mobile-api')?></span>
							<a class="button" href="<?php echo mobile_api_updater()->deactivate_license_link()?>"><?php esc_html_e('Revoke License','mobile-api')?></a>
						</div>
						<?php $support_info = mobile_api_updater()->support_period_info();
						if (!empty($support_info['status'])) {
							switch ($support_info['status']) {
								case 'expiring':
									$support_message = sprintf(esc_html__('Expiring! will expire on %s','mobile-api'),$support_info['date']);
									break;
								case 'active':
									$support_message = esc_html__('Active','mobile-api');
									break;
								default:
									$support_message = esc_html__('Expired','mobile-api');
									break;
							}
						}
						if (!empty($support_message)) {?>
							<div class="framework-support-status framework-support-status-<?php echo ($support_info['status'])?>">
								<?php esc_html_e('Support Status:','mobile-api');?> <span><?php echo ($support_message)?></span>
								<a class="button" href="<?php echo mobile_api_updater()->refresh_support_expiration_link()?>"><?php esc_html_e('Refresh Expiration Date','mobile-api')?></a>
							</div>
						<?php }
					}?>
				</div>
			</div>
		</div><!-- framework-dash-container -->
	</div><!-- framework-demos-container -->
	<?php
}?>