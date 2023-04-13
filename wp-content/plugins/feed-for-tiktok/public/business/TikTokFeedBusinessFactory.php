<?php

namespace TikTokFeed\PublicView\Business;

use TikTokFeed\Config\AbstractConfigInterface;
use TikTokFeed\PublicView\Business\Api\Feed;
use TikTokFeed\PublicView\Business\Api\FeedInterface;
use TikTokFeed\PublicView\Business\Api\UserProfile;
use TikTokFeed\PublicView\Business\Api\UserProfileInterface;
use TikTokFeed\PublicView\Business\Model\ElementorFeedWidget;
use TikTokFeed\PublicView\Business\Model\ElementorFeedWidgetInterface;
use TikTokFeed\PublicView\Business\Model\ElementorProfileWidget;
use TikTokFeed\PublicView\Business\Model\ElementorProfileWidgetInterface;
use TikTokFeed\PublicView\Business\Model\FeedShortcode;
use TikTokFeed\PublicView\Business\Model\ProfileShortcode;
use TikTokFeed\PublicView\Business\Model\ProfileShortcodeInterface;
use TikTokFeed\PublicView\Core\AbstractFactory;
use TikTokFeed\PublicView\Persistence\TikTokFeedEntityManagerInterface;
use TikTokFeed\PublicView\Persistence\TikTokFeedRepositoryInterface;

/**
 * Class TikTokFeedBusinessFactory
 * @package TikTokFeed\PublicView\Business
 * @method AbstractConfigInterface getConfig()
 * @method TikTokFeedRepositoryInterface getRepository()
 * @method TikTokFeedEntityManagerInterface getEntityManager()
 */
class TikTokFeedBusinessFactory extends AbstractFactory
{
    /**
     * @return FeedShortcode
     */
    public function createFeedShortcode()
    {
        return new FeedShortcode($this->createFeedApi());
    }

    /**
     * @return ProfileShortcodeInterface
     */
    public function createProfileShortcode()
    {
        return new ProfileShortcode($this->createUserProfileApi());
    }

    /**
     * @return ElementorFeedWidgetInterface
     */
    public function createElementorFeedWidget()
    {
        return new ElementorFeedWidget();
    }

    /**
     * @return ElementorProfileWidgetInterface
     */
    public function createElementorProfileWidget()
    {
        return new ElementorProfileWidget();
    }

    /**
     * @return FeedInterface
     */
    protected function createFeedApi()
    {
        return new Feed();
    }

    /**
     * @return UserProfileInterface
     */
    protected function createUserProfileApi()
    {
        return new UserProfile();
    }
}
