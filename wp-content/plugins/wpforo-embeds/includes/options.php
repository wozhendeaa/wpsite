<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
?>

<input type="hidden" name="wpfaction" value="wpforo_embeds_settings_save">

<style type="text/css">
    #vk_register_api {
        border: 1px solid black;
    }

    #vk_no_api {
        color: red;
        float: left;
        margin-left: 5px;
        cursor: pointer;
    }
</style>
<table class="wpf-addon-table">
    <tr>
        <th scope="row" style="width:60%;">
            <label><?php _e( 'Embed video player sizes', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'Set this option value 0 if you want to set width/height "AUTO"', 'wpforo_embed' ) ?></p>
        </th>
        <td style="min-width:250px;">
            <p>
                <label style="height:30px; display:inline-block; width:70px; margin:0; vertical-align:middle;" for="video_width"><?php _e( 'Width', 'wpforo_embed' ) ?></label>
                <input style="height:30px; width:80px; margin:0; vertical-align:middle;" id="video_width" class="wpf-field-small" type="number" name="wpforo_embed_options[video_width]" value="<?php wpfo( WPF_EMBED()->options['video_width'] ) ?>"/>&nbsp;
                <select title="type" style="height:30px; margin:0; vertical-align:middle;" name="wpforo_embed_options[video_width_type]">
                    <option value="%" <?php echo( WPF_EMBED()->options['video_width_type'] == '%' ? 'selected="selected"' : '' ) ?>>%</option>
                    <option value="px" <?php echo( WPF_EMBED()->options['video_width_type'] == 'px' ? 'selected="selected"' : '' ) ?>>px</option>
                </select>
            </p>
            <p>
                <label style="height:30px; display:inline-block; width:70px; margin:0; vertical-align:middle;" for="video_height"><?php _e( 'Height', 'wpforo_embed' ) ?></label>
                <input style="height:30px; width:80px; margin:0; vertical-align:middle;" id="video_height" class="wpf-field-small" type="number" name="wpforo_embed_options[video_height]" value="<?php wpfo( WPF_EMBED()->options['video_height'] ) ?>"/>&nbsp;
                <input type="hidden" name="wpforo_embed_options[video_height_type]" value="px">
                <span>px</span>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row" style="width:60%;">
            <label><?php _e( 'Maximum number of embedded content per post', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'Set this option value 0 to remove this limit', 'wpforo_embed' ) ?></p>
        </th>
        <td style="min-width:250px;">
            <p>
                <label style="height:30px; display:inline-block; width:70px; margin:0; vertical-align:middle;" for="max_per_post"><?php _e( 'Limit', 'wpforo_embed' ) ?></label>
                <input style="height:30px; width:80px; margin:0; vertical-align:middle;" id="max_per_post" class="wpf-field-small" type="number" min="0" name="wpforo_embed_options[max_per_post]" value="<?php wpfo( WPF_EMBED()->options['max_per_post'] ) ?>"/>&nbsp;
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'Enable YouTube privacy-enhanced mode', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'When you turn on privacy-enhanced mode, YouTube won\'t store information about visitors on your website unless they play the video. <a href="https://support.google.com/youtube/answer/171780?visit_id=1-636620943329167783-4190845816&rd=1">Read more here</a>', 'wpforo_embed' ); ?></p>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_embed_options[youtube_pe_mode]" id="wpf_youtube_pe_mode_1" <?php wpfo_check( WPF_EMBED()->options['youtube_pe_mode'], 1 ); ?>><label for="wpf_youtube_pe_mode_1"><?php _e( 'On', 'wpforo' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_embed_options[youtube_pe_mode]" id="wpf_youtube_pe_mode_0" <?php wpfo_check( WPF_EMBED()->options['youtube_pe_mode'], 0 ); ?>><label for="wpf_youtube_pe_mode_0"><?php _e( 'Off', 'wpforo' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?php
            $vk_img = '';
            foreach( WPF_EMBED()->default->embeds as $embed => $embed_status ) {
                if( $embed === 'custom' ) continue;
                if( wpfkey( WPF_EMBED()->embeds, $embed ) ) $embed_status = WPF_EMBED()->embeds[ $embed ];
                $img = '';
                $src = '/assets/icons/' . $embed . '.png';
                if( file_exists( WPFOROEMBED_DIR . $src ) ) {
                    $img = '<img src="' . WPFOROEMBED_URL . $src . '" title="' . $embed . '" style="vertical-align:middle; margin-right:5px; max-height: 20px;">';
                }
                if( $embed === 'vk.com' ) $vk_img = $img;
                ?>
                <div style="width:31%; min-width:155px; margin-right:2%; margin-bottom:2px; box-sizing:border-box; padding:2px 5px; float:left; background:#F9F9F9; border-bottom:1px solid #ddd; ">
                    <div style="float:left;">
                        <label for="<?php echo md5( $embed ) ?>" style="cursor:pointer;"><?php echo $img ?><span><?php echo ucfirst( preg_replace( '|\.[^.]+$|i', '', $embed ) ); ?></span></label>
                    </div>
                    <?php if( $embed === 'vk.com' && ! WPF_EMBED()->options['vk_access_token'] ) echo '<div id="vk_no_api">configure API key</div>' ?>
                    <div style="float:right;">
                        <input id="<?php echo md5( $embed ) ?>" <?php wpfo_check( $embed_status, 1 ); ?> type="checkbox" name="wpforo_embed_options[embeds][<?php echo $embed ?>]" value="1" <?php if( $embed == 'vk.com' && ! WPF_EMBED()->options['vk_access_token'] ) echo 'disabled' ?> />
                    </div>
                    <div style="clear:both"></div>
                </div>
                <?php
            }
            ?>
            <div style="clear:both;"></div>
            <p></p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'Embed Website URLs', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'This option embeds all URLs using website meta data information. You just need to put website URL in post content to display a nice widget with the website data.', 'wpforo_embed' ); ?></p>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_embed_options[wpf_embed_is_on]" id="wpf_wpf_embed_is_on_1" <?php wpfo_check( WPF_EMBED()->options['wpf_embed_is_on'], 1 ); ?>><label for="wpf_wpf_embed_is_on_1"><?php _e( 'On', 'wpforo' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_embed_options[wpf_embed_is_on]" id="wpf_wpf_embed_is_on_0" <?php wpfo_check( WPF_EMBED()->options['wpf_embed_is_on'], 0 ); ?>><label for="wpf_wpf_embed_is_on_0"><?php _e( 'Off', 'wpforo' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'Embed File URLs', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'This option embeds internal and external File URLs. You just need to put File Full URL in post content to display a image, video player, audio player or just link for other types.', 'wpforo_embed' ); ?></p>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_embed_options[embed_file_urls]" id="wpf_embed_file_urls_1" <?php wpfo_check( WPF_EMBED()->options['embed_file_urls'], 1 ); ?>><label for="wpf_embed_file_urls_1"><?php _e( 'On', 'wpforo' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_embed_options[embed_file_urls]" id="wpf_embed_file_urls_0" <?php wpfo_check( WPF_EMBED()->options['embed_file_urls'], 0 ); ?>><label for="wpf_embed_file_urls_0"><?php _e( 'Off', 'wpforo' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'oEmbed Functions', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'This option enables the oEmbed discovery ability. oEmbed supports hundreds of content providers. Video, audio, photos, products, and more—embed the content your users crave. you just need to put item share URL in post content to get a nice widget with all content.', 'wpforo_embed' ); ?></p>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_embed_options[oembed_is_on]" id="wpf_oembed_is_on_1" <?php wpfo_check( WPF_EMBED()->options['oembed_is_on'], 1 ); ?>><label for="wpf_oembed_is_on_1"><?php _e( 'On', 'wpforo' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_embed_options[oembed_is_on]" id="wpf_oembed_is_on_0" <?php wpfo_check( WPF_EMBED()->options['oembed_is_on'], 0 ); ?>><label for="wpf_oembed_is_on_0" onclick="jQuery('#wpf_own_wpposts_embed_0').trigger('click');"><?php _e( 'Off', 'wpforo' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'oEmbed current domain content', 'wpforo_embed' ) ?></label>
            <p class="wpf-info"><?php _e( 'This option allow to embed current domain blog contents.', 'wpforo_embed' ); ?></p>
        </th>
        <td>
            <div class="wpf-switch-field">
                <input type="radio" value="1" name="wpforo_embed_options[own_wpposts_embed]" id="wpf_own_wpposts_embed_1" <?php wpfo_check( WPF_EMBED()->options['own_wpposts_embed'], 1 ); ?>><label for="wpf_own_wpposts_embed_1" onclick="jQuery('#wpf_oembed_is_on_1').trigger('click');"><?php _e( 'On', 'wpforo' ); ?></label> &nbsp;
                <input type="radio" value="0" name="wpforo_embed_options[own_wpposts_embed]" id="wpf_own_wpposts_embed_0" <?php wpfo_check( WPF_EMBED()->options['own_wpposts_embed'], 0 ); ?>><label for="wpf_own_wpposts_embed_0"><?php _e( 'Off', 'wpforo' ); ?></label>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <h4 style="margin-top:0;"><?php _e( 'oEmbed Examples:', 'wpforo_embed' ); ?></h4>
            <div style="max-width:35%; height:210px;float:left; margin-bottom:10px;">
                <img alt="" src="<?php echo WPFOROEMBED_URL ?>/assets/screens/wpForo-Embeds-Twitter.png" title="Embed Tweets" style="width:100%;"/>
            </div>
            <div style="max-width:35%; height:210px;float:left; margin-bottom:10px;">
                <img alt="" src="<?php echo WPFOROEMBED_URL ?>/assets/screens/wpForo-Embed-WordPress-and-Websites.png" title="Embed Websites" style="width:100%;"/>
            </div>
            <div style="max-width:35%; height:210px;float:left; margin-bottom:10px;">
                <img alt="" src="<?php echo WPFOROEMBED_URL ?>/assets/screens/wpForo-Embed-SoundCloud.png" title="Embed SoundCloud" style="width:100%;"/>
            </div>
            <div style="max-width:35%; height:210px;float:left; margin-bottom:10px;">
                <img alt="" src="<?php echo WPFOROEMBED_URL ?>/assets/screens/wpForo-Embed-Flicker.png" title="Embed Flicker Album and Photos" style="width:100%;"/>
            </div>
            <div style="clear:both;"></div>
            <p></p>
        </td>
    </tr>
</table>
