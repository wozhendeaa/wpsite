<?php

namespace QuadLayers\TTF\Api\Fetch\User_Video_List;

use QuadLayers\TTF\Api\Fetch\Base as Base;

/**
 * API_Fetch_User_Video_List Class extends Base
 */
class Get extends Base {

	/**
	 * Function to build query url.
	 *
	 * @return string
	 */
	public function get_url() {
		$url = $this->fetch_url . 'userVideoList';
		return $url;
	}

	/**
	 * Function to parse response to usable data.
	 *
	 * @param array $response Raw response from tiktok.
	 * @return array
	 */
	public function response_to_data( $response = null ) {

		if ( ! isset( $response['data']['videos'] ) ) {
			$response = array(
				'message' => esc_html__( 'The current feed has no videos.', 'wp-tiktok-feed' ),
				'code'    => 404,
			);
			return $response;
		}

		$videos_data = $response['data']['videos'];

		$videos_array = array();

		foreach ( $videos_data as $video ) {

			if ( ! isset( $video['id'] ) ) {
				continue;
			}

			$url_encode        = base64_encode( $video['share_url'] );
			$download_url_ajax = admin_url( "admin-ajax.php?action=qlttf-download&url={$url_encode}&video_id={$video['id']}&source=username" );

			preg_match_all(
				'/(?=#)(.*?)(?=\s)+/',
				htmlspecialchars( $video['video_description'] ),
				$tags
			);
			$videos_array[] = array(
				'id'                => $video['id'],
				'create_time'       => $response['data']['has_more'] ? $video['create_time'] : 0,
				'cover_image_url'   => isset( $video['cover_image_url'] ) ? $video['cover_image_url'] : '',
				'share_url'         => $video['share_url'],
				'title'             => $video['title'],
				'video_description' => preg_replace_callback(
					'/(?=#)(.*?)(?=\s)+/',
					function( $tag ) {
						$tag = str_replace( '#', '', $tag[1] );
						return '<a target="_blank" href="' . QLTTF_TIKTOK_URL . '/tag/' . $tag . '">#' . $tag . '</a>';
					},
					htmlspecialchars( $video['video_description'] )
				),
				'tags'              => $tags[0],
				'likes_count'       => $video['like_count'],
				'comments_count'    => $video['comment_count'],
				'views_count'       => $video['view_count'],
				'video_url'         => '',
				'download_url'      => $download_url_ajax,
				'height'            => isset( $video['height'] ) ? $video['height'] : 0,
				'width'             => isset( $video['width'] ) ? $video['width'] : 0,
				'date'              => date_i18n( 'j F, Y', $video['create_time'] ),
				'embed_html'        => $video['embed_html'],
				'embed_link'        => isset( $video['embed_link'] ) ? $video['embed_link'] : '',
			);
		}

		return $videos_array;
	}
}
