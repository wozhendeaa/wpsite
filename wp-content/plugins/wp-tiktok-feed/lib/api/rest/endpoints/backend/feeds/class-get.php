<?php
namespace QuadLayers\TTF\Api\Rest\Endpoints\Backend\Feeds;

use QuadLayers\TTF\Models\Feeds as Models_Feed;
use QuadLayers\TTF\Api\Rest\Endpoints\Base as Base;
/**
 * API_Rest_Feeds_Get Class
 */
class Get extends Base {

	protected static $rest_route = 'feeds';

	public function callback( \WP_REST_Request $request ) {

		$models_feeds = new Models_Feed();

		$feed_id = $request->get_param( 'feed_id' );

		if ( null === $feed_id ) {
			$feeds = $models_feeds->get();
			if ( null !== $feeds && 0 !== count( $feeds ) ) {
				return $this->handle_response( $feeds );
			}
			$response = array(
				'code'    => 404,
				'message' => esc_html__( 'Feeds empty', 'wp-tiktok-feed' ),
			);
			return $this->handle_response( $response );
		}

		$feed = $models_feeds->get_by_id( $feed_id );

		if ( ! $feed ) {
			$response = array(
				'code'    => 404,
				'message' => sprintf( esc_html__( 'Feed %s not found', 'wp-tiktok-feed' ), $feed_id ),
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $feed );

	}

	public static function get_rest_args() {
		return array(
			'feed_id' => array(
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
		);
	}

	public static function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}

	public function get_rest_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		return true;
	}
}
