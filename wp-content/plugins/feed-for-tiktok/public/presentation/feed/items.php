<div class="tik-tok-feed-wrap tik-tok-bootstrap" <?php echo isset($view['general_style']) ? $view['general_style'] : ''; ?>>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-4 col-md-2 avatar">
                <?php echo sprintf('<img %s class="img-fluid rounded-circle" src="%s"/>', isset($view['profile_border_style']) ? $view['profile_border_style'] : '', $view['avatar']); ?>
            </div>
            <div class="col-6 col-md-3 details">
                <div class="row g-0">
                    <div class="col-4">
                        <?php echo sprintf('<a href="%s" target="_blank"><h2>%s</h2></a>', $view['url_profile'], $view['username']); ?>
                    </div>
                    <?php if ($view['is_verified'] === true) { ?>
                        <div class="col-2">
                            <i class="is-verified" data-feather="check"></i>
                        </div>
                    <?php } ?>
                </div>
                <div class="row g-0">
                    <div class="col-12">
                        <h4><?php echo $view['nickname']; ?></h4>
                        <?php if ($view['show_followers'] === true) {
                            echo sprintf('<span>%s %s</span>', $view['followers'], __('Followers', 'tik-tok-feed'));
                        } ?>
                        <?php if ($view['show_follow_button'] === true) {
                            echo sprintf('<a href="%s" target="_blank"><span class="badge bg-dark" %s>%s</span></a>', $view['url_profile'], isset($view['follow_button_style']) ? $view['follow_button_style'] : '', __('Follow', 'tik-tok-feed'));
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($view['show_profile_description'] === true) {
        echo sprintf('<p class="user-description">%s</p>', nl2br($view['description']));
    } ?>
    <?php if ($view['carousel_type'] === 'horizontally') { ?>
        <div class="arrows text-right pb-3">
            <button class="left"><i data-feather="arrow-left-circle" <?php echo isset($view['arrows_style']) ? $view['arrows_style']: ''; ?>></i></button>
            <button class="right"><i data-feather="arrow-right-circle" <?php echo isset($view['arrows_style']) ? $view['arrows_style'] : ''; ?>></i></button>
        </div>
    <?php } ?>
    <?php if ($view['carousel_type'] === 'vertical') { ?>
        <div class="arrow text-center pb-3">
            <button class="up"><i data-feather="arrow-up-circle" <?php echo isset($view['arrows_style']) ? $view['arrows_style']: ''; ?>></i></button>
        </div>
    <?php } ?>
    <div class="tik-tok-feed-carousel">
        <?php echo $view['slides']; ?>
    </div>
    <?php if ($view['carousel_type'] === 'vertical') { ?>
        <div class="arrow text-center pt-3">
            <button class="down"><i data-feather="arrow-down-circle" <?php echo isset($view['arrows_style']) ? $view['arrows_style']: ''; ?>></i></button>
        </div>
    <?php } ?>
    <?php echo $view['modals']; ?>
    <input type="hidden" id="carousel-type" value="<?php echo $view['carousel_type']; ?>"/>
</div>