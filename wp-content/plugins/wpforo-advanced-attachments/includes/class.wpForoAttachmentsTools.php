<?php

class wpForoAttachmentsTools {
	public function __construct() {
		add_filter( 'wpforo_body_text_filter', [ $this, 'do_shortcodes' ] );
		add_filter( 'wpforo_body_text_filter', [ $this, 'do_default_attachs' ] );
		add_filter( 'bp_get_activity_content_body', [ $this, 'do_shortcodes' ], 1 );
		add_action( 'wp_ajax_wpforoattach_load_ajax_function', [ $this, 'load_ajax_function' ] );
		add_action( 'wp_ajax_wpforoattach_edit_filename_ajax', [ $this, 'edit_filename_ajax' ] );
	}

	public function get_file_type( $filename ) {
		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if( $ext == 'pdf' ) return 'pdf';
		if( $ext == 'doc' || $ext == 'docx' || $ext == 'odm' || $ext == 'odt' || $ext == 'ott' ) return 'word';
		if( $ext == 'zip' || $ext == 'gz' || $ext == 'bz' || $ext == 'bz2' || $ext == 'tar' || $ext == 'tgz' || $ext == '7z' || $ext == 'jar' || $ext == 'rar' || $ext == 'iso' ) return 'archive';
		if( $ext == 'csv' || $ext == 'xlsx' || $ext == 'xls' || $ext == 'xlsb' || $ext == 'xlsm' || $ext == 'xlt' || $ext == 'xltm' || $ext == 'ots' || $ext == 'ods' || $ext == 'stc' || $ext == 'sxc' ) return 'excel';
		if( $ext == 'xml' || $ext == 'html' || $ext == 'js' || $ext == 'php' || $ext == 'sql' || $ext == 'css' || $ext == 'sh' || $ext == 'csh' || $ext == 'json' || $ext == 'tcl' || $ext == 'bat' || $ext == 'as' || $ext == 'cmd' ) return 'code';
		if( $ext == 'ppt' || $ext == 'pptx' || $ext == 'otp' || $ext == 'odp' || $ext == 'pot' || $ext == 'pps' || $ext == 'shw' || $ext == 'sti' || $ext == 'sxi' || $ext == 'thmx' ) return 'powerpoint';

		if( $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'bmp' || $ext == 'tiff' || $ext == 'tif' || $ext == 'ico' ) return 'picture';
		if( $ext == 'mp4' || $ext == 'flv' || $ext == 'mov' || $ext == 'mkv' || $ext == 'vob' || $ext == 'mpg' || $ext == 'mpeg' || $ext == 'mpe' || $ext == '3gp' || $ext == 'avi' || $ext == 'wmv' ) return 'video';
		if( $ext == 'mp3' || $ext == 'wma' || $ext == 'wav' || $ext == 'amr' || $ext == 'mp2' || $ext == 'aac' ) return 'audio';
		if( $ext == 'txt' || $ext == 'asc' ) return 'alt';

		return 'file';
	}

	public function get_file_fa_ico_class( $filename ) {
		$filetype      = $this->get_file_type( $filename );
		$file_fa_class = 'far ' . ( $filetype != 'file' ? 'fa-file-' . $filetype : 'fa-file' );

		return $file_fa_class;
	}

	public function get_file_fa_ico( $filename ) {
		$file_fa_class = $this->get_file_fa_ico_class( $filename );

		return '<i class="' . $file_fa_class . ' fa-5x wpfa-file-icon"></i>';
	}

	public function strip_not_allowed_filetypes( $accepted_file_types = '' ) {
		if( ! $accepted_file_types ) $accepted_file_types = WPF_ATTACH()->options['accepted_file_types'];

		if( ! WPF()->usergroup->can( 'em' ) && WPF()->member->current_user_is_new() ) {
			$accepted_file_types = array_filter( array_map( 'trim', explode( '|', $accepted_file_types ) ) );
			$accepted_file_types = array_diff( (array) $accepted_file_types, wpforo_setting( 'antispam', 'limited_file_ext' ) );
			$accepted_file_types = implode( '|', $accepted_file_types );
		}

		return $accepted_file_types;
	}

	/**
	 * @param mixed $attachids
	 *
	 * @return string
	 */
	public function make_shortcode( $attachids ) {
		$attachids = wpforo_parse_args( $attachids );
		$attachids = array_map( 'wpforo_bigintval', $attachids );
		$attachids = array_filter( $attachids );
		$attachids = array_unique( $attachids );
		$shortcode = ( $attachids ? "[attach]" . implode( ',', $attachids ) . "[/attach]" : "" );

		return $shortcode;
	}

