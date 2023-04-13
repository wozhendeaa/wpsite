<?php

namespace TikTokFeed\AdminView\Business\Api;

use \Requests;
use TikTokFeed\Includes\BaseApi;
use TikTokFeed\Includes\PluginResponse;
use TikTokFeed\Includes\TikTokFeedHelper;

class Authenticator extends BaseApi implements AuthenticatorInterface
{
    use TikTokFeedHelper;

    /**
     * @var PluginResponse
     */
    private $pluginResponse;

    /**
     * Authenticator constructor.
     * @param PluginResponse $pluginResponse
     */
    public function __construct($pluginResponse)
    {
        $this->pluginResponse = $pluginResponse;
    }

    public function execute()
    {
        check_ajax_referer('ajax_tik_tok_feed_admin', 'security');

        $customerId = wp_kses($_POST['client_id'], []);

        $data = [
            'client_id' => $customerId,
            'locale' => get_locale(),
        ];

        $response = Requests::post(sprintf('%s/%s', self::API_DOMAIN, self::API_AUTH), [], $data);

        $body = $response->body;
        $data = json_decode($body, true);

        if (!in_array($data['code'], [200, 400])) {
            $this->pluginResponse->isSuccess = false;
            $this->pluginResponse->message = $data['message'];

            delete_option('tik_tok_feed_auth_secret');
        }

        if ($data['code'] === 200) {
            update_option('tik_tok_feed_auth_secret', $data['auth_secret']);
            carbon_set_theme_option('ttf_customer_id', $customerId);

            $this->pluginResponse->isSuccess = true;
            $this->pluginResponse->message = __('You have successfully connected to the API.', 'tik-tok-feed');
        }

        echo json_encode($this->pluginResponse);

        wp_die();
    }
}