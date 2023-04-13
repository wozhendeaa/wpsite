<?php

namespace TikTokFeed\PublicView;

use TikTokFeed\AdminView\Business\TikTokFeedFacade as TikTokFeedAdminViewFacade;
use TikTokFeed\PublicView\Business\TikTokFeedFacade as TikTokFeedPublicViewFacade;

class TikTokFeedPublic
{
	private $pluginName;

	private $version;

	private $adminViewFacade;

	private $publicViewFacade;

	public function __construct($pluginName, $version)
	{
		$this->pluginName = $pluginName;
		$this->version = $version;
		$this->adminViewFacade = TikTokFeedAdminViewFacade::getInstance();
		$this->publicViewFacade = TikTokFeedPublicViewFacade::getInstance();
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->pluginName, PLUGIN_TIK_TOK_FEED_URL . 'public/dist/css/tik-tok-feed.css', [], $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_register_script($this->pluginName, PLUGIN_TIK_TOK_FEED_URL . 'public/dist/js/tik-tok-feed.js', ['jquery'], $this->version, true);
		wp_localize_script($this->pluginName, 'tik_tok_feed_public_object', [
            'feed_count' => carbon_get_theme_option('ttf_videos_count'),
        ]);
		wp_enqueue_script($this->pluginName);
	}

    public function feedShortcodeRenderHTML($atts, $content = '')
    {
        $atts = shortcode_atts([
            'username' => 'sgmro',
            'count' => '5',
        ], $atts);

        return $this->publicViewFacade->feedShortcodeRenderHTML($atts);
    }

    public function userProfileShortcodeRenderHTML($atts, $content = '')
    {
        $atts = shortcode_atts([
            'username' => 'sgmro',
        ], $atts);

        return $this->publicViewFacade->userProfileShortcodeRenderHTML($atts);
    }

    public function registerElementorWidgets()
    {
        $this->publicViewFacade->registerElementorWidgets();
    }

    public function elementorEnqueueStyles()
    {
        wp_enqueue_script('elementor-tik-tok-feed', PLUGIN_TIK_TOK_FEED_URL . 'public/dist/elementor/js/feed-widget.js', ['jquery'], false, true);
    }
}
