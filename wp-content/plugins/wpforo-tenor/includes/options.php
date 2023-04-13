<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

$locales = [
	''      => '-- ' . __( 'Not Specified', 'wpforo_tenor' ) . ' --',
	'pt'    => 'Portuguese (pt)',
	'en'    => 'English (en)',
	'es'    => 'Spanish (es)',
	'it'    => 'Italian (it)',
	'de'    => 'German (de)',
	'ar'    => 'Arabic (ar)',
	'ru'    => 'Russian (ru)',
	'fr'    => 'French (fr)',
	'sq'    => 'Albanian (sq)',
	'sk'    => 'Slovak (sk)',
	'id'    => 'Indonesian (id)',
	'zh-CN' => 'Chinese Simplified (zh-CN)',
	'zh-TW' => 'Chinese Traditional (zh-TW)',
	'ja'    => 'Japanese (ja)',
	'ko'    => 'Korean (ko)',
	'hi'    => 'Hindi (hi)',
	'tr'    => 'Turkish (tr)',
	'nl'    => 'Dutch (nl)',
	'bn'    => 'Bengali (bn)',
	'tl'    => 'Filipino (tl)',
	'he'    => 'Hebrew (he)',
	'fi'    => 'Finnish (fi)',
	'sv'    => 'Swedish (sv)',
	'da'    => 'Danish (da)',
	'cs'    => 'Czech (cs)',
	'pl'    => 'Polish (pl)',
	'ro'    => 'Romanian (ro)',
	'ms'    => 'Malay (ms)',
	'ur'    => 'Urdu (ur)',
	'no'    => 'Norwegian (no)',
	'ca'    => 'Catalan (ca)',
	'el'    => 'Greek (el)',
	'hu'    => 'Hungarian (hu)',
	'th'    => 'Thai (th)',
	'fa'    => 'Farsi (fa)',
	'uk'    => 'Ukrainian (uk)',
	'hr'    => 'Croatian (hr)',
	'vi'    => 'Vietnamese (vi)',
];

$cats_textarea_value = '';
foreach( WPF_TENOR()->options['cats'] as $cat ) {
	$cats_textarea_value .= "{$cat['name']}\n";
	if( $subcats = wpfval( $cat, 'subcategories' ) ) {
		foreach( $subcats as $subcat ) {
			$cats_textarea_value .= "\t{$subcat['name']}\n";
		}
	}
}
?>

<input type="hidden" name="wpfaction" value="wpftenor_settings_save">
<table class="wpf-addon-table" style="table-layout: fixed;">
    <tr>
        <th scope="row">
            <label for="limit"><?php _e( 'Lazy loading items per step in the Gif picking dialog', 'wpforo_tenor' ) ?></label>
        </th>
        <td style="width: 65%;">
            <input style="height:30px; width:80px; margin:0; vertical-align:middle;" id="limit" class="wpf-field-small" type="number" name="wpforo_tenor_options[limit]" value="<?php wpfo( WPF_TENOR()->options['limit'] ) ?>" min="10">&nbsp;
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e( 'Content Filtering Mode', 'wpforo_tenor' ) ?></label>
            <p class="wpf-info"><?php _e( 'If you do not specify a content filter, you will receive results from all possible content filters. <a href="https://tenor.com/gifapi/documentation#contentfilter">Read more here</a>', 'wpforo_tenor' ); ?></p>
        </th>
        <td style="width: 65%;">
            <div class="wpf-switch-field">
                <input type="radio" value="off" name="wpforo_tenor_options[contentfilter]" id="contentfilter_off" <?php checked( WPF_TENOR()->options['contentfilter'], 'off' ); ?>><label for="contentfilter_off"> Off</label> &nbsp;
                <input type="radio" value="low" name="wpforo_tenor_options[contentfilter]" id="contentfilter_low" <?php checked( WPF_TENOR()->options['contentfilter'], 'low' ); ?>><label for="contentfilter_low"> Low</label> &nbsp;
                <input type="radio" value="medium" name="wpforo_tenor_options[contentfilter]" id="contentfilter_medium" <?php checked( WPF_TENOR()->options['contentfilter'], 'medium' ); ?>><label for="contentfilter_medium"> Medium</label> &nbsp;
                <input type="radio" value="high" name="wpforo_tenor_options[contentfilter]" id="contentfilter_high" <?php checked( WPF_TENOR()->options['contentfilter'], 'high' ); ?>><label for="contentfilter_high"> High</label>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="locale"><?php _e( 'Specify default language to interpret search string', 'wpforo_tenor' ) ?></label>
            <p class="wpf-info"><a href="https://tenor.com/gifapi/documentation#localization">Read more here</a></p>
        </th>
        <td style="width: 65%;">
            <select name="wpforo_tenor_options[locale]" id="locale">
                <?php
                foreach( $locales as $iso => $locale ) {
                    printf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        $iso,
                        selected( WPF_TENOR()->options['locale'], $iso, false ),
                        $locale
                    );
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="cats"><?php _e( 'Customize GIF categories with subcategories', 'wpforo_tenor' ) ?></label>
            <p class="wpf-info"><?php _e( 'Do not fill this field if you want to use the default Tenor categories.', 'wpforo_tenor' ); ?></p>
            <p class="wpf-info"><?php _e( 'Category and subcategory tree with two depth. Use a new line for each item, and indent with either tab or space before subcategories.', 'wpforo_tenor' ); ?></p>
            <p class="wpf-info">e.g.</p>
            <p style="border: 1px solid #ebebeb; padding: 5px 10px;" class="wpf-info">cat-1<br>&emsp;subcat-1<br>&emsp;subcat-2<br>&emsp;subcat-3<br>cat-2<br>&emsp;subcat-2-1<br>&emsp;subcat-2-2<br>&emsp;subcat-2-3</p>
        </th>
        <td style="width: 65%;">
            <textarea
                    cols="55"
                    rows="15"
                    id="cats"
                    name="wpforo_tenor_options[cats]"
            ><?php echo $cats_textarea_value ?></textarea>
        </td>
    </tr>
</table>
