<?php /* Lang options */
add_filter("mobile_api_language_options","mobile_api_language_options");
function mobile_api_language_options($options) {
	$lang_setting = mobile_api_language_vars();

	$unset = array("loadingUpdates","baseUrl","baseUrlTitle","baseUrlDesc","emptyBaseUrl","alreadyBaseUrl","layout","pullScreen","pullScreenSubtitle","poweredBy","cantOpenUrl","tapsLeft","devModeActive","version","yourVersionUpToDate","yourVersionNotUpToDate","upgradeHint");

	foreach ($lang_setting as $key => $value) {
		if (!in_array($key,$unset)) {
			$options[] = array(
				'name' => ucwords($key),
				'id'   => strtolower("lang_".$key),
				'std'  => trim($value),
				'type' => 'text',
			);
		}
	}

	return $options;
}?>