	public function do_shortcodes( $text, $ignore_permissions = false ) {
		if( preg_match_all( '#\[attach(?:[^\[\]]+?title=[\'\"]([^\'\"]+)[\'\"])?[^\[\]]*?\](.*?)\[\/attach\]#isu', $text, $shortcodes, PREG_SET_ORDER ) ) {
			foreach( $shortcodes as $shortcode ) {

				$alt_title = '';
				$expld     = explode( ',', $shortcode[2] );
				if( ! empty( $expld ) ) {
					$shortcode_title = false;
					if( ! empty( $shortcode[1] ) ) {
						$alt_title       = trim( $shortcode[1] );
						$shortcode_title = true;
					}

					$attachs   = [];
					$expld     = array_filter( array_map( 'wpforo_bigintval', $expld ) );
					$attachids = implode( ',', $expld );
					if( count( $expld ) === 1 ) {
						if( $temp = WPF_ATTACH()->get_attach( current( $expld ) ) ) $attachs[] = $temp;
					} else {
						if( $attachids ) $attachs = WPF_ATTACH()->get_attachs( [ 'include' => $attachids, 'orderby' => 'FIELD(`attachid`,' . $attachids . ')' ] );
					}

					$attach_html = '';
					if( ! empty( $attachs ) ) {

						if( $ignore_permissions || ( WPF()->perm->forum_can( 'va' ) && WPF()->usergroup->can( 'caa' ) ) ) {
							foreach( $attachs as $key => $attach ) {

								if( ! $shortcode_title || ( $shortcode_title && $key ) ) $alt_title = str_replace( [ '-', '_' ], ' ', pathinfo( $attach['filename'], PATHINFO_FILENAME ) );
								$attr      = 'alt="' . $alt_title . '" title="' . $alt_title . '"';
								$link_attr = 'title="' . $alt_title . '"';

								$fileurl  = preg_replace( '#^https?\:#is', '', $attach['fileurl'] );
								$filepath = wpforo_fix_upload_dir( $fileurl );
								if( file_exists( $filepath ) ) {
									$w                 = (int) WPF_ATTACH()->options['thumbnail_width'];
									$h                 = (int) WPF_ATTACH()->options['thumbnail_height'];
									$file_display_name = urldecode( basename( $attach['filename'] ) );
									if( strpos( $attach['mime'], 'image/' ) === 0 ) {
										if( WPF_ATTACH()->options['image_caption'] ) {
											$attach_html .= sprintf(
												'<div class="wpfa-item wpfa-img-caption"><a href="%1$s" data-gallery="#wpf-content-blueimp-gallery" %2$s><img src="%3$s" style="max-width: %4$dpx; max-height: %5$dpx;"></a><div class="wpfa-info" %6$s>%7$s</div></div>',
												esc_attr( $this->get_public_url( $attach ) ),
												$link_attr,
												esc_attr( $this->get_public_url( $attach, true ) ),
												$w,
												$h,
												$attr,
												$file_display_name
											);
										} else {
											if( WPF_ATTACH()->options['boxed'] ) {
												$attach_html .= sprintf(
													'<div class="wpfa-item wpfa-img-boxed" style="min-width: %1$dpx;min-height: %2$dpx;"><a href="%3$s" data-gallery="#wpf-content-blueimp-gallery"><img src="%4$s" style="max-width: %1$dpx; max-height: %2$dpx;" %5$s></a></div>',
													$w,
													$h,
													esc_attr( $this->get_public_url( $attach ) ),
													esc_attr( $this->get_public_url( $attach, true ) ),
													$attr
												);
											} else {
												$attach_html .= sprintf(
													'<div class="wpfa-item wpfa-img"><a href="%1$s" data-gallery="#wpf-content-blueimp-gallery" %2$s><img src="%3$s" style="max-width: %4$dpx; max-height: %5$dpx;" %6$s></a></div>',
													esc_attr( $this->get_public_url( $attach ) ),
													$link_attr,
													esc_attr( $this->get_public_url( $attach, true ) ),
													$w,
													$h,
													$attr
												);
											}
										}
									} elseif( strpos( $attach['mime'], 'video/' ) === 0 ) {
										$attach_html .= sprintf(
											'<div class="wpfa-item wpfa-video"><video src="%1$s" controls %2$s></video></div>',
											esc_attr( $this->get_public_url( $attach ) ),
											$attr
										);
									} elseif( strpos( $attach['mime'], 'audio/' ) === 0 ) {
										$attach_html .= sprintf(
											'<div class="wpfa-item wpfa-audio"><audio src="%1$s" controls %2$s></audio></div>',
											esc_attr( $this->get_public_url( $attach ) ),
											$attr
										);
									} else {
										$attach_html .= sprintf(
											'<div class="wpfa-item wpfa-file"><a href="%1$s" target="_blank" download="%2$s" %3$s>%4$s<span>%5$s</span></a></div>',
											esc_attr( $this->get_public_url( $attach ) ),
											esc_attr( $attach['filename'] ),
											$link_attr,
											$this->get_file_fa_ico( $attach['filename'] ),
											$file_display_name
										);
									}
								}

							}
						} else {
							foreach( $attachs as $attach ) {
								$fileurl  = preg_replace( '#^https?\:#is', '', $attach['fileurl'] );
								$filepath = wpforo_fix_upload_dir( $fileurl );
								if( file_exists( $filepath ) ) {
									$file_display_name = urldecode( basename( $attach['filename'] ) );
									$attach_html       .= sprintf(
										'<br/><div class="wpfa-item wpfa-file" style="border: 1px dotted #bbb; padding: 5px 10px; font-size: 13px; margin: 5px 2px;"><a class="attach_cant_view" style="cursor: pointer;"> <span style="color: #666;" > %1$s : </span> %2$s </a> </div>',
										wpforo_phrase( 'Attachment', false ),
										$file_display_name
									);
								}
							}
						}

					}

					if( $attach_html ) $attach_html = '<figure data-attachids="' . $attachids . '" contenteditable="false">' . $attach_html . '</figure>';
					$text = str_replace( $shortcode[0], $attach_html, $text );

				}
			}
		}

		return $text;
	}

