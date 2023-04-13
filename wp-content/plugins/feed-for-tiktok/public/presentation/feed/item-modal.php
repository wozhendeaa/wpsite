<?php
    $video = $view['item'];
?>
<div class="tik-tok-video modal fade" id="tik-tok-video-<?php echo $video->id; ?>" tabindex="-1" aria-labelledby="tik-tok-video-<?php echo $video->id; ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="container g-0">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-4 col-md-3">
                            <?php if ($view['show_date'] === true) { ?>
                                <small class="created-date"><?php echo date_i18n('F d, Y', $video->createdDate);?></small>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container g-0">
                    <div class="row">
                        <div class="col-7">
                            <video width="576" height="1024" disablePictureInPicture controls muted loop controlsList="nodownload" poster="<?php //echo $video->dynamicCover; ?>">
                                <?php echo sprintf('<source src="%s" type="video/mp4">', $video->source); ?>
                            </video>
                            <div class="text-center">
                                <?php if ($view['show_button'] === true) { ?>
                                    <a href="<?php echo $video->url; ?>" target="_blank">
                                        <button type="button" class="btn-grad" <?php echo isset($view['button_style']) ? $view['button_style'] : ''; ?>><?php echo __('View on tiktok', 'tik-tok-feed'); ?></button>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="row">
                                <div class="col-12">
                                    <?php if ($view['show_description'] === true) { ?>
                                        <div class="description">
                                            <p><?php echo $video->description; ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row text-center justify-content-between counters">
                                <?php if ($view['show_likes'] === true) { ?>
                                    <div class="col-6 col-md-3">
                                        <span class="item">
                                            <i data-feather="heart" <?php echo isset($view['icons_style']) ? $view['icons_style'] : ''; ?>></i><br/><?php echo $video->likes; ?>
                                        </span>
                                    </div>
                                <?php } ?>
                                <?php if ($view['show_comments'] === true) { ?>
                                    <div class="col-6 col-md-3">
                                        <span class="item">
                                            <i data-feather="message-circle" <?php echo isset($view['icons_style']) ? $view['icons_style'] : ''; ?>></i><br/><?php echo $video->comments; ?>
                                        </span>
                                    </div>
                                <?php } ?>
                                <?php if ($view['show_shares'] === true) { ?>
                                    <div class="col-6 col-md-3 mt-3 mt-sm-0">
                                        <span class="item">
                                            <i data-feather="share-2" <?php echo isset($view['icons_style']) ? $view['icons_style'] : ''; ?>></i><br/><?php echo $video->shares; ?>
                                        </span>
                                    </div>
                                <?php } ?>
                                <?php if ($view['show_views'] === true) { ?>
                                    <div class="col-6 col-md-3 mt-3 mt-sm-0">
                                        <span class="item">
                                            <i data-feather="eye" <?php echo isset($view['icons_style']) ? $view['icons_style'] : ''; ?>></i><br/><?php echo $video->plays; ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="row bottom-arrows">
                                <div class="col-12">
                                    <?php if ($view['prev_button'] === true) { ?>
                                        <svg data-modal-id="tik-tok-video-<?php echo $video->id; ?>" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="prev-tik-tok-modal bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                                            <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                                        </svg>
                                    <?php } ?>
                                    <?php if ($view['next_button'] === true) { ?>
                                        <svg data-modal-id="tik-tok-video-<?php echo $video->id; ?>" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="next-tik-tok-modal bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                                            <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                                        </svg>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
