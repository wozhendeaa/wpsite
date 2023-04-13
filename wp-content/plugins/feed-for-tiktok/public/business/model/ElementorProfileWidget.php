<?php

namespace TikTokFeed\PublicView\Business\Model;

use Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use TikTokFeed\Includes\TikTokFeedHelper;
use TikTokFeed\PublicView\Business\Api\UserProfile;

class ElementorProfileWidget extends Widget_Base implements ElementorProfileWidgetInterface
{
    use TikTokFeedHelper;

    public const SLUG = 'tik-tok-feed';

    /**
     * Get widget name.
     *
     * Retrieve oEmbed widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'tik-tok-user-profile';
    }

    /**
     * Get widget title.
     *
     * Retrieve oEmbed widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Tik Tok User Profile', self::SLUG);
    }

    /**
     * Get widget icon.
     *
     * Retrieve oEmbed widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fab fa-tiktok';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the oEmbed widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Register oEmbed widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->settingsControl();
        $this->generalStyleControl();
    }

    /**
     * Render oEmbed widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $file = sprintf("%spublic/presentation/profile.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $userProfile = (new UserProfile())->execute();

        if (isset($userProfile->user) === false) {
            return '';
        }

        $settings = $this->get_settings_for_display();

        $generalStyle = '';
        $followButtonStyle = '';
        $imageProfileStyle = '';

        if (!empty($settings['user_profile_font_family'])) {
            $generalStyle = sprintf('font-family: %s;', $settings['user_profile_font_family']);
        }

        if ($settings['user_profile_wrap_show'] === 'yes') {
            if (!empty($settings['user_profile_wrap_primary_color']) && !empty($settings['user_profile_wrap_secondary_color'])) {
                $generalStyle .= sprintf('background-image: linear-gradient(-20deg, %s 0%%, %s 70%%);', $settings['user_profile_wrap_primary_color'], $settings['user_profile_wrap_secondary_color']);
            } elseif (!empty($settings['user_profile_wrap_primary_color'])) {
                $generalStyle .= sprintf('background-image: linear-gradient(-20deg, %s 0%%, #fb3961 70%%);', $settings['user_profile_wrap_primary_color']);
            } elseif (!empty($settings['user_profile_wrap_secondary_color'])) {
                $generalStyle .= sprintf('background-image: linear-gradient(-20deg, #5bf7f2 0%%, %s 70%%);', $settings['user_profile_wrap_secondary_color']);
            }
        } else {
            $generalStyle .= 'background: transparent;';
        }

        if (!empty($settings['user_profile_follow_button_color'])) {
            $followButtonStyle = sprintf('color: %s;', $settings['user_profile_follow_button_color']);
        }

        if (!empty($settings['user_profile_follow_button_background'])) {
            $followButtonStyle .= sprintf('background: %s;', $settings['user_profile_follow_button_background']);
        }

        if (!empty($settings['user_profile_profile_border_color'])) {
            $imageProfileStyle = sprintf('box-shadow: 0px 5px 50px 0px %s, 0px 0px 0px 7px rgb(60 60 60 / 50%%);', $settings['user_profile_profile_border_color']);
        }

        $user = $userProfile->user;
        $stats = $userProfile->stats;

        echo $this->renderView($file, [
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
            'show_profile_description' => $settings['show_profile_description'] === 'yes',
            'show_following' => $settings['show_profile_following'] === 'yes',
            'show_followers' => $settings['show_profile_followers'] === 'yes',
            'show_likes' => $settings['show_profile_likes'] === 'yes',
            'show_videos' => $settings['show_profile_videos'] === 'yes',
            'show_follow_button' => $settings['show_profile_follow_button'] === 'yes',
            'general_style' => sprintf('style="%s"', $generalStyle),
            'follow_button_style' => sprintf('style="%s"', $followButtonStyle),
            'image_profile_style' => sprintf('style="%s"', $imageProfileStyle),
        ]);
    }

    private function settingsControl()
    {
        $this->start_controls_section(
            'user_profile_general_settings',
            [
                'label' => __('Settings', self::SLUG),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_profile_description',
            [
                'label' => __('Description', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_profile_following',
            [
                'label' => __('Number of following', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_profile_followers',
            [
                'label' => __('Number of followers', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_profile_likes',
            [
                'label' => __('Number of likes', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_profile_videos',
            [
                'label' => __('Number of videos', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_profile_follow_button',
            [
                'label' => __('Follow button', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    private function generalStyleControl()
    {
        $this->start_controls_section(
            'user_profile_general_style',
            [
                'label' => __('General', self::SLUG),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'user_profile_font_family',
            [
                'label' => __('Font Family', self::SLUG),
                'type' => Controls_Manager::FONT,
                'default' => "'Quicksand', sans-serif",
            ]
        );

        $this->add_control(
            'user_profile_wrap',
            [
                'label' => __('Wrapper', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'user_profile_wrap_show',
            [
                'label' => __('Show wrapper', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', self::SLUG),
                'label_off' => __('No', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'user_profile_wrap_primary_color',
            [
                'label' => __('Primary color', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'user_profile_wrap_show' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'user_profile_wrap_secondary_color',
            [
                'label' => __('Secondary color', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'user_profile_wrap_show' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'user_profile_follow_button',
            [
                'label' => __('Follow button', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_profile_follow_button' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'user_profile_follow_button_color',
            [
                'label' => __('Color', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_profile_follow_button' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'user_profile_follow_button_background',
            [
                'label' => __('Background', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_profile_follow_button' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'user_profile_profile_border',
            [
                'label' => __('Image profile border', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'user_profile_profile_border_color',
            [
                'label' => __('Color', self::SLUG),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->end_controls_section();
    }
}