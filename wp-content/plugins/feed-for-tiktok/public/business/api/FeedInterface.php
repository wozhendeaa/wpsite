<?php

namespace TikTokFeed\PublicView\Business\Api;

interface FeedInterface
{
    /**
     * @param int $count
     * @return array|mixed
     */
    public function execute($count);
}