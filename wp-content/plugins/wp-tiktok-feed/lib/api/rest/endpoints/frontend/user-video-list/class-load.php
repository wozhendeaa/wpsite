<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Frontend\User_Video_List;

use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;
use QuadLayers\TTF\Models\Accounts as Models_Account;
use QuadLayers\TTF\Models\Feeds as Models_Feed;
use QuadLayers\TTF\Api\Fetch\User_Video_List\Get as API_Fetch_User_Video_List;
use QuadLayers\TTF\Utils\Cache as Cache;


/**
 * API_Rest_Frontend_User_Video_List Class
 */
class Load extends Base {

	protected static $rest_route = 'frontend/user-video-list';

	protected $media_cache_engine;
	protected $media_cache_key = 'feed';

	public function callback( \WP_REST_Request $request ) {

		$body = json_decode( $request->get_body(), true );

		if ( ! isset( $body['feedSettings'] ) ) {
			$message = array(
				'message' => esc_html__( 'Bad Request, feed settings not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		$feed        = $body['feedSettings'];
		$create_time = $body['createTime'];

		$cursor = 0;

		if ( 'null' !== $create_time ) {
			$cursor = intval( $create_time . '000' );
		}

		// Get cache data and return it if exists.
		// Set prefix to cache.
		$feed_md5              = md5( wp_json_encode( $feed ) );
		$media_complete_prefix = "{$this->media_cache_key}_{$feed_md5}_{$cursor}";

		$this->media_cache_engine = new Cache( 6, true, $media_complete_prefix );

		// Get cached user media data.
		$response = $this->media_cache_engine->get( $media_complete_prefix );

		// Check if $response has data, if it have return it.
		if ( ! empty( $response['response'] ) ) {
			return $response['response'];
		}

		if ( ! isset( $feed['open_id'] ) ) {
			$message = array(
				'message' => esc_html__( 'Feed open_id not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}
		$open_id = $feed['open_id'];

		$models_account = new Models_Account();
		$account        = $models_account->get_account( $open_id );

		if ( ! isset( $account['access_token'] ) ) {
			return $this->handle_response(
				array(
					'code'    => 412,
					'message' => sprintf( esc_html__( 'Account id %s not found', 'wp-tiktok-feed' ), $open_id ),
				)
			);
		}

		$access_token = $account['access_token'];

		if ( ! isset( $feed['source'] ) ) {
			$message = array(
				'message' => esc_html__( 'Feed limit not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		$limit = $feed['limit'];

		$fetch_user_video_list = new API_Fetch_User_Video_List();
		$args                  = array(
			'open_id'      => $open_id,
			'access_token' => $access_token,
			'cursor'       => $cursor,
			'max_count'    => intval( $limit ),
		);

		$response = $fetch_user_video_list->get_data( $args );
		// Check if response is an error and return it.
		if ( isset( $response['message'] ) && isset( $response['code'] ) ) {
			return $this->handle_response( $response );
		}

		if ( ! isset( $feed['hide_carousel_feed'] ) ) {
			$message = array(
				'message' => esc_html__( 'Feed hide carousel feed not found.', 'wp-tiktok-feed' ),
				'code'    => '400',
			);
			return $this->handle_response( $message );
		}

		// Delete carousel feed items if is setted true $feed['hide_carousel_feed']
		$hide_carousel_feed = isset( $feed['hide_carousel_feed'] ) ? $feed['hide_carousel_feed'] : true;

		if ( $hide_carousel_feed ) {

			$final_response = array();

			foreach ( $response as $item ) {

				if ( '' !== $item['cover_image_url'] && 0 !== $item['height'] && 0 !== $item['width'] && '' !== $item['embed_link'] ) {
					$final_response[] = $item;
				}
			};

			$response = $final_response;

		}

		// Update user media data cache and return it.
		if ( ! QLTTF_DEVELOPER ) {
			$this->media_cache_engine->update( $media_complete_prefix, $response );
		}

		return $this->handle_response( $response );
	}

	public static function get_rest_args() {
		return array();
	}

	public static function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}

	public function get_rest_permission() {
		return true;
	}
}
