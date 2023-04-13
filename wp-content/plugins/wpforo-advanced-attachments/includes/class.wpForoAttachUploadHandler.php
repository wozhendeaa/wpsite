<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


class wpForoAttachUploadHandler extends UploadHandler {
	private $items_count = 0;

	protected function set_additional_file_properties( $file, $allow = false ) {
		parent::set_additional_file_properties( $file );
		if( $_SERVER['REQUEST_METHOD'] === 'GET' || $allow ) {
			$attach = WPF_ATTACH()->get_attach( $file->name );

			$file->id              = (int) wpfval( $attach, 'attachid' );
			$file->filename        = (string) wpfval( $attach, 'filename' );
			$file->wpf_type        = WPF_ATTACH()->tools->get_file_type( $file->name );
			$file->wpf_file_fa_ico = WPF_ATTACH()->tools->get_file_fa_ico_class( $file->name );
			$file->wpf_is_new      = false;
			$file->url             = WPF_ATTACH()->tools->get_public_url( $attach );
			if( $file->wpf_type === 'picture' ) $file->thumbnailUrl = WPF_ATTACH()->tools->get_public_url( $attach, true );
			$file->front_view = WPF_ATTACH()->tools->do_shortcodes( '[attach]' . $file->id . '[/attach]', true );
		}
	}

	public function delete( $print_response = true ) {
		if( ! empty( WPF_ATTACH()->options['disable_delete'] ) ) return false;

		$response = parent::delete( false );
		foreach( $response as $name => $deleted ) {
			if( $deleted ) WPF_ATTACH()->delete( $this->get_download_url( $name ) );
		}

		return $this->generate_response( $response, $print_response );
	}

	public function wpf_download( $attach, $thumb = false ) {
		if( $attach && $fileurl = wpfval( $attach, 'fileurl' ) ) {
			if( $thumb && strpos( $attach['mime'], 'image/' ) === 0 ) {
				$fileurl = str_replace( basename( $fileurl ), 'thumbnail/' . basename( $fileurl ), $fileurl );
				$t       = true;
			} else {
				$t = false;
			}
			$fileurl  = preg_replace( '#^https?:#is', '', $fileurl );
			$filepath = wpforo_fix_upload_dir( $fileurl );
			$filename = trim( (string) wpfval( $attach, 'filename' ) );
			if( ! $filename ) $filename = basename( $filepath );

			if( ! file_exists( $filepath ) ) {
				if( $t ) {
					$fileurl  = preg_replace( '#^https?:#is', '', $attach['fileurl'] );
					$filepath = wpforo_fix_upload_dir( $fileurl );
					if( ! file_exists( $filepath ) ) {
						$this->header( 'HTTP/1.1 404 Not Found' );
						die;
					}
				} else {
					if( strpos( $attach['mime'], 'image/' ) === 0 ) {
						$fileurl  = str_replace( basename( $fileurl ), 'thumbnail/' . basename( $fileurl ), $attach['fileurl'] );
						$fileurl  = preg_replace( '#^https?:#is', '', $fileurl );
						$filepath = wpforo_fix_upload_dir( $fileurl );
						if( ! file_exists( $filepath ) ) {
							$this->header( 'HTTP/1.1 404 Not Found' );
							die;
						}
					} else {
						$this->header( 'HTTP/1.1 404 Not Found' );
						die;
					}
				}
			}

			switch( WPF_ATTACH()->options['download_via_php'] ) {
				case 1:
					$redirect_header = null;
				break;
				case 2:
					$redirect_header = 'X-Sendfile';
				break;
				case 3:
					$redirect_header = 'X-Accel-Redirect';
				break;
				default:
					$redirect_header = null;
				//				    $this->header('HTTP/1.1 403 Forbidden'); die;
			}
			if( $redirect_header ) {
				$this->header( $redirect_header . ': ' . preg_replace( '#^.*?/wp-content/#isu', '/wp-content/', $filepath ) );
				die;
			}
			// Prevent browsers from MIME-sniffing the content-type:
			$this->header( 'X-Content-Type-Options: nosniff' );
			if( ! preg_match( $this->options['inline_file_types'], basename( $filepath ) ) ) {
				$this->header( 'Content-Type: application/octet-stream' );
				$this->header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			} else {
				$this->header( 'Content-Type: ' . $this->get_file_type( $filepath ) );
				$this->header( 'Content-Disposition: inline; filename="' . $filename . '"' );
			}
			$this->header( 'Content-Length: ' . $this->get_file_size( $filepath ) );
			$this->header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s T', filemtime( $filepath ) ) );
			$this->readfile( $filepath );
		} else {
			$this->header( 'HTTP/1.1 404 Not Found' );
			die;
		}
	}

