<div class="user-profile-wrapper" <?php echo isset($view['general_style']) ? $view['general_style'] : ''; ?>>
    <div class="profile-card js-profile-card">
        <div class="profile-card__img" <?php echo isset($view['image_profile_style']) ? $view['image_profile_style'] : ''; ?>>
            <img src="<?php echo $view['avatar']; ?>" alt="profile card">
        </div>

        <div class="profile-card__cnt js-profile-cnt">
            <div class="profile-card__name">
                <?php echo sprintf('<a href="%s" target="_blank">%s</a>', $view['url'], $view['unique_id']); ?></div>
            <div class="profile-card__txt">
                <?php echo $view['nickname']; ?>
                <?php if ($view['is_verified'] === true) { ?>
                    <span class="profile-card-social__item verified" >
                        <span class="icon-font">
                            <i data-feather="check"></i>
                        </span>
                    </span>
                <?php } ?>
                <?php if ($view['show_profile_description'] === true) { ?>
                    <p style="font-size: 14px;">
                        <?php echo $view['description']; ?>
                    </p>
                <?php } ?>
            </div>

            <div class="profile-card-inf">
                <?php if ($view['show_following'] === true) { ?>
                    <div class="profile-card-inf__item">
                        <div class="profile-card-inf__title"><?php echo $view['following']; ?></div>
                        <div class="profile-card-inf__txt"><?php echo __('Following', 'tik-tok-feed'); ?></div>
                    </div>
                <?php } ?>

                <?php if ($view['show_followers'] === true) { ?>
                    <div class="profile-card-inf__item">
                        <div class="profile-card-inf__title"><?php echo $view['followers']; ?></div>
                        <div class="profile-card-inf__txt"><?php echo __('Followers', 'tik-tok-feed'); ?></div>
                    </div>
                <?php } ?>

                <?php if ($view['show_likes'] === true) { ?>
                    <div class="profile-card-inf__item">
                        <div class="profile-card-inf__title"><?php echo $view['likes']; ?></div>
                        <div class="profile-card-inf__txt"><?php echo __('Likes', 'tik-tok-feed'); ?></div>
                    </div>
                <?php } ?>

                <?php if ($view['show_videos'] === true) { ?>
                    <div class="profile-card-inf__item">
                        <div class="profile-card-inf__title"><?php echo $view['videos']; ?></div>
                        <div class="profile-card-inf__txt"><?php echo __('Videos', 'tik-tok-feed'); ?></div>
                    </div>
                <?php } ?>
            </div>

            <?php if ($view['show_follow_button'] === true) { ?>
                <div class="profile-card-ctr">
                    <?php echo sprintf('<a href="%s" target="_blank"><button class="profile-card__button button--orange" %s>%s</button></a>', $view['url'], isset($view['follow_button_style']) ? $view['follow_button_style'] : '', __('Follow', 'tik-tok-feed')); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>