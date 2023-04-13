<?php

namespace QUADLAYERS\TIKTOK\Api\Fetch\UserProfile;

use QUADLAYERS\TIKTOK\Api\Fetch\Base as Base;

/**
 * API_Fetch_User_Profile Class extends Base
 */
class Get extends Base {

	/**
	 * Function to parse response to usable data.
	 *
	 * @param array $response Raw response from tiktok.
	 * @return array
	 */
	public function response_to_data( $response = null ) {
		if ( isset( $response['data']['user'] ) ) {
			$response = array(
				'username'  => $response['data']['user']['username'],
				'nickname'  => $response['data']['user']['display_name'],
				'link'      => $response['data']['user']['profile_deep_link'],
				'biography' => $response['data']['user']['bio_description'],
				'avatar'    => $response['data']['user']['avatar_url_100'],
			);
		}
		return $response;
	}

	/**
	 * Function to build query url.
	 *
	 * @return string
	 */
	public function get_url() {
		$url = $this->fetch_url . 'userProfile';
		return $url;
	}
}
