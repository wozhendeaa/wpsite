<?php
namespace QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Frontend\UserVideoList;

use QUADLAYERS\TIKTOK\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\TIKTOK\Models\Accounts as Models_Account;
use QUADLAYERS\TIKTOK\Models\Feeds as Models_Feed;
use QUADLAYERS\TIKTOK\Api\Fetch\UserVideoList\Get as API_Fetch_User_VideoList ;
use QUADLAYERS\TIKTOK\Utils\Cache as Cache;


/**
 * API_Rest_Frontend_User_VideoList Class
 */
class Load extends Base {

	protected static $rest_route = 'frontend/user-video-list';

	protected $media_cache_engine;
	protected $media_cache_key = 'feed';

	public function callback( \WP_REST_Request $request ) {

		// $open_id     = $request->get_param( 'open_id' );
		// $limit       = trim( $request->get_param( 'limit' ) );
		$feed_id     = $request->get_param( 'feed_id' );
		$create_time = trim( $request->get_param( 'create_time' ) );

		$cursor = 0;

		if ( 'null' !== $create_time ) {
			$cursor = intval( $create_time . '000' );
		}

		// Get cache data and return it if exists.
		// Set prefix to cache.
		$media_complete_prefix = "{$this->media_cache_key}_{$feed_id}_{$cursor}";

		$this->media_cache_engine = new Cache( 6, true, $media_complete_prefix );

		// Get cached user media data.
		$response = $this->media_cache_engine->get( $media_complete_prefix );

		// Check if $response has data, if it have return it.
		if ( ! empty( $response['response'] ) ) {
			return $response['response'];
		}

		$models_feeds = new Models_Feed();
		$feed         = $models_feeds->get_by_id( $feed_id );

		// Check if exist a feed related to feed_id setted by param, if it is not return error.
		if ( ! $feed ) {
			return $this->handle_response(
				array(
					'code'    => 412,
					'message' => sprintf( esc_html__( 'Feed id %s not found', 'insta-gallery' ), $feed_id ),
				)
			);
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
		$limit        = $feed['limit'];

		$fetch_user_video_list = new API_Fetch_User_VideoList();
		$args = array(
			'open_id'      => $open_id,
			'access_token' => $access_token,
			'cursor'       => $cursor,
			'max_count'    => intval($limit),
		);

		$response = $fetch_user_video_list->get_data( $args );
		// Check if response is an error and return it.
		if ( isset( $response['message'] ) && isset( $response['code'] ) ) {
			return $this->handle_response( $response );
		}

		// Update user media data cache and return it.
		if ( ! QLTTF_DEVELOPER ) {
			$this->media_cache_engine->update( $media_complete_prefix, $response );
		}

		return $this->handle_response( $response );
	}

	public static function get_rest_args() {
		return array(
			'feed_id' => array(
				'required' => true,
				'type'     => 'string',
			),
		);
	}

	public static function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}

	public function get_rest_permission() {
		return true;
	}
}
