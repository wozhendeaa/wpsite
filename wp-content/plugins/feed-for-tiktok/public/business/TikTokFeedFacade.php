<?php

namespace TikTokFeed\PublicView\Business;

use Elementor\Plugin;
use TikTokFeed\PublicView\Core\AbstractFacade;
use TikTokFeed\PublicView\Persistence\TikTokFeedEntityManagerInterface;
use TikTokFeed\PublicView\Persistence\TikTokFeedRepositoryInterface;

/**
 * Class TikTokFeedBusinessFactory
 * @package TikTokFeed\PublicView\Business
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

    /**
     * @param array $atts
     * @return string
     */
    public function feedShortcodeRenderHTML($atts)
    {
        return $this->getFactory()->createFeedShortcode()->feedShortcodeRenderHTML($atts);
    }

    /**
     * @param array $atts
     * @return string
     */
    public function userProfileShortcodeRenderHTML($atts)
    {
        return $this->getFactory()->createProfileShortcode()->profileShortcodeRenderHTML($atts);
    }

    public function registerElementorWidgets()
    {
        $elementorFeedWidget = $this->getFactory()->createElementorFeedWidget();
        $elementorProfileWidget = $this->getFactory()->createElementorProfileWidget();

        Plugin::instance()->widgets_manager->register_widget_type($elementorFeedWidget);
        Plugin::instance()->widgets_manager->register_widget_type($elementorProfileWidget);
    }
}
