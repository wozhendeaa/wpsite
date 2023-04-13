<?php

class wpFCrossPostUtils {

    public static function wpdiscuzMediaUploder($comment_id) {
        $wpdiscuzVersion = get_option('wc_plugin_version', '');
        if (version_compare($wpdiscuzVersion, "7.0.0", ">=")) {
            $attachments = get_comment_meta($comment_id, 'wmu_attachments', true);
            if ($attachments) {
                $images = !empty($attachments['images']) ? $attachments['images'] : array();
                $videos_audios = !empty($attachments['videos']) ? $attachments['videos'] : array();
                $files = !empty($attachments['files']) ? $attachments['files'] : array();
                if ($images) {
                    echo '<div class="wpdiscuz-media-images">';
                    foreach ($images as $image_id) {
                        $image_src_full = wp_get_attachment_image_src($image_id, 'full');
                        $image_src_thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail');
                        $title = esc_attr(get_the_title($image_id));
                        echo '<div class="wpfa-item wpfa-img"><a href="' . $image_src_full[0] . '" data-gallery="#wpf-content-blueimp-gallery" title="' . $title . '"><img src="' . $image_src_thumbnail[0] . '" style="max-width:' . $image_src_thumbnail[1] . 'px; max-height:' . $image_src_thumbnail[1] . 'px;"  alt="' . $title . '" /></a></div>';
                    }
                    echo '</div>';
                }
                if ($videos_audios) {
                    echo '<div class="wpdiscuz-media-videos-audios">';
                    foreach ($videos_audios as $mfile_id) {
                        $mfile_url = wp_get_attachment_url($mfile_id);
                        $mime_type = get_post_mime_type($mfile_id);
                        if (strpos($mime_type, 'video/') !== FALSE) {
                            echo '<div class="wpfa-item wpfa-video"><video src="' . $mfile_url . '" controls></video></div>';
                        } elseif (strpos($mime_type, 'audio/') !== FALSE) {
                            echo '<div class="wpfa-item wpfa-audio"><audio src="' . $mfile_url . '" controls ></audio></div>';
                        }
                    }
                    echo '</div>';
                }

                if ($files) {
                    echo '<div class="wpdiscuz-media-files">';
                    foreach ($files as $file_id) {
                        $file_url = wp_get_attachment_url($file_id);
                        $title = get_the_title($file_id);
                        echo '<div class="wpforo-attached-file"><a class="wpforo-default-attachment" href="' . $file_url . '" target="_blank"><i class="fas fa-paperclip"></i>&nbsp;' . $title . '</a></div>';
                    }
                    echo '</div>';
                }
            }
        } else if (class_exists('WpdiscuzMediaUploader')) {
            $images = trim(get_comment_meta($comment_id, 'attachment_images', true), ' ,');
            $videos_audios = trim(get_comment_meta($comment_id, 'attachment_videos_audios', true), ' ,');
            $files = trim(get_comment_meta($comment_id, 'attachment_files', true), ' ,');
            if ($images) {
                echo '<div class="wpdiscuz-media-images">';
                $images = explode(',', $images);
                foreach ($images as $image_id) {
                    $image_src_full = wp_get_attachment_image_src($image_id, 'full');
                    $image_src_thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail');
                    $title = esc_attr(get_the_title($image_id));
                    echo '<div class="wpfa-item wpfa-img"><a href="' . $image_src_full[0] . '" data-gallery="#wpf-content-blueimp-gallery" title="' . $title . '"><img src="' . $image_src_thumbnail[0] . '" style="max-width:' . $image_src_thumbnail[1] . 'px; max-height:' . $image_src_thumbnail[1] . 'px;"  alt="' . $title . '" /></a></div>';
                }
                echo '</div>';
            }
            if ($videos_audios) {
                echo '<div class="wpdiscuz-media-videos-audios">';
                $videos_audios = explode(',', $videos_audios);
                foreach ($videos_audios as $mfile_id) {
                    $mfile_url = wp_get_attachment_url($mfile_id);
                    $mime_type = get_post_mime_type($mfile_id);
                    if (strpos($mime_type, 'video/') !== FALSE) {
                        echo '<div class="wpfa-item wpfa-video"><video src="' . $mfile_url . '" controls></video></div>';
                    } elseif (strpos($mime_type, 'audio/') !== FALSE) {
                        echo '<div class="wpfa-item wpfa-audio"><audio src="' . $mfile_url . '" controls ></audio></div>';
                    }
                }
                echo '</div>';
            }

            if ($files) {
                echo '<div class="wpdiscuz-media-files">';
                $files = explode(',', $files);
                foreach ($files as $file_id) {
                    $file_url = wp_get_attachment_url($file_id);
                    $title = get_the_title($file_id);
                    echo '<div class="wpforo-attached-file"><a class="wpforo-default-attachment" href="' . $file_url . '" target="_blank"><i class="fas fa-paperclip"></i>&nbsp;' . $title . '</a></div>';
                }
                echo '</div>';
            }
        }
    }

    public static function getRelatedBoardIDs($postID) {
        $boardids = get_post_meta($postID, wpForoCrossPostingOptions::WPFBOARDIDS, true);
        if (!$boardids) {
            $boardids = [];
            $boardids[] = 0;
        }

        return $boardids;
    }

}
