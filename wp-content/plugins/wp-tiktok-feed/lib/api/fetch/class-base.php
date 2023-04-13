<?php

namespace QuadLayers\TTF\Api\Fetch;

use QuadLayers\TTF\Api\Fetch\Fetch as Fetch_Interface;

/**
 * Base Class
 */
abstract class Base implements Fetch_Interface {

	/**
	 * Base fetch url
	 *
	 * @var string
	 */
	protected $fetch_url = 'https://tiktokfeed.quadlayers.com/data/';

	/**
	 * Function to get response and parse to data
	 *
	 * @param array $args Args to get response with.
	 * @return array
	 */
	public function get_data( $args = null ) {
		$response = $this->get_response( $args );
		if ( isset( $response['code'], $response['message'] ) ){
			return $response;
		}
		$data     = $this->response_to_data( $response );
		return $data;
	}

	/**
	 * Function to query Tiktok data.
	 *
	 * @param string $args Args to set query.
	 * @return array
	 */
	public function get_response( $args = null ) {
		$url = $this->get_url();

		$response = wp_remote_post(
			$url,
			array(
				'method'  => 'POST',
				'timeout' => 45,
				'body'    => json_encode( $args ),
			)
		);

		$response = $this->handle_response( $response );

		return $response;
	}

	/**
	 * Function to handle query response.
	 *
	 * @param array $response Tiktok response.
	 * @return array
	 */
	public function handle_response( $response = null ) {

		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( null === $response && json_last_error() !== JSON_ERROR_NONE ) {
			$response = array(
				'error' => array(
					'code'    => 404,
					'message' => esc_html__( 'Response is not valid json.', 'wp-tiktok-feed' ),
				),
			);
		}

		return $this->handle_error( $response ) ? $this->handle_error( $response ) : $response;
	}

	/**
	 * Function to handle error on query response.
	 *
	 * @param array $response Tiktok response.
	 * @return array
	 */
	public function handle_error( $response = null ) {

		$is_error = empty( $response['data'] ) && 0 !== $response['error']['code'];

		if ( $is_error ) {
			$message = isset( $response['error']['message'] ) ? $response['error']['message'] : esc_html__( 'Unknown error.', 'wp-tiktok-feed' );
			$code    = isset( $reponse['error']['code'] ) ? $response['error']['code'] : 413;
			return array(
				'code'    => $code,
				'message' => $message,
			);
		}
		return false;
	}

}
