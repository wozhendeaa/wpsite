<?php

namespace TikTokFeed\Includes;

class TikTokFeedI18n
{
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'tik-tok-feed',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