	protected function handle_file_upload( $uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null ) {
		wp_raise_memory_limit( 'image' );

		$file             = new \stdClass();
		$file_name        = $this->get_file_name( $uploaded_file, $name, $size, $type, $error, $index, $content_range );
		$file->size       = $this->fix_integer_overflow( intval( $size ) );
		$file->type       = $type;
		$file->filename   = $name;
		$file->wpf_is_new = false;

		$ext = pathinfo( $file_name, PATHINFO_EXTENSION );
		$fnm = pathinfo( $file_name, PATHINFO_FILENAME );
		$fnm = str_replace( ' ', '-', $fnm );
		while( strpos( $fnm, '--' ) !== false ) {
			$fnm = str_replace( '--', '-', $fnm );
		}
		$fnm       = preg_replace( "/[^-a-zA-Z0-9]/", "", $fnm );
		$fnm       = trim( $fnm, "-" );
		$fnm_empty = ( $fnm ? false : true );

		$file_name = $fnm . "." . $ext;

		$file->name = $file_name;

		$args = [
			'filename' => $file->filename,
			'size'     => $file->size,
			'mime'     => $file->type,
		];
		if( $file->id = WPF_ATTACH()->add( $args ) ) {
			$file->name = $file->id . ( $fnm_empty ? '' : '-' ) . $file->name;

			if( $this->validate( $uploaded_file, $file, $error, $index ) ) {
				$this->handle_form_data( $file, $index );
				$upload_dir = $this->get_upload_path();
				if( ! is_dir( $upload_dir ) ) {
					mkdir( $upload_dir, $this->options['mkdir_mode'], true );
				}
				if( ! is_dir( $upload_dir . 'thumbnail/' ) ) {
					mkdir( $upload_dir . 'thumbnail/', $this->options['mkdir_mode'], true );
				}

				$file_path = $this->get_upload_path( $file->name );

				$append_file = $content_range && is_file( $file_path ) && $file->size > $this->get_file_size( $file_path );
				if( $uploaded_file && is_uploaded_file( $uploaded_file ) ) {
					// multipart/formdata uploads (POST method uploads)
					if( $append_file ) {
						file_put_contents(
							$file_path,
							fopen( $uploaded_file, 'r' ),
							FILE_APPEND
						);
					} else {
						move_uploaded_file( $uploaded_file, $file_path );
					}
				} else {
					// Non-multipart uploads (PUT method support)
					file_put_contents(
						$file_path,
						fopen( 'php://input', 'r' ),
						$append_file ? FILE_APPEND : 0
					);
				}
				$file_size = $this->get_file_size( $file_path, $append_file );
				if( $file_size === $file->size ) {
					$file->url = $this->get_download_url( $file->name );
					if( $this->is_valid_image_file( $file_path ) ) {
						$file_path_thumb = str_replace( basename( $file_path ), 'thumbnail/' . basename( $file_path ), $file_path );
						copy( wpforo_fix_dir_sep( $file_path ), wpforo_fix_dir_sep( $file_path_thumb ) );

						$this->handle_image_file( $file_path, $file );
					}
				} else {
					$file->size = $file_size;
					if( ! $content_range && $this->options['discard_aborted_uploads'] ) {
						unlink( $file_path );
						$file->error = $this->get_error_message( 'abort' );
					}
				}


				//	            $this->set_additional_file_properties($file);
			}

		}

		if( ! empty( $file->url ) && $file->id ) {
			$file->error      = '';
			$file->wpf_is_new = true;
			WPF_ATTACH()->edit( [ 'fileurl' => $file->url, 'size' => $file->size ], $file->id );
			$this->set_additional_file_properties( $file, true );
		} elseif( $file->id ) {
			WPF_ATTACH()->delete( $file->id );
		}

		return $file;
	}

