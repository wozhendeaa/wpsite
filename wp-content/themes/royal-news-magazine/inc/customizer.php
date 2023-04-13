<?php
/**
 * Royal News Magazine Theme Customizer
 *
 * @package royal_news_magazine
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
*/
function royal_news_magazine_customize_register($wp_customize)
{
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    $royal_news_magazine_options = royal_news_magazine_theme_options();

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('blogname', [
            'selector' => '.site-title a',
            'render_callback' => 'royal_news_magazine_customize_partial_blogname',
        ]);
        $wp_customize->selective_refresh->add_partial('blogdescription', [
            'selector' => '.site-description',
            'render_callback' => 'royal_news_magazine_customize_partial_blogdescription',
        ]);
    }

    $wp_customize->add_panel('theme_options', [
        'title' => esc_html__('Front Page Options', 'royal-news-magazine'),
        'priority' => 2,
    ]);

    /* Header Section */
    $wp_customize->add_section('header_section', [
        'title' => esc_html__('Header Section', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[facebook]', [
        'type' => 'option',
        'default' => $royal_news_magazine_options['facebook'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_url',
    ]);
    $wp_customize->add_control('royal_news_magazine_theme_options[facebook]', [
        'label' => esc_html__('Facebook Link', 'royal-news-magazine'),
        'type' => 'url',
        'section' => 'header_section',
        'settings' => 'royal_news_magazine_theme_options[facebook]',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[pinterest]', [
        'type' => 'option',
        'default' => $royal_news_magazine_options['pinterest'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_url',
    ]);
    $wp_customize->add_control('royal_news_magazine_theme_options[pinterest]', [
        'label' => esc_html__('pinterest Link', 'royal-news-magazine'),
        'type' => 'url',
        'section' => 'header_section',
        'settings' => 'royal_news_magazine_theme_options[pinterest]',
    ]);







    $wp_customize->add_section('main_banner_section', [
        'title' => esc_html__('Main Banner', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);


    $wp_customize->add_setting('royal_news_magazine_theme_options[main_slider_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('main_slider_title',
    array(
        'label' => esc_html__('Main Banner First Column Title', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'main_banner_section',
        'settings' => 'royal_news_magazine_theme_options[main_slider_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[main_banner_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['main_banner_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[main_banner_category]',
        [
            'section' => 'main_banner_section',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),
            'description' => esc_html__(
                'Max 3 Posts will be shown from the selected Category in Free Version',
                'royal-news-magazine'
            ),
            'settings' => 'royal_news_magazine_theme_options[main_banner_category]',
            'priority' => 1,
        ]
    );



    $wp_customize->add_setting('royal_news_magazine_theme_options[main_banner_second_column_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('main_banner_second_column_title',
    array(
        'label' => esc_html__('Main Banner Second Column Title', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'main_banner_section',
        'settings' => 'royal_news_magazine_theme_options[main_banner_second_column_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[main_banner_second_column_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['main_banner_second_column_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[main_banner_second_column_category]',
        [
            'section' => 'main_banner_section',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),

            'settings' => 'royal_news_magazine_theme_options[main_banner_second_column_category]',
            'priority' => 1,
        ]
    );



    $wp_customize->add_setting('royal_news_magazine_theme_options[main_banner_third_column_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('main_banner_third_column_title',
    array(
        'label' => esc_html__('Main Banner Third Column Title', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'main_banner_section',
        'settings' => 'royal_news_magazine_theme_options[main_banner_third_column_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[main_banner_third_column_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['main_banner_third_column_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[main_banner_third_column_category]',
        [
            'section' => 'main_banner_section',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),

            'settings' => 'royal_news_magazine_theme_options[main_banner_third_column_category]',
            'priority' => 1,
        ]
    );
    //radio box sanitization function
    function royal_news_magazine_sanitize_radio( $input, $setting ){

        //input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
        $input = sanitize_key($input);

        //get the list of possible radio box options
        $choices = $setting->manager->get_control( $setting->id )->choices;

        //return input if valid or return default option
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

    }

    $wp_customize->add_section('eight_post_section', [
        'title' => esc_html__('Eight Post Section', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[show_eight_post]', [
        'type' => 'option',
        'default' => true,
        'default' => $royal_news_magazine_options['show_eight_post'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_checkbox',
    ]);

    $wp_customize->add_control('royal_news_magazine_theme_options[show_eight_post]', [
        'label' => esc_html__('Show Eight Post Section', 'royal-news-magazine'),
        'type' => 'Checkbox',
        'priority' => 1,
        'section' => 'eight_post_section',
    ]);
    $wp_customize->add_setting('royal_news_magazine_theme_options[eight_post_section_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('eight_post_section_title',
    array(
        'label' => esc_html__('Eight Post Section title', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'eight_post_section',
        'settings' => 'royal_news_magazine_theme_options[eight_post_section_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[eight_post_section_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['eight_post_section_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[eight_post_section_category]',
        [
            'section' => 'eight_post_section',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),

            'settings' => 'royal_news_magazine_theme_options[eight_post_section_category]',
            'priority' => 1,
        ]
    );


    function royal_news_magazine_sanitize_checkbox($input)
    {
        if (true === $input) {
            return 1;
        } else {
            return 0;
        }
    }



    $wp_customize->add_section('three_column_post', [
        'title' => esc_html__('Three Column Post Section', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_setting('royal_news_magazine_theme_options[show_three_post]', [
        'type' => 'option',
        'default' => true,
        'default' => $royal_news_magazine_options['show_three_post'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_checkbox',
    ]);

    $wp_customize->add_control('royal_news_magazine_theme_options[show_three_post]', [
        'label' => esc_html__('Show Three Column Post Section', 'royal-news-magazine'),
        'type' => 'Checkbox',
        'priority' => 1,
        'section' => 'three_column_post',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[three_column_post_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('three_column_post_title',
    array(
        'label' => esc_html__('Three Column Section title', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'three_column_post',
        'settings' => 'royal_news_magazine_theme_options[three_column_post_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[three_column_post_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['three_column_post_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[three_column_post_category]',
        [
            'section' => 'three_column_post',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),

            'settings' => 'royal_news_magazine_theme_options[three_column_post_category]',
            'priority' => 1,
        ]
    );




    $wp_customize->add_section('four_column_post_one', [
        'title' => esc_html__('Four Column Post Section - 1', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[show_four_post1]', [
        'type' => 'option',
        'default' => true,
        'default' => $royal_news_magazine_options['show_four_post1'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_checkbox',
    ]);

    $wp_customize->add_control('royal_news_magazine_theme_options[show_four_post1]', [
        'label' => esc_html__('Show Four Column Post Section - 1', 'royal-news-magazine'),
        'type' => 'Checkbox',
        'priority' => 1,
        'section' => 'four_column_post_one',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[four_column_post_one_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('four_column_post_one_title',
    array(
        'label' => esc_html__('Four Column Section title -1 ', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'four_column_post_one',
        'settings' => 'royal_news_magazine_theme_options[four_column_post_one_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[four_column_post_one_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['four_column_post_one_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[four_column_post_one_category]',
        [
            'section' => 'four_column_post_one',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),

            'settings' => 'royal_news_magazine_theme_options[four_column_post_one_category]',
            'priority' => 1,
        ]
    );


    $wp_customize->add_section('four_column_post_two', [
        'title' => esc_html__('Four Column Post Section - 2', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[show_four_post2]', [
        'type' => 'option',
        'default' => true,
        'default' => $royal_news_magazine_options['show_four_post2'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_checkbox',
    ]);

    $wp_customize->add_control('royal_news_magazine_theme_options[show_four_post2]', [
        'label' => esc_html__('Show Four Column Post Section - 2', 'royal-news-magazine'),
        'type' => 'Checkbox',
        'priority' => 1,
        'section' => 'four_column_post_two',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[four_column_post_two_title]',
    array(
        'type' => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    )
);
$wp_customize->add_control('four_column_post_two_title',
    array(
        'label' => esc_html__('Four Column Section title - 2', 'royal-news-magazine'),
        'type' => 'text',
        'section' => 'four_column_post_two',
        'settings' => 'royal_news_magazine_theme_options[four_column_post_two_title]',
    )
);

    $wp_customize->add_setting(
        'royal_news_magazine_theme_options[four_column_post_two_category]',
        [
            'type' => 'option',
            'sanitize_callback' => 'royal_news_magazine_sanitize_select',
            'default' => $royal_news_magazine_options['four_column_post_two_category'],
        ]
    );

    $wp_customize->add_control(
        'royal_news_magazine_theme_options[four_column_post_two_category]',
        [
            'section' => 'four_column_post_two',
            'type' => 'select',
            'choices' => royal_news_magazine_get_categories_select(),
            'label' => esc_html__('Select Category to show Posts', 'royal-news-magazine'),

            'settings' => 'royal_news_magazine_theme_options[four_column_post_two_category]',
            'priority' => 1,
        ]
    );


   





    $wp_customize->add_section('prefooter_section', [
        'title' => esc_html__('Prefooter Section', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[show_prefooter]', [
        'type' => 'option',
        'default' => true,
        'default' => $royal_news_magazine_options['show_prefooter'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_checkbox',
    ]);

    $wp_customize->add_control('royal_news_magazine_theme_options[show_prefooter]', [
        'label' => esc_html__('Show Prefooter Section', 'royal-news-magazine'),
        'type' => 'Checkbox',
        'priority' => 1,
        'section' => 'prefooter_section',
    ]);


    $wp_customize->add_section('single_page_section', [
        'title' => esc_html__('Article Page Option', 'royal-news-magazine'),
        'panel' => 'theme_options',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_setting('royal_news_magazine_theme_options[show_sidebar]', [
        'type' => 'option',
        'default' => true,
        'default' => $royal_news_magazine_options['show_sidebar'],
        'sanitize_callback' => 'royal_news_magazine_sanitize_checkbox',
    ]);

    $wp_customize->add_control('royal_news_magazine_theme_options[show_sidebar]', [
        'label' => esc_html__('Show Sidebar in Single Article Page', 'royal-news-magazine'),
        'type' => 'Checkbox',
        'priority' => 1,
        'section' => 'single_page_section',
    ]);
}
add_action('customize_register', 'royal_news_magazine_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function royal_news_magazine_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function royal_news_magazine_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function royal_news_magazine_customize_preview_js() {
	wp_enqueue_script( 'royal-news-magazine-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'royal_news_magazine_customize_preview_js' );
