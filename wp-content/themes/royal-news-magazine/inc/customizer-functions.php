<?php


if (!class_exists('WP_Customize_Control')) {
    return null;
}

class royal_news_magazine_Dropdown_Customize_Control extends WP_Customize_Control
{
    public $type = 'select';

    public function render_content()
    {
        $terms = get_terms('category'); ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html(
                $this->label
            ); ?></span>
            
                <select <?php $this->link(); ?>>
                    <option value="none"><?php esc_html_e(
                        'None',
                        'royal-news-magazine'
                    ); ?></option>
                    <?php foreach ($terms as $t) {
                        echo '<option value="' .
                            esc_attr($t->slug) .
                            '"' .
                            selected(
                                $this->value(),
                                esc_attr($t->name),
                                false
                            ) .
                            '>' .
                            esc_attr($t->name) .
                            '</option>';
                    } ?>
                </select>

        </label>

        <?php
    }
}

if (!function_exists('royal_news_magazine_get_categories_select')):
    function royal_news_magazine_get_categories_select()
    {
        $royal_news_magazine_categories = get_categories();
        $results = [];

        if (!empty($royal_news_magazine_categories)):
            $results[''] = __('Select Category', 'royal-news-magazine');
            foreach ($royal_news_magazine_categories as $result) {
                $results[$result->slug] = $result->name;
            }
        endif;
        return $results;
    }
endif;

function royal_news_magazine_sanitize_image($image, $setting)
{
    $type = [
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'tif|tiff' => 'image/tiff',
        'ico' => 'image/x-icon',
    ];
    $file = wp_check_filetype($image, $type);
    return $file['ext'] ? $image : $setting->default;
}

function royal_news_magazine_sanitize_url($url)
{
    return esc_url_raw($url);
}
function royal_news_magazine_sanitize_select($input, $setting)
{
    $input = sanitize_key($input);

    $choices = $setting->manager->get_control($setting->id)->choices;

    return array_key_exists($input, $choices) ? $input : $setting->default;
}

/**
 * Class to create a custom post control
 */
class royal_news_magazine_Post_Dropdown_Custom_Control extends WP_Customize_Control
{
    public $type = 'select';

    public function __construct($manager, $id, $args = [], $options = [])
    {
        $postargs = wp_parse_args($options, ['numberposts' => '-1']);
        $this->posts = get_posts($postargs);

        parent::__construct($manager, $id, $args);
    }

    /**
     * Render the content on the theme customizer page
     */
    public function render_content()
    {
        if (!empty($this->posts)) { ?>
                <label>
                    <span class="customize-post-dropdown"><?php echo esc_html(
                        $this->label
                    ); ?></span>
                    <select name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>">
                    <?php foreach ($this->posts as $post) {


                        echo '<option value="' .
                            $post->ID,
                            esc_attr($post->slug) .
                            '"' .
                            selected(
                                $this->value(),
                                $post->ID, false
                            ) .
                            '>' .
                            $post->post_title .
                            '</option>';
                       
                    } ?>
                    </select>
                </label>
            <?php }
    }
}

if (!function_exists('royal_news_magazine_get_posts_select')):
    function royal_news_magazine_get_posts_select()
    {
        $royal_news_magazine_post = get_posts();
        $postresults = [];

        if (!empty($royal_news_magazine_post)):
            $postresults[''] = __('Select Post', 'royal-news-magazine');
            foreach ($royal_news_magazine_post as $postresult) {
                $postresults[$postresult->slug] = $postresult->name;
            }
        endif;
        return $postresults;
    }
endif;