	private function sortByCreationTime( $file_1, $file_2 ) {
		$upload_dir = $this->get_upload_path();
		$file_1     = filectime( $upload_dir . $file_1 );
		$file_2     = filectime( $upload_dir . $file_2 );
		if( $file_1 == $file_2 ) {
			return 0;
		}

		return $file_1 < $file_2 ? 1 : - 1;
	}

	public function get( $print_response = true ) {
		if( $print_response && $this->get_query_param( 'download' ) ) {
			return $this->download();
		}
		$file_name = $this->get_file_name_param();
		if( $file_name ) {
			$response = [
				$this->get_singular_param_name() => $this->get_file_object( $file_name ),
			];
		} else {
			$response = [
				$this->options['param_name'] => $this->get_file_objects(),
				'items_count'                => intval( $this->items_count ),
			];
		}

		return $this->generate_response( $response, $print_response );
	}

	protected function get_file_objects( $iteration_method = 'get_file_object' ) {
		$offset = intval( wpfval( $_GET, 'offset' ) );
		if( $iteration_method === 'get_file_object' ) {
			$this->items_count = 0;
			$args              = [ 'userid' => WPF()->current_userid, 'offset' => $offset, 'row_count' => WPF_ATTACH()->options['attachs_per_load'] ];
			$files             = WPF_ATTACH()->get_attachs( $args, $this->items_count );
			$files             = array_values(
				array_filter(
					array_map(
						[ $this, 'wpf_get_file_object' ],
						$files
					)
				)
			);
		} else {
			$upload_dir = $this->get_upload_path();
			if( ! is_dir( $upload_dir ) ) return [];
			$files = scandir( $upload_dir );
			$files = array_diff( $files, [ '.', '..', 'thumbnail' ] );
			$files = array_values(
				array_filter(
					array_map(
						[ $this, $iteration_method ],
						$files
					)
				)
			);
		}

		return $files;
	}

	private function wpf_get_file_object( $file ) {
		$filedir = wpforo_fix_upload_dir( $file['fileurl'] );
		if( file_exists( $filedir ) ) {
			$file['id']              = $file['attachid'] = (int) wpfval( $file, 'attachid' );
			$file['posts']           = (int) wpfval( $file, 'posts' );
			$file['userid']          = (int) wpfval( $file, 'userid' );
			$file['size']            = (int) wpfval( $file, 'size' );
			$file['name']            = basename( $file['fileurl'] );
			$file['wpf_type']        = WPF_ATTACH()->tools->get_file_type( $file['name'] );
			$file['wpf_file_fa_ico'] = WPF_ATTACH()->tools->get_file_fa_ico_class( $file['name'] );
			$file['wpf_is_new']      = false;
			$file['url']             = WPF_ATTACH()->tools->get_public_url( $file );
			if( $file['wpf_type'] === 'picture' ) $file['thumbnailUrl'] = WPF_ATTACH()->tools->get_public_url( $file, true );
			$file['front_view'] = WPF_ATTACH()->tools->do_shortcodes( '[attach]' . $file['id'] . '[/attach]', true );
			$file['deleteUrl']  = $this->options['script_url'] . $this->get_query_separator( $this->options['script_url'] ) . $this->get_singular_param_name() . '=' . rawurlencode( $file['name'] );
			$file['deleteType'] = $this->options['delete_type'];
			if( $file['deleteType'] !== 'DELETE' ) {
				$file['deleteUrl'] .= '&_method=DELETE';
			}
			if( $this->options['access_control_allow_credentials'] ) {
				$file['deleteWithCredentials'] = true;
			}
			unset( $file['fileurl'] );

			return (object) $file;
		}

		return null;
	}

