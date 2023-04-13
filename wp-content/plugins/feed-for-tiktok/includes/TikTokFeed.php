<?php

namespace TikTokFeed\Includes;

use TikTokFeed\AdminView\TikTokFeedAdmin;
use TikTokFeed\PublicView\TikTokFeedPublic;

class TikTokFeed
{
	protected $loader;

	protected $i18n;

	protected $adminView;

	protected $publicView;

	protected $pluginName;

	protected $version;

	public function __construct()
	{
		if (defined('TIK_TOK_FEED_VERSION')) {
			$this->version = TIK_TOK_FEED_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->pluginName = 'tik-tok-feed';

		$this->loadDependencies();
		$this->setLocale();
		$this->defineAdminHooks();
		$this->definePublicHooks();
	}

	private function loadDependencies()
	{
        $this->i18n = new TikTokFeedI18n();

        $this->adminView = new TikTokFeedAdmin($this->pluginName, $this->version);

        $this->publicView = new TikTokFeedPublic($this->pluginName, $this->version);

		$this->loader = new TikTokFeedLoader();

	}

	private function setLocale()
	{
		$plugin_i18n = new TikTokFeedI18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function defineAdminHooks()
	{
		$this->loader->add_action('admin_enqueue_scripts', $this->adminView, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $this->adminView, 'enqueue_scripts');
        $this->loader->add_action('carbon_fields_register_fields', $this->adminView, 'createAdminPage');
		$this->loader->add_action('after_setup_theme', $this->adminView, 'carbonFieldsFrameworkLoad');
//		$this->loader->add_action('carbon_fields_theme_options_container_saved', $this->adminView, 'apiAuthenticator');
		$this->loader->add_action('wp_ajax_api_authenticator', $this->adminView, 'apiAuthenticator');
	}

	private function definePublicHooks()
	{
		// Actions
		$this->loader->add_action('wp_enqueue_scripts', $this->publicView, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $this->publicView, 'enqueue_scripts');
		$this->loader->add_shortcode('tik-tok-feed', $this->publicView, 'feedShortcodeRenderHTML');
		$this->loader->add_shortcode('tik-tok-user-profile', $this->publicView, 'userProfileShortcodeRenderHTML');
		$this->loader->add_action('elementor/widgets/widgets_registered', $this->publicView, 'registerElementorWidgets');
		$this->loader->add_action('elementor/preview/enqueue_scripts', $this->publicView, 'elementorEnqueueStyles');
	}

	public function run()
	{
		$this->loader->run();
	}

	public function get_plugin_name()
	{
		return $this->pluginName;
	}

	public function get_loader()
	{
		return $this->loader;
	}

	public function get_version()
	{
		return $this->version;
	}
}
