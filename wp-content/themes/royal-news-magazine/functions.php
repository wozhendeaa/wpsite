<?php
/**
 * Royal News Magazine functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package royal_news_magazine
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'royal_news_magazine_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function royal_news_magazine_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Royal News Magazine, use a find and replace
		 * to change 'royal-news-magazine' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'royal-news-magazine', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		remove_theme_support( 'widgets-block-editor' );

        add_image_size('royal-news-magazine-blog-thumbnail-img', 600, 400, true);
        add_image_size('royal-news-magazine-blog-single-img', 900, 500, true);
        add_image_size('royal-news-magazine-custom-size', 350, 450, true);


		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'royal-news-magazine' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'royal_news_magazine_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'royal_news_magazine_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function royal_news_magazine_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'royal_news_magazine_content_width', 640 );
}
add_action( 'after_setup_theme', 'royal_news_magazine_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function royal_news_magazine_widgets_init()
{
    register_sidebar([
        'name' => esc_html__('Sidebar', 'royal-news-magazine'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'royal-news-magazine'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title"><span>',
        'after_title' => '</span></h2>',
    ]);
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar([
            'name' => esc_html__('Royal News Magazine Footer Widget', 'royal-news-magazine') . $i,
            'id' => 'royal_news_magazine_footer_' . $i,
            'description' =>
                esc_html__('Shows Widgets in Footer', 'royal-news-magazine') . $i,
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]);
    }
    
}
add_action('widgets_init', 'royal_news_magazine_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function royal_news_magazine_scripts() {
	wp_enqueue_style( 'royal-news-magazine-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'royal-news-magazine-style', 'rtl', 'replace' );

	wp_enqueue_script( 'royal-news-magazine-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'royal_news_magazine_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/royal-news-magazine-nav-walker.php';
/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/customizer-functions.php';
require get_template_directory() . '/inc/royal-news-magazine-customizers-default.php';
require_once( trailingslashit( get_template_directory() ) . 'trt-customize-pro/royal-news-magazine-upgrade/class-customize.php' );

/**
 * Extra Files
 */

require get_template_directory() . '/inc/plugin-activation.php';
require get_template_directory() . '/inc/royal-news-magazine-tgmp-plugins.php';







if (!function_exists('royal_news_magazine_archive_link')) {
    function royal_news_magazine_archive_link($post)
    {
        $year = date('Y', strtotime($post->post_date));
        $month = date('m', strtotime($post->post_date));
        $day = date('d', strtotime($post->post_date));
        $link = home_url('') . '/' . $year . '/' . $month . '?day=' . $day;
        return $link;
    }
}
if (!function_exists('wp_body_open')) {
    function wp_body_open()
    {
        do_action('wp_body_open');
    }
}



if (!function_exists('royal_news_magazine_blank_widget')) {
    function royal_news_magazine_blank_widget()
    {
        echo '<div class="col-md-3">';
        if (is_user_logged_in() && current_user_can('edit_theme_options')) {
            echo '<a href="' .
                esc_url(admin_url('widgets.php')) .
                '" target="_blank"><i class="fa fa-plus-circle"></i> ' .
                esc_html__('Add Footer Widget', 'royal-news-magazine') .
                '</a>';
        }
        echo '</div>';
    }
}

if (!function_exists('royal_news_magazine_get_excerpt')):
    function royal_news_magazine_get_excerpt($post_id, $count)
    {
        $content_post = get_post($post_id);
        $excerpt = $content_post->post_content;

        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);

        $excerpt = preg_replace('/\s\s+/', ' ', $excerpt);
        $excerpt = preg_replace('#\[[^\]]+\]#', ' ', $excerpt);
        $strip = explode(' ', $excerpt);
        foreach ($strip as $key => $single) {
            if (!filter_var($single, FILTER_VALIDATE_URL) === false) {
                unset($strip[$key]);
            }
        }
        $excerpt = implode(' ', $strip);

        $excerpt = substr($excerpt, 0, $count);
        if (strlen($excerpt) >= $count) {
            $excerpt = substr($excerpt, 0, strripos($excerpt, ' '));
            $excerpt = $excerpt . '...';
        }
        return $excerpt;
    }
endif;


/**
 * Enqueue scripts and styles.
 */
function royal_news_magazine_scripts_enqueue()
{
    wp_enqueue_style('royal-news-magazine-style', get_stylesheet_uri());
    wp_enqueue_style('royal-news-magazine-font', royal_news_magazine_font_url(), [], null);
    wp_enqueue_style(
        'bootstrap-css',
        get_template_directory_uri() . '/assets/css/bootstrap.min.css',
        [],
        '1.0'
    );
    wp_enqueue_style(
        'fontawesome-css',
        get_template_directory_uri() . '/assets/css/font-awesome.css',
        [],
        '1.0'
    );
    wp_enqueue_style(
        'slick-css',
        get_template_directory_uri() . '/assets/css/slick.css',
        [],
        '1.0'
    );

    wp_enqueue_style(
        'royal-news-magazine-css',
        get_template_directory_uri() . '/royal-news-magazine.css',
        [],
        '1.0'
    );

    wp_enqueue_style(
        'royal-news-magazine-media-css',
        get_template_directory_uri() . '/assets/css/media-queries-css.css',
        [],
        '1.0'
    );
    wp_enqueue_script(
        'royal-news-magazine-navigation',
        get_template_directory_uri() . '/js/navigation.js',
        ['jquery'],
        '1.0',
        true
    );
    wp_enqueue_script(
        'bootstrap-js',
        get_template_directory_uri() . '/assets/js/bootstrap.min.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_enqueue_script(
        'slick-js',
        get_template_directory_uri() . '/assets/js/slick.min.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_enqueue_script(
        'royal-news-magazine-app',
        get_template_directory_uri() . '/assets/js/main.js',
        ['jquery'],
        '1.0',
        true
    );


    wp_enqueue_script(
        'royal-news-magazine-skip-link-focus-fix',
        get_template_directory_uri() . '/js/skip-link-focus-fix.js',
        ['jquery'],
        '',
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'royal_news_magazine_scripts_enqueue');

function royal_news_magazine_custom_customize_enqueue()
{
    wp_enqueue_style(
        'royal-news-magazine-customizer-style',
        trailingslashit(get_template_directory_uri()) .
            'inc/customizer/css/customizer-control.css'
    );
}

add_action(
    'customize_controls_enqueue_scripts',
    'royal_news_magazine_custom_customize_enqueue'
);



if (!function_exists('royal_news_magazine_font_url')):
    function royal_news_magazine_font_url()
    {
        $fonts_url = '';
        $fonts = [];

        if ('off' !== _x('on', 'PT Serif font: on or off', 'royal-news-magazine')) {
            $fonts[] = 'PT Serif:700';
        }

        if ('off' !== _x('on', 'Nunito font: on or off', 'royal-news-magazine')) {
            $fonts[] = 'Nunito:300,400';
        }
        if ($fonts) {
            $fonts_url = add_query_arg(
                [
                    'family' => urlencode(implode('|', $fonts)),
                ],
                '//fonts.googleapis.com/css'
            );
        }

        return $fonts_url;
    }
endif;