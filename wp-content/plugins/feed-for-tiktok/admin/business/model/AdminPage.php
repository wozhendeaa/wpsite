<?php

namespace TikTokFeed\AdminView\Business\Model;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class AdminPage implements AdminPageInterface
{
    public function __construct() {}

    public function create()
    {
        $menuIcon = sprintf('%simages/tik-tok-menu-icon.png', PLUGIN_TIK_TOK_FEED_URL);

        Container::make('theme_options', __('Tik Tok', 'tik-tok-feed'))
            ->set_icon($menuIcon)
            ->set_page_menu_position(3)
            ->add_tab(__('General Settings', 'tik-tok-feed'), array_merge(
                $this->setGeneralSettings()
            ))
            ->add_tab(__('Shortcode Feed Settings', 'tik-tok-feed'), array_merge(
                $this->setFeedSettings()
            ))
            ->add_tab(__('Shortcode Feed Style', 'tik-tok-feed'), array_merge(
                $this->setFeedStyleSettings()
            ))
            ->add_tab(__('Shortcode User Profile Settings', 'tik-tok-feed'), array_merge(
                $this->setUserProfileSettings()
            ))
            ->add_tab(__('Shortcode User Profile Style', 'tik-tok-feed'), array_merge(
                $this->setUserProfileStyleSettings()
            ));
    }

    /**
     * @return array
     */
    private function setGeneralSettings()
    {
        return [
            Field::make('text', 'ttf_customer_id', __('Customer ID', 'tik-tok-feed'))
                ->set_default_value('c3b1852d-eca8-c81e-cdbe-45bf78930c2e')
                ->set_help_text(__('In order to use this plugin you need to connect to the API. Please fill in the field above with your customer id: c3b1852d-eca8-c81e-cdbe-45bf78930c2e', 'tik-tok-feed')),
            Field::make('text', 'ttf_username', __('Tik Tok Username', 'tik-tok-feed'))
                ->set_help_text(__('Write without @. E.g.: sgmro', 'tik-tok-feed')),
        ];
    }

    /**
     * @return array
     */
    private function setFeedSettings()
    {
        return [
            Field::make('separator', 'ttf_shortcode', __('Shortcode:', 'tik-tok-feed') . '[tik-tok-feed]'),
            Field::make('number', 'ttf_videos_count', __('Number of videos', 'tik-tok-feed'))
                ->set_default_value(5)
                ->set_min(1)
                ->set_max(20)
                ->set_help_text(__('The number of videos we will display in the feed. Max: 20', 'tik-tok-feed')),
            Field::make('radio', 'ttf_carousel_type', __('Carousel type', 'tik-tok-feed'))
                ->add_options([
                    'vertical' => __('Vertical', 'tik-tok-feed'),
                    'horizontally' => __('Horizontally', 'tik-tok-feed'),
                ])
                ->set_default_value('horizontally'),
            Field::make('radio', 'ttf_number_of_followers', __('Show number of followers', 'tik-tok-feed'))
                ->add_options([
                    'yes' => __('YES', 'tik-tok-feed'),
                    'no' => __('NO', 'tik-tok-feed'),
                ])
                ->set_default_value('yes')
                ->set_help_text(__('Tell me if you want to show the number of followers.', 'tik-tok-feed')),
            Field::make('radio', 'ttf_show_follow_button', __('Show follow button', 'tik-tok-feed'))
                ->add_options([
                    'yes' => __('YES', 'tik-tok-feed'),
                    'no' => __('NO', 'tik-tok-feed'),
                ])
                ->set_default_value('yes'),
            Field::make('radio', 'ttf_show_description', __('Show description', 'tik-tok-feed'))
                ->add_options([
                    'yes' => __('YES', 'tik-tok-feed'),
                    'no' => __('NO', 'tik-tok-feed'),
                ])
                ->set_default_value('yes'),
            Field::make('separator', 'ttf_video_cover_separator', __('Video Cover', 'tik-tok-feed')),
            Field::make('select', 'ttf_video_cover_type', __('Type', 'tik-tok-feed'))
                ->set_options([
                    'both' =>  __('Both', 'tik-tok-feed'),
                    'static' =>  __('Static', 'tik-tok-feed'),
                    'dynamic' =>  __('Dynamic', 'tik-tok-feed'),
                ])
                ->set_default_value('both'),
            Field::make('radio', 'ttf_video_cover_show_views', __('Show number of views', 'tik-tok-feed'))
                ->add_options([
                    'yes' => __('YES', 'tik-tok-feed'),
                    'no' => __('NO', 'tik-tok-feed'),
                ])
                ->set_default_value('yes'),

            Field::make('separator', 'ttf_video_module_separator', __('Video Popup', 'tik-tok-feed')),
            Field::make('checkbox', 'ttf_video_module_show_date', __('Show date', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_video_module_show_button', __('Show button', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_video_module_show_likes', __('Show likes', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_video_module_show_comments', __('Show comments', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_video_module_show_shares', __('Show shares', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_video_module_show_views', __('Show views', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_video_module_show_description', __('Show description', 'tik-tok-feed'))
                ->set_option_value('yes'),
        ];
    }

    /**
     * @return array
     */
    private function setFeedStyleSettings()
    {
        return [
            Field::make('separator', 'ttf_general_style_follow_button', __('Follow button', 'tik-tok-feed')),
            Field::make('color', 'ttf_general_style_follow_button_color', __('Color', 'tik-tok-feed'))
                ->set_alpha_enabled(true),
            Field::make('color', 'ttf_general_style_follow_button_background', __('Background', 'tik-tok-feed'))
                ->set_alpha_enabled(true),

            Field::make('separator', 'ttf_general_style_profile_border', __('Image profile border', 'tik-tok-feed')),
            Field::make('color', 'ttf_general_style_profile_border_primary_color', __('Primary color', 'tik-tok-feed'))
                ->set_alpha_enabled(true),
            Field::make('color', 'ttf_general_style_profile_border_secondary_color', __('Secondary color', 'tik-tok-feed'))
                ->set_alpha_enabled(true),

            Field::make('separator', 'ttf_navigation_style_arrows', __('Arrows', 'tik-tok-feed')),
            Field::make('color', 'ttf_navigation_style_arrows_color', __('Color', 'tik-tok-feed'))
                ->set_alpha_enabled(true),
            Field::make('number', 'ttf_navigation_style_arrows_size', __('Size', 'tik-tok-feed'))
                ->set_default_value(24)
                ->set_min(24)
                ->set_max(60),

            Field::make('separator', 'ttf_video_cover_style', __('Video cover', 'tik-tok-feed')),
            Field::make('color', 'ttf_video_cover_style_color_views', __('Color number of views', 'tik-tok-feed'))
                ->set_alpha_enabled(true),

            Field::make('separator', 'ttf_video_modal_style', __('Video modal', 'tik-tok-feed')),
            Field::make('color', 'ttf_video_modal_style_icons_background', __('Background for icons', 'tik-tok-feed'))
                ->set_alpha_enabled(true),
            Field::make('color', 'ttf_video_modal_style_icons_color', __('Color for icons', 'tik-tok-feed'))
                ->set_alpha_enabled(true),
            Field::make('color', 'ttf_video_modal_style_button_primary_color', __('Primary color for button', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_video_module_show_button',
                        'value' => true,
                        'compare' => '=',
                    ]
                ]),
            Field::make('color', 'ttf_video_modal_style_button_secondary_color', __('Secondary color for button', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_video_module_show_button',
                        'value' => true,
                        'compare' => '=',
                    ]
                ]),
            Field::make('color', 'ttf_video_modal_style_button_third_color', __('Third color for button', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_video_module_show_button',
                        'value' => true,
                        'compare' => '=',
                    ]
                ]),
        ];
    }

