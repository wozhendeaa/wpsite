<?php

namespace TikTokFeed\PublicView\Core;

abstract class AbstractFacade
{
    final public function getFactory()
    {
        $className = sprintf('\%s\PublicView\Business\%sBusinessFactory', 'TikTokFeed', 'TikTokFeed');

        return new $className();
    }

    final public function getEntityManager()
    {
        $className = sprintf('\%s\PublicView\Persistence\%sEntityManager', 'TikTokFeed', 'TikTokFeed');

        return new $className();
    }

    final public function getRepository()
    {
        $className = sprintf('\%s\PublicView\Persistence\%sRepository', 'TikTokFeed', 'TikTokFeed');

        return new $className();
    }

    private function getPluginName()
    {
        $parts = explode('/', PLUGIN_TIK_TOK_FEED_SLUG);

        $pluginDirParts = explode('-', $parts[0]);

        $name = '';

        foreach ($pluginDirParts as $pluginDirPart) {
            $name .= ucfirst($pluginDirPart);
        }

        return $name;
    }
}
