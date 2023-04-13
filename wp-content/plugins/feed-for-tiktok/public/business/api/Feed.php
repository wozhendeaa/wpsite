<?php

namespace TikTokFeed\PublicView\Business\Api;

use TikTokFeed\Includes\BaseApi;
use \Requests;
use TikTokFeed\Includes\TikTokFeedHelper;

class Feed extends BaseApi implements FeedInterface
{
    use TikTokFeedHelper;

    public function __construct() {}

    /**
     * @param int $count
     * @return array|mixed
     */
    public function execute($count)
    {
        $username = carbon_get_theme_option('ttf_username');
        $authSecret = get_option('tik_tok_feed_auth_secret');

        if (empty($username) || empty($count) || empty($authSecret)) {
            return [];
        }

        $response = Requests::GET(add_query_arg([
            'auth_secret' => $authSecret,
            'username' => $username,
            'count' => $count,
            'locale' => get_locale(),
        ], sprintf('%s/%s', self::API_DOMAIN, self::API_USER_FEED)));

        $body = $response->body;
        $feedData = json_decode($body);

        if (isset($feedData->code)) {
            return [];
        }

        return $feedData;
    }
}