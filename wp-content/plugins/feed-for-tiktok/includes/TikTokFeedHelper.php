<?php

namespace TikTokFeed\Includes;

trait TikTokFeedHelper
{
    /**
     * @param string $path
     * @param array $args
     * @return false|string
     */
    public function renderView(string $path, array $view)
    {
        ob_start();
        include($path);
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }

    public function myPrint($var)
    {
        echo '<pre>' . print_r($var, true) . '</pre>';
    }

    public function restyleCount($input)
    {
        $input = number_format((int)$input);
        $inputCount = substr_count($input, ',');

        if ($inputCount != '0') {
            if ($inputCount == '1') {
                return substr($input, 0, -4) . 'k+';
            } elseif ($inputCount == '2') {
                return substr($input, 0, -8) . 'mil+';
            } elseif ($inputCount == '3') {
                return substr($input, 0,  -12) . 'bil+';
            } else {
                return $input;
            }
        } else {
            return $input;
        }
    }
}