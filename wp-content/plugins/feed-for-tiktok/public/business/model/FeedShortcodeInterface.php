<?php

namespace TikTokFeed\PublicView\Business\Model;

interface FeedShortcodeInterface
{
    /**
     * @param array $atts
     * @return string
     */
    public function feedShortcodeRenderHTML($atts);
}