	protected function get_upload_data( $id ) {
		return wpfval( $_FILES, $id );
	}

	protected function get_post_param( $id ) {
		return wpfval( $_POST, $id );
	}

	protected function get_query_param( $id ) {
		return wpfval( $_GET, $id );
	}

	protected function get_server_var( $id ) {
		return wpfval( $_SERVER, $id );
	}

	protected function get_upload_path( $file_name = null, $version = null ) {
		$file_name = $file_name ? $file_name : '';
		if( empty( $version ) ) {
			$version_path = '';
		} else {
			$version_dir = wpfval( $this->options, 'image_versions', $version, 'upload_dir' );
			if( $version_dir ) {
				return $version_dir . $this->get_user_path() . $file_name;
			}
			$version_path = $version . '/';
		}

		return $this->options['upload_dir'] . $this->get_user_path() . $version_path . $file_name;
	}

	protected function get_download_url( $file_name, $version = null, $direct = false ) {
		if( ! $direct && $this->options['download_via_php'] ) {
			$url = $this->options['script_url'] . $this->get_query_separator( $this->options['script_url'] ) . $this->get_singular_param_name() . '=' . rawurlencode( $file_name );
			if( $version ) {
				$url .= '&version=' . rawurlencode( $version );
			}

			return $url . '&download=1';
		}
		if( empty( $version ) ) {
			$version_path = '';
		} else {
			$version_url = wpfval( $this->options, 'image_versions', $version, 'upload_url' );
			if( $version_url ) {
				return $version_url . $this->get_user_path() . rawurlencode( $file_name );
			}
			$version_path = rawurlencode( $version ) . '/';
		}

		return $this->options['upload_url'] . $this->get_user_path() . $version_path . rawurlencode( $file_name );
	}

	protected function imagemagick_create_scaled_image( $file_name, $version, $options ) {
		list(
			$file_path, $new_file_path
			) = $this->get_scaled_image_file_paths( $file_name, $version );
		$mw     = (int) wpfval( $options, 'max_width' );
		$mh     = (int) wpfval( $options, 'max_height' );
		$resize = '';
		if( $mw ) $resize = $mw;
		if( $mh ) $resize .= 'X' . $mh;
		if( ! $resize && empty( $options['auto_orient'] ) ) {
			if( $file_path !== $new_file_path ) {
				return copy( $file_path, $new_file_path );
			}

			return true;
		}
		$cmd = $this->options['convert_bin'];
		if( ! empty( $this->options['convert_params'] ) ) {
			$cmd .= ' ' . $this->options['convert_params'];
		}
		$cmd .= ' ' . escapeshellarg( $file_path );
		if( ! empty( $options['auto_orient'] ) ) {
			$cmd .= ' -auto-orient';
		}
		if( $resize ) {
			// Handle animated GIFs:
			$cmd .= ' -coalesce';
			if( empty( $options['crop'] ) ) {
				$cmd .= ' -resize ' . escapeshellarg( $resize . '>' );
			} else {
				$cmd .= ' -resize ' . escapeshellarg( $resize . '^' );
				$cmd .= ' -gravity center';
				$cmd .= ' -crop ' . escapeshellarg( $resize . '+0+0' );
			}
			// Make sure the page dimensions are correct (fixes offsets of animated GIFs):
			$cmd .= ' +repage';
		}
		if( ! empty( $options['convert_params'] ) ) {
			$cmd .= ' ' . $options['convert_params'];
		}
		$cmd .= ' ' . escapeshellarg( $new_file_path );
		exec( $cmd, $output, $error );
		if( $error ) {
			error_log( implode( '\n', $output ) );

			return false;
		}

		return true;
	}

