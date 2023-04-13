<?php

namespace TikTokFeed\PublicView\Business\Model;

use TikTokFeed\Includes\TikTokFeedHelper;
use TikTokFeed\PublicView\Business\Api\UserProfileInterface;

class ProfileShortcode implements ProfileShortcodeInterface
{
    use TikTokFeedHelper;

    /**
     * @var UserProfileInterface
     */
    private $userProfileApi;

    /**
     * @param UserProfileInterface $userProfileApi
     */
    public function __construct($userProfileApi)
    {
        $this->userProfileApi = $userProfileApi;
    }

    /**
     * @param array $atts
     * @return string
     */
    public function profileShortcodeRenderHTML($atts)
    {
        $file = sprintf("%spublic/presentation/profile.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $userProfile = $this->userProfileApi->execute();

        if (isset($userProfile->user) === false) {
            return '';
        }

        $generalStyle = '';
        $followButtonStyle = '';
        $imageProfileStyle = '';

        if (carbon_get_theme_option('ttf_user_profile_style_show_wrapper') === 'yes') {
            if (!empty(carbon_get_theme_option('ttf_user_profile_style_wrap_primary_color')) && !empty(carbon_get_theme_option('ttf_user_profile_style_wrap_secondary_color'))) {
                $generalStyle .= sprintf('background-image: linear-gradient(-20deg, %s 0%%, %s 70%%);', carbon_get_theme_option('ttf_user_profile_style_wrap_primary_color'), carbon_get_theme_option('ttf_user_profile_style_wrap_secondary_color'));
            } elseif (!empty(carbon_get_theme_option('ttf_user_profile_style_wrap_primary_color'))) {
                $generalStyle .= sprintf('background-image: linear-gradient(-20deg, %s 0%%, #fb3961 70%%);', carbon_get_theme_option('ttf_user_profile_style_wrap_primary_color'));
            } elseif (!empty(carbon_get_theme_option('ttf_user_profile_style_wrap_secondary_color'))) {
                $generalStyle .= sprintf('background-image: linear-gradient(-20deg, #5bf7f2 0%%, %s 70%%);', carbon_get_theme_option('ttf_user_profile_style_wrap_secondary_color'));
            }
        } else {
            $generalStyle .= 'background: transparent;';
        }

        if (!empty(carbon_get_theme_option('ttf_user_profile_style_follow_button_color'))) {
            $followButtonStyle = sprintf('color: %s;', carbon_get_theme_option('ttf_user_profile_style_follow_button_color'));
        }

        if (!empty(carbon_get_theme_option('ttf_user_profile_style_follow_button_background'))) {
            $followButtonStyle .= sprintf('background: %s;', carbon_get_theme_option('ttf_user_profile_style_follow_button_background'));
        }

        if (!empty(carbon_get_theme_option('ttf_user_profile_style_image_profile_border_color'))) {
            $imageProfileStyle = sprintf('box-shadow: 0px 5px 50px 0px %s, 0px 0px 0px 7px rgb(60 60 60 / 50%%);', carbon_get_theme_option('ttf_user_profile_style_image_profile_border_color'));
        }

        $user = $userProfile->user;
        $stats = $userProfile->stats;

        return $this->renderView($file, [
            'avatar' => $user->avatarMedium,
            'url' => sprintf('https://tiktok.com/@%s', $user->uniqueId),
            'unique_id' => $user->uniqueId,
            'nickname' => $user->nickname,
            'description' => $user->signature,
            'is_verified' => !empty($user->verified),
            'followers' => $this->restyleCount($stats->followerCount),
            'following' => $this->restyleCount($stats->followingCount),
            'likes' => $this->restyleCount($stats->heartCount),
            'videos' => $this->restyleCount($stats->videoCount),
            'show_profile_description' => carbon_get_theme_option('ttf_user_profile_show_description'),
            'show_following' => carbon_get_theme_option('ttf_user_profile_show_following'),
            'show_followers' => carbon_get_theme_option('ttf_user_profile_show_followers'),
            'show_likes' => carbon_get_theme_option('ttf_user_profile_show_likes'),
            'show_videos' => carbon_get_theme_option('ttf_user_profile_show_views'),
            'show_follow_button' => carbon_get_theme_option('ttf_user_profile_follow_button'),
            'general_style' => sprintf('style="%s"', $generalStyle),
            'follow_button_style' => sprintf('style="%s"', $followButtonStyle),
            'image_profile_style' => sprintf('style="%s"', $imageProfileStyle),
        ]);
    }
}