	public function do_default_attachs( $text ) {
		if( preg_match_all( '#<a[^<>]*class=[\'"]wpforo-default-attachment[\'"][^<>]*href=[\'"]([^\'"]+)[\'"][^<>]*>[\r\n\t\s\0]*(?:<i[^<>]*>[\r\n\t\s\0]*</i>[\r\n\t\s\0]*)?([^<>]*)</a>#isu', $text, $matches, PREG_SET_ORDER ) ) {
			foreach( $matches as $match ) {
				$attach_html = '';
				$fileurl     = preg_replace( '#^https?\:#is', '', $match[1] );
				$filename    = $match[2];
				$filetype    = $this->get_file_type( $fileurl );

				$alt_title = str_replace( [ '-', '_' ], ' ', pathinfo( $filename, PATHINFO_FILENAME ) );
				$attr      = 'alt="' . $alt_title . '" title="' . $alt_title . '"';

				$filepath = wpforo_fix_upload_dir( $fileurl );

				$file_exists = file_exists( $filepath );

				if( apply_filters( 'wpforoattach_default_attachment_file_exists', $file_exists ) ) {

					if( WPF()->perm->forum_can( 'va' ) && WPF()->usergroup->can( 'caa' ) ) {
						if( $filetype == 'picture' ) {
							if( isset( WPF_ATTACH()->options['image_caption'] ) && WPF_ATTACH()->options['image_caption'] ) {
								$attach_html .= '<br/><div class="wpfa-item wpfa-img"><a href="' . $fileurl . '" data-gallery="#wpf-content-blueimp-gallery"><img src="' . $fileurl . '" ' . $attr . '/></a><div class="wpfa-info">' . urldecode( basename( $filename ) ) . '</div></div>';
							} else {
								$attach_html .= '<br/><a href="' . $fileurl . '" data-gallery="#wpf-content-blueimp-gallery"><img src="' . $fileurl . '"  ' . $attr . '/></a>';
							}
						} elseif( $filetype == 'video' ) {
							$attach_html .= '<br/><video src="' . $fileurl . '" controls  ' . $attr . '></video>';
						} elseif( $filetype == 'audio' ) {
							$attach_html .= '<br/><audio src="' . $fileurl . '" controls  ' . $attr . '></audio>';
						} else {
							$attach_html .= '<br/><div class="wpfa-item wpfa-file"><a href="' . $fileurl . '" target="_blank" download="' . $filename . '"  ' . $attr . '>' . $this->get_file_fa_ico( $fileurl ) . urldecode( basename( $filename ) ) . '</a><div class="wpfa-info"></div></div>';
						}
					} else {
						$attach_html .= '<br/><div class="wpfa-item wpfa-file"><a class="attach_cant_view" style="cursor: pointer;">< span style="color: #666;"> ' . wpforo_phrase( 'Attachment', false ) . ' : </span> ' . urldecode( basename( $filename ) ) . '</a></div>';
					}
				}

				if( $attach_html ) $attach_html .= '<br/>';
				$text = str_replace( $match[0], $attach_html, $text );
			}
		}

		return $text;
	}

