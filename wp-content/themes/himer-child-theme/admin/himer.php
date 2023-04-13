<?php /* Activate gender */
add_filter("wpqa_activate_male_female","himer_activate_male_female");
add_filter("mobile_api_activate_male_female","himer_activate_male_female");
function himer_activate_male_female() {
	$gender_answers = himer_options("gender_answers");
	return ($gender_answers == "on"?true:false);
}
/* Config */
add_filter("mobile_api_config_array","himer_config_array",1,2);
function himer_config_array($array,$mobile_api_options) {
	$activate_male_female = apply_filters("wpqa_activate_male_female",false);
	if ($activate_male_female == true) {
		$mobile_setting_home = (isset($mobile_api_options["mobile_setting_home"])?$mobile_api_options["mobile_setting_home"]:"");
		$answer_gender_mobile = (isset($mobile_api_options["answer_gender_mobile"])?$mobile_api_options["answer_gender_mobile"]:"");
		$answer_all_tab_mobile = (isset($mobile_api_options["answer_all_tab_mobile"])?$mobile_api_options["answer_all_tab_mobile"]:"");
		if ($answer_gender_mobile == "on") {
			if ($answer_all_tab_mobile == "on") {
				$array["archives"]["single"]["allAnswers"] = "true";
			}
			$array["archives"]["single"]["genderAnswers"] = "true";
		}
		$array["archives"]["single"]["showUnspecifiedGenders"] = "true";
		
		$array["tabs"]["options"]["genderAnswersCount"] = "true";
		$array["archives"]["category"]["options"]["genderAnswersCount"] = "true";
		$array["archives"]["questions"]["options"]["genderAnswersCount"] = "true";
		$array["archives"]["search"]["options"]["genderAnswersCount"] = "true";
		$array["archives"]["favorites"]["options"]["genderAnswersCount"] = "true";
		$array["archives"]["followed"]["options"]["genderAnswersCount"] = "true";
		$array["archives"]["groups"]["tabsOptions"]["genderAnswersCount"] = "true";
		$array["archives"]["groups"]["single"]["genderAnswersCount"] = "true";
		$array["archives"]["groups"]["single"]["showUnspecifiedGenders"] = "true";
		
		$array["icons"]["male"] = "fa-male";
		$array["icons"]["female"] = "fa-female";
		$array["styling"]["ThemeMode.light"]["maleColor"] = "2e6ffd";
		$array["styling"]["ThemeMode.light"]["femaleColor"] = "ff0084";
		$array["styling"]["ThemeMode.light"]["otherGenderColor"] = "272930";
		$array["styling"]["ThemeMode.dark"]["maleColor"] = "2e6ffd";
		$array["styling"]["ThemeMode.dark"]["femaleColor"] = "ff0084";
		$array["styling"]["ThemeMode.dark"]["otherGenderColor"] = "272930";
	}
	return $array;
}
/* Language */
add_filter("mobile_api_language_array","himer_language_array");
function himer_language_array($array) {
	$activate_male_female = apply_filters("wpqa_activate_male_female",false);
	if ($activate_male_female == true) {
		$array["genderNotSpecified"] = "Other";
	}
	return $array;
}
/* Author meta */
add_filter("mobile_api_author_meta","himer_author_meta",1,2);
function himer_author_meta($meta,$user_id) {
	$activate_male_female = apply_filters("wpqa_activate_male_female",false);
	if ($activate_male_female == true) {
		$gender = get_the_author_meta('gender',$user_id);
		$return = '';
		if ($gender == 'male' || $gender == 1) {
			$return = 'him-user';
		}else if ($gender == 'female' || $gender == 2) {
			$return = 'her-user';
		}else if ($gender == 'other' || $gender == 3) {
			$return = 'other-user';
		}
		if ($return != "") {
			$meta = array_merge($meta,array("gender_meta" => $return));
		}
	}
	return $meta;
}?>