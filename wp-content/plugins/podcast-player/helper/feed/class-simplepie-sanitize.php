<?php
/**
 * Feed API: PP_SimplePie_Sanitize class
 *
 * @link       https://www.vedathemes.com
 * @since      1.0.0
 *
 * @package    Podcast_Player
 * @subpackage Podcast_Player/public
 */

namespace Podcast_Player\Helper\Feed;

/**
 * Class to override simplepie sanitization class.
 *
 * Override and disable simplepie sanitization. We will use customized method
 * to fetch and sanitize required data.
 *
 * @since 2.4.0
 *
 * @see SimplePie_Sanitize
 */
class SimplePie_Sanitize extends \SimplePie_Sanitize {

	/**
	 * Podcast Player SimplePie sanitization.
	 *
	 * @since 2.4.0
	 *
	 * @param mixed   $data The data that needs to be sanitized.
	 * @param integer $type The type of data that it's supposed to be.
	 * @param string  $base Optional. The `xml:base` value to use when converting relative
	 *                      URLs to absolute ones. Default empty.
	 * @return mixed Sanitized data.
	 */
	public function sanitize( $data, $type, $base = '' ) {
		$data = trim( $data );
		if ( $type & SIMPLEPIE_CONSTRUCT_MAYBE_HTML ) {
			if ( preg_match( '/(&(#(x[0-9a-fA-F]+|[0-9]+)|[a-zA-Z0-9]+)|<\/[A-Za-z][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E]*' . SIMPLEPIE_PCRE_HTML_ATTRIBUTE . '>)/', $data ) ) {
				$type |= SIMPLEPIE_CONSTRUCT_HTML;
			} else {
				$type |= SIMPLEPIE_CONSTRUCT_TEXT;
			}
		}
		if ( $type & ( SIMPLEPIE_CONSTRUCT_HTML | SIMPLEPIE_CONSTRUCT_XHTML ) ) {
			if ( 'UTF-8' !== $this->output_encoding ) {
				$data = $this->registry->call( 'Misc', 'change_encoding', array( $data, 'UTF-8', $this->output_encoding ) );
			}
			return $data;
		}
		return $data;
	}
}
