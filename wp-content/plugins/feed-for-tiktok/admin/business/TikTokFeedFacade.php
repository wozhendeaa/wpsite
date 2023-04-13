<?php

namespace TikTokFeed\AdminView\Business;

use TikTokFeed\AdminView\Core\AbstractFacade;
use TikTokFeed\AdminView\Persistence\TikTokFeedEntityManagerInterface;
use TikTokFeed\AdminView\Persistence\TikTokFeedRepositoryInterface;

/**
 * Class TikTokFeedFacade
 * @package TikTokFeed\AdminView\Business
 * @method TikTokFeedBusinessFactory getFactory()
 * @method TikTokFeedRepositoryInterface getRepository()
 * @method TikTokFeedEntityManagerInterface getEntityManager()
 */
class TikTokFeedFacade extends AbstractFacade implements TikTokFeedFacadeInterface
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function createAdminPage()
    {
        $this->getFactory()->createAdminPage()->create();
    }

    public function apiAuthenticator()
    {
        $this->getFactory()->createApiAuthenticator()->execute();
    }
}
