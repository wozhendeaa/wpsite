<?php

namespace TikTokFeed\PublicView\Business\Model;

interface ProfileShortcodeInterface
{
    /**
     * @param array $atts
     * @return string
     */
    public function profileShortcodeRenderHTML($atts);
}