<?php

namespace QuadLayers\TTF\Models;

use QuadLayers\TTF\Models\Base as Model;

/**
 * Models_Settings Class
 */
class Settings extends Model {

	protected $table = 'tiktok_feed_settings';

	protected function get_args() {
		return array(
			'flush'             => false,
			'spinner_image_url' => '',
		);
	}

	public function get() {
		$settings = wp_parse_args( $this->get_all(), $this->get_args() );
		return $settings;
	}

	public function save( $settings_data = null ) {
		return $this->save_all( $settings_data );
	}

	public function delete_table() {
		$this->delete_all();
	}

}