	public function get_public_url( $attach, $thumb = false ) {
		$url = '';
		if( ( $fileurl = trim( (string) wpfval( $attach, 'fileurl' ) ) ) && ( $slug = wpfval( $attach, 'slug' ) ) ) {
			$fileurl = preg_replace( '#^https?:#is', '', $fileurl );
			if( WPF_ATTACH()->options['download_via_php'] ) {
				$forofile = wpforo_settings_get_slug( 'forofile' );
				if( $thumb ) {
					$url = wpforo_home_url( "/$forofile/thumb/$slug/" );
				} else {
					$url = wpforo_home_url( "/$forofile/$slug/" );
				}
			} else {
				if( $thumb ) {
					$thumb_fileurl = str_replace( basename( $fileurl ), 'thumbnail/' . basename( $fileurl ), $fileurl );
					$filepath      = wpforo_fix_upload_dir( $thumb_fileurl );
					if( file_exists( $filepath ) ) {
						$url = $thumb_fileurl;
					} else {
						$url = $fileurl;
					}
				} else {
					$url = $fileurl;
				}
				$url = wpforo_fix_upload_url( $url );
			}
		}

		return $url;
	}

	public function init_uploader_class( $initialize = true ) {
		/* Include the upload handler */
		require_once( WPFOROATTACH_DIR . '/wpf-third-party/file-uploader/UploadHandler.php' );
		require_once( WPFOROATTACH_DIR . '/includes/class.wpForoAttachUploadHandler.php' );

		$options = [
			'script_url'          => admin_url( 'admin-ajax.php' ),
			'delete_type'         => 'POST',
			'upload_dir'          => WPF()->folders['attachments']['dir'] . DIRECTORY_SEPARATOR . WPF()->current_userid . DIRECTORY_SEPARATOR,
			'upload_url'          => WPF()->folders['attachments']['url//'] . '/' . WPF()->current_userid . '/',
			'readfile_chunk_size' => 10 * 1024 * 1024,
			'accept_file_types'   => '#\.(' . WPF_ATTACH()->options['accepted_file_types'] . ')$#isu',
			'max_file_size'       => WPF_ATTACH()->options['maximum_file_size'],
			'image_file_types'    => '#\.(' . WPF_ATTACH()->options['accepted_file_types'] . ')$#isu',
			'image_versions'      => [
				// The empty image version key defines options for the original image:
				''          => [
					'jpeg_quality' => (int) WPF_ATTACH()->options['bigimg_jpeg_quality'],
					'max_width'    => 0,
					'max_height'   => (int) WPF_ATTACH()->options['bigimg_max_height'],
					// Automatically rotate images based on EXIF meta data:
					'auto_orient'  => true,
					'strip'        => true,
				],
				'thumbnail' => [
					'max_width'    => WPF_ATTACH()->options['thumbnail_width'],
					'max_height'   => WPF_ATTACH()->options['thumbnail_height'],
					'jpeg_quality' => (int) WPF_ATTACH()->options['thumbnail_jpeg_quality'],
					'auto_orient'  => true,
					'strip'        => true,
				],
			],
		];
		$options = apply_filters( 'wpforoattach_uploader_class_options', $options );

		$error_messages = [
			1                     => wpforo_attach_phrase( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', false ),
			2                     => wpforo_attach_phrase( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', false ),
			3                     => wpforo_attach_phrase( 'The uploaded file was only partially uploaded', false ),
			4                     => wpforo_attach_phrase( 'No file was uploaded', false ),
			6                     => wpforo_attach_phrase( 'Missing a temporary folder', false ),
			7                     => wpforo_attach_phrase( 'Failed to write file to disk', false ),
			8                     => wpforo_attach_phrase( 'A PHP extension stopped the file upload', false ),
			'post_max_size'       => wpforo_attach_phrase( 'The uploaded file exceeds the post_max_size directive in php.ini', false ),
			'max_file_size'       => wpforo_attach_phrase( 'File is too big', false ),
			'min_file_size'       => wpforo_attach_phrase( 'File is too small', false ),
			'accept_file_types'   => wpforo_attach_phrase( 'Filetype not allowed', false ),
			'max_number_of_files' => wpforo_attach_phrase( 'Maximum number of files exceeded', false ),
			'max_width'           => wpforo_attach_phrase( 'Image exceeds maximum width', false ),
			'min_width'           => wpforo_attach_phrase( 'Image requires a minimum width', false ),
			'max_height'          => wpforo_attach_phrase( 'Image exceeds maximum height', false ),
			'min_height'          => wpforo_attach_phrase( 'Image requires a minimum height', false ),
			'abort'               => wpforo_attach_phrase( 'File upload aborted', false ),
			'image_resize'        => wpforo_attach_phrase( 'Failed to resize image', false ),
		];

		return new wpForoAttachUploadHandler( $options, $initialize, $error_messages );
	}

	public function load_ajax_function() {
		$this->init_uploader_class();
		die;
	}

	public function edit_filename_ajax() {
		if( ( $attachid = wpfval( $_POST, 'attachid' ) ) && ( $filename = trim( strip_tags( wpfval( $_POST, 'filename' ) ) ) ) ) {
			if( WPF_ATTACH()->edit( [ 'filename' => $filename ], $attachid ) ) {
				echo $filename;
				exit();
			}
		}

		echo 0;
		exit();
	}

}
