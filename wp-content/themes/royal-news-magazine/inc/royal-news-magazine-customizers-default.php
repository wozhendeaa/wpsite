<?php
if (!function_exists('royal_news_magazine_theme_options')) :
    function royal_news_magazine_theme_options()
    {
        $defaults = array(

            'facebook' => '',
            'pinterest' => '',


            'main_banner_category' => '',
            'royal_news_magazine_slider_category' => '',
            'main_banner_third_column_category' => '',
            'main_banner_second_column_category' => '',

            'main_slider_title' => '',
            'main_banner_second_column_title' => '',
            'main_banner_third_column_title' => '',

            'eight_post_section_category' => '',
            'eight_post_section_title' => '',

            'three_column_post_title' => '',
            'three_column_post_category' => '',

            'four_column_post_one_title' => '',
            'four_column_post_one_category' => '',

            'four_column_post_two_title' => '',
            'four_column_post_two_category' => '',

            'four_column_post_three_title' => '',
            'four_column_post_three_category' => '',


      


            'show_sidebar' => 1,
            'show_eight_post' => 1,
            'show_three_post' => 1,
            'show_four_post1' => 1,
            'show_four_post2' => 1,
            'show_four_post3' => 1,

            

            'show_prefooter' => 1,



        );

        $options = get_option('royal_news_magazine_theme_options', $defaults);

        //Parse defaults again - see comments
        $options = wp_parse_args($options, $defaults);

        return $options;
    }
endif;
