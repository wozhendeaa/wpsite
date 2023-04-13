<?php

namespace TikTokFeed\PublicView\Business\Model;

use TikTokFeed\Includes\TikTokFeedHelper;
use TikTokFeed\PublicView\Business\Api\FeedInterface;

class FeedShortcode implements FeedShortcodeInterface
{
    use TikTokFeedHelper;

    /**
     * @var FeedInterface
     */
    private $feedApi;

    /**
     * @param FeedInterface $feedApi
     */
    public function __construct($feedApi)
    {
        $this->feedApi = $feedApi;
    }

    /**
     * @param array $atts
     * @return string
     */
    public function feedShortcodeRenderHTML($atts)
    {
        $file = sprintf("%spublic/presentation/feed/items.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $count = carbon_get_theme_option('ttf_videos_count');

        $profileBorderStyle= '';
        $followButtonStyle = '';
        $arrowsStyle = '';

        if (!empty(carbon_get_theme_option('ttf_general_style_profile_border_primary_color')) && !empty(carbon_get_theme_option('ttf_general_style_profile_border_secondary_color'))) {
            $profileBorderStyle = sprintf('background-image: linear-gradient(white, white), radial-gradient(circle at top left, %s, %s);', carbon_get_theme_option('ttf_general_style_profile_border_primary_color'), carbon_get_theme_option('ttf_general_style_profile_border_secondary_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_general_style_profile_border_primary_color'))) {
            $profileBorderStyle = sprintf('background-image: linear-gradient(white, white), radial-gradient(circle at top left, %s, #fb3961);', carbon_get_theme_option('ttf_general_style_profile_border_primary_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_general_style_profile_border_secondary_color'))) {
            $profileBorderStyle = sprintf('background-image: linear-gradient(white, white), radial-gradient(circle at top left, #5bf7f2, %s);', carbon_get_theme_option('ttf_general_style_profile_border_secondary_color'));
        }

        if (!empty(carbon_get_theme_option('ttf_general_style_follow_button_color'))) {
            $followButtonStyle = sprintf('color: %s;', carbon_get_theme_option('ttf_general_style_follow_button_color'));
        }

        if (!empty(carbon_get_theme_option('ttf_general_style_follow_button_background'))) {
            $followButtonStyle .= sprintf('background: %s;', carbon_get_theme_option('ttf_general_style_follow_button_background'));
        }

        if (carbon_get_theme_option('ttf_navigation_style_arrows_size') !== 24) {
            $arrowsStyle = sprintf('width: %dpx; height: %dpx;', carbon_get_theme_option('ttf_navigation_style_arrows_size'), carbon_get_theme_option('ttf_navigation_style_arrows_size'));
        }

        if (!empty(carbon_get_theme_option('ttf_navigation_style_arrows_color'))) {
            $arrowsStyle .= sprintf('color: %s;', carbon_get_theme_option('ttf_navigation_style_arrows_color'));
        }

        $feedData = $this->feedApi->execute($count);

        if (empty($feedData)) {
            return '';
        }

        $slidesHtml = '';
        $modalsHtml = '';

        $viewData = [
            'username' => carbon_get_theme_option('ttf_username'),
            'nickname' => $feedData->data->nickname,
            'avatar' => $feedData->data->avatar,
            'description' => $feedData->data->description,
            'is_verified' => $feedData->data->isVerified === false ? false : true,
            'followers' => $this->restyleCount($feedData->data->followers),
            'show_followers' => carbon_get_theme_option('ttf_number_of_followers') === 'yes',
            'show_follow_button' => carbon_get_theme_option('ttf_show_follow_button') === 'yes',
            'show_profile_description' => carbon_get_theme_option('ttf_show_description') === 'yes',
            'url_profile' => sprintf('https://tiktok.com/@%s', carbon_get_theme_option('ttf_username')),
            'profile_border_style' => sprintf('style="%s"', $profileBorderStyle),
            'follow_button_style' => sprintf('style="%s"', $followButtonStyle),
            'arrows_style' => sprintf('style="%s"', $arrowsStyle),
            'carousel_type' => carbon_get_theme_option('ttf_carousel_type'),
        ];

        $viewData = $this->setFollowers($feedData, $viewData);

        $videos = $feedData->data->videos;

        foreach ($videos as $key => $video) {
            $slidesHtml .= $this->renderItemSlideHTML($video);
            $modalsHtml .= $this->renderItemModalHTML($video, $key, count($videos));
        }

        $viewData['slides'] = $slidesHtml;
        $viewData['modals'] = $modalsHtml;

        return $this->renderView($file, $viewData);
    }

    private function renderItemSlideHTML($item)
    {
        $file = sprintf("%spublic/presentation/feed/item-slide.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $numberOfViewsStyle = '';

        if (!empty(carbon_get_theme_option('ttf_video_cover_style_color_views'))) {
            $numberOfViewsStyle .= sprintf('color: %s;', carbon_get_theme_option('ttf_video_cover_style_color_views'));
        }

        $item->plays = $this->restyleCount($item->plays);

        return $this->renderView($file, [
            'cover_type' => carbon_get_theme_option('ttf_video_cover_type'),
            'cover_show_views' => carbon_get_theme_option('ttf_video_cover_show_views') === 'yes',
            'number_of_views_style' => sprintf('style="%s"', $numberOfViewsStyle),
            'item' => $item,
        ]);
    }

    private function renderItemModalHTML($item, $videoKey, $videosTotal)
    {
        $file = sprintf("%spublic/presentation/feed/item-modal.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $iconsStyle = '';
        $buttonStyle = '';

        if (!empty(carbon_get_theme_option('ttf_video_modal_style_icons_background'))) {
            $iconsStyle .= sprintf('background: %s;', carbon_get_theme_option('ttf_video_modal_style_icons_background'));
        }

        if (!empty(carbon_get_theme_option('ttf_video_modal_style_icons_color'))) {
            $iconsStyle .= sprintf('color: %s;', carbon_get_theme_option('ttf_video_modal_style_icons_color'));
        }

        if (!empty(carbon_get_theme_option('ttf_video_modal_style_button_primary_color')) &&
            !empty(carbon_get_theme_option('ttf_video_modal_style_button_secondary_color')) &&
            !empty(carbon_get_theme_option('ttf_video_modal_style_button_third_color'))
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, %s 51%%, %s 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_primary_color'),
                carbon_get_theme_option('ttf_video_modal_style_button_secondary_color'),
                carbon_get_theme_option('ttf_video_modal_style_button_third_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_video_modal_style_button_primary_color')) &&
            !empty(carbon_get_theme_option('ttf_video_modal_style_button_secondary_color'))
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, %s 51%%, #000000 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_primary_color'),
                carbon_get_theme_option('ttf_video_modal_style_button_secondary_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_video_modal_style_button_primary_color')) &&
            !empty(carbon_get_theme_option('ttf_video_modal_style_button_third_color'))
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, #fb3961 51%%, %s 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_primary_color'),
                carbon_get_theme_option('ttf_video_modal_style_button_third_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_video_modal_style_button_secondary_color')) &&
            !empty(carbon_get_theme_option('ttf_video_modal_style_button_third_color'))
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, #5bf7f2 0%%, %s 51%%, %s 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_secondary_color'),
                carbon_get_theme_option('ttf_video_modal_style_button_third_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_video_modal_style_button_primary_color'))) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, #fb3961 51%%, #000000 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_primary_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_video_modal_style_button_secondary_color'))) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, #5bf7f2 0%%, %s 51%%, #000000 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_secondary_color'));
        } elseif (!empty(carbon_get_theme_option('ttf_video_modal_style_button_third_color'))) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, #5bf7f2 0%%, #fb3961 51%%, %s 100%%);',
                carbon_get_theme_option('ttf_video_modal_style_button_third_color'));
        }

        $item->likes = $this->restyleCount($item->likes);
        $item->shares = $this->restyleCount($item->shares);
        $item->comments = $this->restyleCount($item->comments);
        $item->plays = $this->restyleCount($item->plays);
        $item->url = sprintf('https://tiktok.com/@%s/video/%s', carbon_get_theme_option('ttf_username'), $item->id);

        return $this->renderView($file, [
            'icons_style' => sprintf('style="%s"', $iconsStyle),
            'button_style' => sprintf('style="%s"', $buttonStyle),
            'show_date' => carbon_get_theme_option('ttf_video_module_show_date'),
            'show_button' => carbon_get_theme_option('ttf_video_module_show_button'),
            'show_likes' => carbon_get_theme_option('ttf_video_module_show_likes'),
            'show_comments' => carbon_get_theme_option('ttf_video_module_show_comments'),
            'show_shares' => carbon_get_theme_option('ttf_video_module_show_shares'),
            'show_views' => carbon_get_theme_option('ttf_video_module_show_views'),
            'show_description' => carbon_get_theme_option('ttf_video_module_show_description'),
            'item' => $item,
            'prev_button' => $videoKey === 0 ? false : true,
            'next_button' => $videosTotal - 1 === $videoKey ? false : true,
        ]);
    }

    /**
     * @param $feedData
     * @param $viewData
     * @return array
     */
    private function setFollowers($feedData, $viewData)
    {
        if (carbon_get_theme_option('ttf_number_of_followers') === 'yes') {
            $viewData['followers'] = $this->restyleCount($feedData->data->followers);
        }

        return $viewData;
    }
}