	protected function imagick_create_scaled_image( $file_name, $version, $options ) {
		list(
			$file_path, $new_file_path
			) = $this->get_scaled_image_file_paths( $file_name, $version );
		$image = $this->imagick_get_image_object(
			$file_path, ! empty( $options['crop'] ) || ! empty( $options['no_cache'] )
		);
		if( is_null( $image ) ) return false;
		$mw = (int) wpfval( $options, 'max_width' );
		$mh = (int) wpfval( $options, 'max_height' );
		if( $image->getImageFormat() === 'GIF' ) {
			// Handle animated GIFs:
			$image = $image->coalesceImages();

			$max_width  = $img_width = $image->getImageWidth();
			$max_height = $img_height = $image->getImageHeight();
			if( $mw ) $max_width = $options['max_width'];
			if( $mh ) $max_height = $options['max_height'];
			$scale      = min(
				$max_width / $img_width,
				$max_height / $img_height
			);
			$new_width  = $img_width * $scale;
			$new_height = $img_height * $scale;
			do {
				$image->resizeImage(
					$new_width,
					$new_height,
					isset( $options['filter'] ) ? $options['filter'] : \imagick::FILTER_LANCZOS,
					isset( $options['blur'] ) ? $options['blur'] : 1,
					$new_width && $new_height // fit image into constraints if not to be cropped
				);
			} while( $image->nextImage() );
			$image   = $image->deconstructImages();
			$success = $image->writeImages( $new_file_path, true );
			$image->clear();
			$image->destroy();

			return $success;
		} else {
			$image_oriented = false;
			if( ! empty( $options['auto_orient'] ) ) {
				$image_oriented = $this->imagick_orient_image( $image );
			}
			$max_width  = $img_width = $image->getImageWidth();
			$max_height = $img_height = $image->getImageHeight();
			if( $mw ) $max_width = $options['max_width'];
			if( $mh ) $max_height = $options['max_height'];
			$scale       = min(
				$max_width / $img_width,
				$max_height / $img_height
			);
			$image_strip = ( isset( $options['strip'] ) ? $options['strip'] : false );
			if( ! $image_oriented && $scale >= 1 && ! $image_strip && empty( $options["jpeg_quality"] ) ) {
				if( $file_path !== $new_file_path ) {
					return copy( $file_path, $new_file_path );
				}

				return true;
			}
			$crop = ( isset( $options['crop'] ) ? $options['crop'] : false );

			if( $crop ) {
				$x = 0;
				$y = 0;
				if( ( $img_width / $img_height ) >= ( $max_width / $max_height ) ) {
					$new_width = 0; // Enables proportional scaling based on max_height
					$x         = ( $img_width / ( $img_height / $max_height ) - $max_width ) / 2;
				} else {
					$new_height = 0; // Enables proportional scaling based on max_width
					$y          = ( $img_height / ( $img_width / $max_width ) - $max_height ) / 2;
				}
			} else {
				$new_width  = $img_width * $scale;
				$new_height = $img_height * $scale;
			}
			$success = $image->resizeImage(
				$new_width,
				$new_height,
				isset( $options['filter'] ) ? $options['filter'] : \imagick::FILTER_LANCZOS,
				isset( $options['blur'] ) ? $options['blur'] : 1,
				$new_width && $new_height // fit image into constraints if not to be cropped
			);
			if( $success && $crop ) {
				$success = $image->cropImage(
					$max_width,
					$max_height,
					$x,
					$y
				);
				if( $success ) {
					$success = $image->setImagePage( $max_width, $max_height, 0, 0 );
				}
			}
			$type = strtolower( substr( strrchr( $file_name, '.' ), 1 ) );
			switch( $type ) {
				case 'jpg':
				case 'jpeg':
					if( ! empty( $options['jpeg_quality'] ) ) {
						$image->setImageCompression( \imagick::COMPRESSION_JPEG );
						$image->setImageCompressionQuality( $options['jpeg_quality'] );
					}
				break;
			}
			if( $image_strip ) {
				$image->stripImage();
			}

			return $success && $image->writeImage( $new_file_path );
		}
	}
}
