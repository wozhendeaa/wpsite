<?php

namespace TikTokFeed\PublicView\Business;

interface TikTokFeedFacadeInterface
{
    /**
     * @param array $atts
     * @return string
     */
    public function feedShortcodeRenderHTML($atts);

    /**
     * @param array $atts
     * @return string
     */
    public function userProfileShortcodeRenderHTML($atts);

    public function registerElementorWidgets();
}
