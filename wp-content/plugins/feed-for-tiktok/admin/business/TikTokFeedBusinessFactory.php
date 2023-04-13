<?php

namespace TikTokFeed\AdminView\Business;

use TikTokFeed\AdminView\Business\Api\Authenticator;
use TikTokFeed\AdminView\Business\Api\AuthenticatorInterface;
use TikTokFeed\AdminView\Business\Model\AdminPage;
use TikTokFeed\AdminView\Business\Model\AdminPageInterface;
use TikTokFeed\AdminView\Core\AbstractFactory;
use TikTokFeed\AdminView\Persistence\TikTokFeedEntityManagerInterface;
use TikTokFeed\AdminView\Persistence\TikTokFeedRepositoryInterface;
use TikTokFeed\Config\AbstractConfigInterface;
use TikTokFeed\Includes\PluginResponse;

/**
 * Class TikTokFeedBusinessFactory
 * @package TikTokFeed\AdminView\Business
 * @method AbstractConfigInterface getConfig()
 * @method TikTokFeedRepositoryInterface getRepository()
 * @method TikTokFeedEntityManagerInterface getEntityManager()
 */
class TikTokFeedBusinessFactory extends AbstractFactory
{
    /**
     * @return AdminPageInterface
     */
    public function createAdminPage()
    {
        return new AdminPage();
    }

    /**
     * @return AuthenticatorInterface
     */
    public function createApiAuthenticator()
    {
        return new Authenticator($this->createPluginResponse());
    }

    /**
     * @return PluginResponse
     */
    protected function createPluginResponse()
    {
        return new PluginResponse();
    }
}
