<?php

namespace TikTokFeed\AdminView;

use TikTokFeed\AdminView\Business\TikTokFeedFacade as TikTokFeedAdminViewFacade;
use TikTokFeed\PublicView\Business\TikTokFeedFacade as TikTokFeedPublicViewFacade;
use Carbon_Fields\Carbon_Fields;

class TikTokFeedAdmin
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

	public function enqueue_styles($hook)
	{
        if ($hook === 'toplevel_page_crb_carbon_fields_container_tik_tok') {
            wp_enqueue_style($this->pluginName, '', [], $this->version, 'all');
        }
	}

	public function enqueue_scripts($hook)
	{
        if ($hook === 'toplevel_page_crb_carbon_fields_container_tik_tok') {
            $authSecret = get_option('tik_tok_feed_auth_secret');

            wp_register_script($this->pluginName, PLUGIN_TIK_TOK_FEED_URL . 'admin/dist/js/tik-tok-feed.js', ['jquery'], $this->version, true);
            wp_localize_script($this->pluginName, 'ajax_tik_tok_feed_admin_object', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('ajax_tik_tok_feed_admin'),
                'has_api_auth_secret' => !empty($authSecret) ? 'true' : 'false',
                'strings' => [
                    'auth_button' => __('Connect to the API', 'tik-tok-feed'),
                    'auth_alerts' => [
                        'notice' => __('To make the plugin functional, please complete your client id and make a connection to the API!', 'tik-tok-feed'),
                    ],
                    'please_wait' => __('Please wait...', 'tik-tok-feed'),
                ]
            ]);
            wp_enqueue_script($this->pluginName);
        }
	}

    public function carbonFieldsFrameworkLoad()
    {
        Carbon_Fields::boot();
    }

    public function createAdminPage()
    {
        $this->adminViewFacade->createAdminPage();
    }

    public function apiAuthenticator()
    {
        $this->adminViewFacade->apiAuthenticator();
    }
//
//    function sar_custom_curl_timeout( $handle ){
//        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
//        curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
//    }
//
//    function sar_custom_http_request_timeout( $timeout_value ) {
//        return 30; // 30 seconds. Too much for production, only for testing.
//    }
//
//// Setting custom timeout in HTTP request args
//    function sar_custom_http_request_args( $r ){
//        $r['timeout'] = 30; // 30 seconds. Too much for production, only for testing.
//        return $r;
//    }
}
