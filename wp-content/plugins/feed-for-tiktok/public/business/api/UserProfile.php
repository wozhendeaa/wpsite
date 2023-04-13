<?php

namespace TikTokFeed\PublicView\Business\Api;

use TikTokFeed\Includes\BaseApi;
use \Requests;

class UserProfile extends BaseApi implements UserProfileInterface
{
    public function __construct() {}

    /**
     * @return array|mixed
     */
    public function execute()
    {
        $username = carbon_get_theme_option('ttf_username');
        $authSecret = get_option('tik_tok_feed_auth_secret');

        if (empty($username) || empty($authSecret)) {
            return [];
        }

        $response = Requests::GET(add_query_arg([
            'auth_secret' => $authSecret,
            'username' => $username,
            'locale' => get_locale(),
        ], sprintf('%s/%s', self::API_DOMAIN, self::API_USER_PROFILE)));

        $body = $response->body;
        $feedData = json_decode($body);

        if (isset($feedData->code)) {
            return [];
        }

        return $feedData;
    }
}