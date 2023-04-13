<?php

namespace QuadLayers\TTF\Utils;

/**
 * Helpers Class
 */
class Helpers {

	/**
	 * Function to get access token link
	 *
	 * @return string
	 */
	public static function get_access_token_link() {
		$redirect_url = QLTTF_ACCOUNT_URL;
		$url          = "https://tiktokfeed.quadlayers.com/auth/?redirect_url={$redirect_url}";
		return $url;
	}
}
