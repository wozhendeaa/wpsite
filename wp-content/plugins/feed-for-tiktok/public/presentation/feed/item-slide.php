<?php
    $video = $view['item'];

    $img = sprintf('<img class="img-fluid img-slide" data-bs-toggle="modal" data-bs-target="#tik-tok-video-%s" onmouseover="this.src=\'%s\'" onmouseout="this.src=\'%s\'" src="%s"/>', $video->id, $video->dynamicCover, $video->originCover, $video->originCover);

    if (isset($view['cover_type']) === true) {
        if ($view['cover_type'] === 'static') {
            $img = sprintf('<img class="img-fluid img-slide" data-bs-toggle="modal" data-bs-target="#tik-tok-video-%s" src="%s"/>', $video->id, $video->originCover);
        } elseif ($view['cover_type'] === 'dynamic') {
            $img = sprintf('<img class="img-fluid img-slide" data-bs-toggle="modal" data-bs-target="#tik-tok-video-%s" src="%s"/>', $video->id, $video->dynamicCover);
        }
    }

echo '<div style="position: relative">';
    echo $img;

    if ($view['cover_show_views'] === true) {
        echo sprintf('<div class="views-cover" %s>', isset($view['number_of_views_style']) ? $view['number_of_views_style'] : '');
            echo sprintf('<i data-feather="play-circle"></i> %s', $video->plays);
        echo '</div>';
    }
echo '</div>';