    /**
     * @return array
     */
    private function setUserProfileSettings()
    {
        return [
            Field::make('separator', 'ttf_user_profile_shortcode', __('Shortcode:', 'tik-tok-feed') . '[tik-tok-user-profile]'),
            Field::make('separator', 'ttf_user_profile_general_settings', __('General settings:', 'tik-tok-feed')),
            Field::make('checkbox', 'ttf_user_profile_show_description', __('Show description', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_user_profile_show_following', __('Show following', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_user_profile_show_followers', __('Show followers', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_user_profile_show_likes', __('Show likes', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_user_profile_show_views', __('Show views', 'tik-tok-feed'))
                ->set_option_value('yes'),
            Field::make('checkbox', 'ttf_user_profile_follow_button', __('Show follow button', 'tik-tok-feed'))
                ->set_option_value('yes'),
        ];
    }

    /**
     * @return array
     */
    private function setUserProfileStyleSettings()
    {
        return [
            Field::make('separator', 'ttf_user_profile_wrapper', __('Wrapper', 'tik-tok-feed')),
            Field::make('radio', 'ttf_user_profile_style_show_wrapper', __('Show wrapper', 'tik-tok-feed'))
                ->add_options([
                    'yes' => __('YES', 'tik-tok-feed'),
                    'no' => __('NO', 'tik-tok-feed'),
                ]),
            Field::make('color', 'ttf_user_profile_style_wrap_primary_color', __('Primary color', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_user_profile_style_show_wrapper',
                        'value' => 'yes',
                        'compare' => '=',
                    ]
                ]),
            Field::make('color', 'ttf_user_profile_style_wrap_secondary_color', __('Secondary color', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_user_profile_style_show_wrapper',
                        'value' => 'yes',
                        'compare' => '=',
                    ]
                ]),

            Field::make('separator', 'ttf_user_profile_style_follow_button', __('Follow button', 'tik-tok-feed')),
            Field::make('color', 'ttf_user_profile_style_follow_button_color', __('Color', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_user_profile_follow_button',
                        'value' => true,
                        'compare' => '=',
                    ]
                ]),
            Field::make('color', 'ttf_user_profile_style_follow_button_background', __('Background', 'tik-tok-feed'))
                ->set_alpha_enabled(true)
                ->set_conditional_logic([
                    [
                        'field' => 'ttf_user_profile_follow_button',
                        'value' => true,
                        'compare' => '=',
                    ]
                ]),

            Field::make('separator', 'ttf_user_profile_style_image_profile_border', __('Image profile border', 'tik-tok-feed')),
            Field::make('color', 'ttf_user_profile_style_image_profile_border_color', __('Color', 'tik-tok-feed'))
                ->set_alpha_enabled(true),
        ];
    }
}