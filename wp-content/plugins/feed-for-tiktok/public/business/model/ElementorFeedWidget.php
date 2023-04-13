<?php

namespace TikTokFeed\PublicView\Business\Model;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Plugin;
use TikTokFeed\Includes\TikTokFeedHelper;
use TikTokFeed\PublicView\Business\Api\Feed;

class ElementorFeedWidget extends Widget_Base implements ElementorFeedWidgetInterface
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
        return self::SLUG;
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
        return __('Tik Tok Feed', self::SLUG);
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
        $this->videoControl();
        $this->generalStyleControl();
        $this->navigationStyleControl();
        $this->videoCoverStyleControl();
        $this->videoModalStyleControl();
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
        $settings = $this->get_settings_for_display();

        $file = sprintf("%spublic/presentation/feed/items.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $generalStyle = '';
        $profileBorderStyle= '';
        $followButtonStyle = '';
        $arrowsStyle = '';

        if (!empty($settings['general_font_family'])) {
            $generalStyle = sprintf('font-family: %s;', $settings['general_font_family']);
        }

        if (!empty($settings['general_style_profile_border_primary_color']) && !empty($settings['general_style_profile_border_secondary_color'])) {
            $profileBorderStyle = sprintf('background-image: linear-gradient(white, white), radial-gradient(circle at top left, %s, %s);', $settings['general_style_profile_border_primary_color'], $settings['general_style_profile_border_secondary_color']);
        } elseif (!empty($settings['general_style_profile_border_primary_color'])) {
            $profileBorderStyle = sprintf('background-image: linear-gradient(white, white), radial-gradient(circle at top left, %s, #fb3961);', $settings['general_style_profile_border_primary_color']);
        } elseif (!empty($settings['general_style_profile_border_secondary_color'])) {
            $profileBorderStyle = sprintf('background-image: linear-gradient(white, white), radial-gradient(circle at top left, #5bf7f2, %s);', $settings['general_style_profile_border_secondary_color']);
        }

        if (!empty($settings['general_style_follow_button_color'])) {
            $followButtonStyle = sprintf('color: %s;', $settings['general_style_follow_button_color']);
        }

        if (!empty($settings['general_style_follow_button_background'])) {
            $followButtonStyle .= sprintf('background: %s;', $settings['general_style_follow_button_background']);
        }

        if ($settings['arrows_size']['size'] !== 24) {
            $arrowsStyle = sprintf('width: %dpx; height: %dpx;', $settings['arrows_size']['size'], $settings['arrows_size']['size']);
        }

        if (!empty($settings['arrows_color'])) {
            $arrowsStyle .= sprintf('color: %s;', $settings['arrows_color']);
        }

        $feedData = (new Feed())->execute($settings['count']);

        if (empty($feedData)) {
            return '';
        }

        $slidesHtml = '';
        $modalsHtml = '';

        $viewData = [
            'is_elementor_edit_mode' => Plugin::$instance->editor->is_edit_mode(),
            'username' => carbon_get_theme_option('ttf_username'),
            'nickname' => $feedData->data->nickname,
            'avatar' => $feedData->data->avatar,
            'description' => $feedData->data->description,
            'is_verified' => $feedData->data->isVerified === false ? false : true,
            'followers' => $feedData->data->followers,
            'show_followers' => $settings['show_followers'] === 'yes',
            'show_follow_button' => $settings['show_follow_button'] === 'yes',
            'show_profile_description' => $settings['show_profile_description'] === 'yes',
            'url_profile' => sprintf('https://tiktok.com/@%s', carbon_get_theme_option('ttf_username')),
            'general_style' => sprintf('style="%s"', $generalStyle),
            'profile_border_style' => sprintf('style="%s"', $profileBorderStyle),
            'follow_button_style' => sprintf('style="%s"', $followButtonStyle),
            'arrows_style' => sprintf('style="%s"', $arrowsStyle),
            'carousel_type' => $settings['carousel_type'],
        ];

        $viewData = $this->setFollowers($feedData, $viewData);

        $videos = $feedData->data->videos;

        foreach ($videos as $key => $video) {
            $slidesHtml .= $this->renderItemSlideHTML($video, $settings);
            $modalsHtml .= $this->renderItemModalHTML($video, $settings, $key, count($videos));
        }

        $viewData['slides'] = $slidesHtml;
        $viewData['modals'] = $modalsHtml;

        echo $this->renderView($file, $viewData);
    }

    private function renderItemSlideHTML($item, $settings)
    {
        $file = sprintf("%spublic/presentation/feed/item-slide.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $numberOfViewsStyle = '';

        if (!empty($settings['video_cover_color_views'])) {
            $numberOfViewsStyle .= sprintf('color: %s;', $settings['video_cover_color_views']);
        }

        $item->plays = $this->restyleCount($item->plays);

        return $this->renderView($file, [
            'cover_type' => $settings['video_section_cover_type'],
            'cover_show_views' => $settings['cover_show_views'] === 'yes',
            'number_of_views_style' => sprintf('style="%s"', $numberOfViewsStyle),
            'item' => $item,
        ]);
    }

    private function renderItemModalHTML($item, $settings, $videoKey, $videosTotal)
    {
        $file = sprintf("%spublic/presentation/feed/item-modal.php", PLUGIN_TIK_TOK_FEED_PATH);

        if (file_exists($file) === false) {
            return '';
        }

        $iconsStyle = '';
        $buttonStyle = '';

        if (!empty($settings['video_modal_icons_background'])) {
            $iconsStyle .= sprintf('background: %s;', $settings['video_modal_icons_background']);
        }

        if (!empty($settings['video_modal_icons_color'])) {
            $iconsStyle .= sprintf('color: %s;', $settings['video_modal_icons_color']);
        }

        if (!empty($settings['video_modal_button_primary_color']) &&
            !empty($settings['video_modal_button_secondary_color']) &&
            !empty($settings['video_modal_button_third_color'])
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, %s 51%%, %s 100%%);',
                $settings['video_modal_button_primary_color'],
                $settings['video_modal_button_secondary_color'],
                $settings['video_modal_button_third_color']);
        } elseif (!empty($settings['video_modal_button_primary_color']) &&
            !empty($settings['video_modal_button_secondary_color'])
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, %s 51%%, #000000 100%%);',
                $settings['video_modal_button_primary_color'],
                $settings['video_modal_button_secondary_color']);
        } elseif (!empty($settings['video_modal_button_primary_color']) &&
            !empty($settings['video_modal_button_third_color'])
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, #fb3961 51%%, %s 100%%);',
                $settings['video_modal_button_primary_color'],
                $settings['video_modal_button_third_color']);
        } elseif (!empty($settings['video_modal_button_secondary_color']) &&
            !empty($settings['video_modal_button_third_color'])
        ) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, #5bf7f2 0%%, %s 51%%, %s 100%%);',
                $settings['video_modal_button_secondary_color'],
                $settings['video_modal_button_third_color']);
        } elseif (!empty($settings['video_modal_button_primary_color'])) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, %s 0%%, #fb3961 51%%, #000000 100%%);',
                $settings['video_modal_button_primary_color']);
        } elseif (!empty($settings['video_modal_button_secondary_color'])) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, #5bf7f2 0%%, %s 51%%, #000000 100%%);',
                $settings['video_modal_button_secondary_color']);
        } elseif (!empty($settings['video_modal_button_third_color'])) {
            $buttonStyle = sprintf('background-image: linear-gradient(to right, #5bf7f2 0%%, #fb3961 51%%, %s 100%%);',
                $settings['video_modal_button_third_color']);
        }

        $item->likes = $this->restyleCount($item->likes);
        $item->shares = $this->restyleCount($item->shares);
        $item->comments = $this->restyleCount($item->comments);
        $item->plays = $this->restyleCount($item->plays);
        $item->url = sprintf('https://tiktok.com/@%s/video/%s', carbon_get_theme_option('ttf_username'), $item->id);

        return $this->renderView($file, [
            'icons_style' => sprintf('style="%s"', $iconsStyle),
            'button_style' => sprintf('style="%s"', $buttonStyle),
            'show_date' => $settings['show_date'] === 'yes',
            'show_button' => $settings['show_button'] === 'yes',
            'show_likes' => $settings['show_likes'] === 'yes',
            'show_comments' => $settings['show_comments'] === 'yes',
            'show_shares' => $settings['show_shares'] === 'yes',
            'show_views' => $settings['show_views'] === 'yes',
            'show_description' => $settings['show_description'] === 'yes',
            'item' => $item,
            'prev_button' => $videoKey === 0 ? false : true,
            'next_button' => $videosTotal - 1 === $videoKey ? false : true,
        ]);
    }

    private function settingsControl()
    {
        $this->start_controls_section(
            'general_settings',
            [
                'label' => __('Settings', self::SLUG),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'count',
            [
                'label' => __('Number of videos', self::SLUG),
                'type' => Controls_Manager::NUMBER,
                'description' => __('The number of videos we will display in the feed. Max: 20', self::SLUG),
                'default' => 5,
                'min' => '1',
                'mix' => '20',
            ]
        );

        $this->add_control(
            'carousel_type',
            [
                'label' => __('Carousel type', self::SLUG),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontally',
                'options' => [
                    'vertical'  => __('Vertical', self::SLUG),
                    'horizontally' => __('Horizontally', self::SLUG),
                ],
            ]
        );

        $this->add_control(
            'show_followers',
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
            'show_follow_button',
            [
                'label' => __('Follow button', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
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

        $this->end_controls_section();
    }

    private function videoControl()
    {
        $this->start_controls_section(
            'video_section',
            [
                'label' => __('Video', self::SLUG),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'video_section_cover',
            [
                'label' => __('Cover', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'video_section_cover_type',
            [
                'label' => __('Type', self::SLUG),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'static'  => __('Static', self::SLUG),
                    'dynamic' => __('Dynamic', self::SLUG),
                    'both' => __('Both', self::SLUG),
                ],
            ]
        );

        $this->add_control(
            'cover_show_views',
            [
                'label' => __('Views', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'video_section_modal',
            [
                'label' => __('Popup', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => __('Date', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_button',
            [
                'label' => __('Button', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_likes',
            [
                'label' => __('Likes', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_comments',
            [
                'label' => __('Comments', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_shares',
            [
                'label' => __('Shares', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_views',
            [
                'label' => __('Views', self::SLUG),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', self::SLUG),
                'label_off' => __('Hide', self::SLUG),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => __('Description', self::SLUG),
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
            'general_style',
            [
                'label' => __('General', self::SLUG),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'general_font_family',
            [
                'label' => __('Font Family', self::SLUG),
                'type' => Controls_Manager::FONT,
                'default' => "'Quicksand', sans-serif",
            ]
        );

        $this->add_control(
            'general_style_follow_button',
            [
                'label' => __('Follow button', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_follow_button' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'general_style_follow_button_color',
            [
                'label' => __('Color', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_follow_button' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'general_style_follow_button_background',
            [
                'label' => __('Background', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_follow_button' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'general_style_profile_border',
            [
                'label' => __('Image profile border', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'general_style_profile_border_primary_color',
            [
                'label' => __('Primary color', self::SLUG),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'general_style_profile_border_secondary_color',
            [
                'label' => __('Secondary color', self::SLUG),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->end_controls_section();
    }

    private function navigationStyleControl()
    {
        $this->start_controls_section(
            'navigation_style',
            [
                'label' => __('Navigation', self::SLUG),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'arrows',
            [
                'label' => __('Arrows', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => __('Color', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label' => __('Size', self::SLUG),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 24,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ]
            ]
        );

        $this->end_controls_section();
    }

    private function videoCoverStyleControl()
    {
        $this->start_controls_section(
            'video_style',
            [
                'label' => __('Video Cover', self::SLUG),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'video_cover_color_views',
            [
                'label' => __('Color number of views', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'cover_show_views' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function videoModalStyleControl()
    {
        $this->start_controls_section(
            'video_modal_style',
            [
                'label' => __('Video Popup', self::SLUG),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'video_modal_icons',
            [
                'label' => __('Icons', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'video_modal_icons_background',
            [
                'label' => __('Background', self::SLUG),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'video_modal_icons_color',
            [
                'label' => __('Color', self::SLUG),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'video_modal_button',
            [
                'label' => __('Button', self::SLUG),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'video_modal_button_primary_color',
            [
                'label' => __('Primary color button', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_button' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'video_modal_button_secondary_color',
            [
                'label' => __('Secondary color button', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_button' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'video_modal_button_third_color',
            [
                'label' => __('Third color button', self::SLUG),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'show_button' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();
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