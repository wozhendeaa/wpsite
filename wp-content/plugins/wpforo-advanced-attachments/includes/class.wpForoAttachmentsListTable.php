<?php

require_once( ABSPATH . 'wp-admin/includes/template.php' );
require_once( ABSPATH . 'wp-admin/includes/screen.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class wpForoAttachmentsListTable extends WP_List_Table {
	/** ************************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 ***************************************************************************/
	function __construct() {
		//Set parent defaults
		parent::__construct( [
			                     'singular' => 'attach',     //singular name of the listed records
			                     'plural'   => 'attachs',    //plural name of the listed records
			                     'ajax'     => false,        //does this table support ajax?
			                     'screen'   => 'wpForoAttachments',
		                     ] );

	}


	/** ************************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 * @param string $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default( $item, $column_name ) {
		$basename = basename( $item['fileurl'] );
		$filetype = WPF_ATTACH()->tools->get_file_type( $basename );

		switch( $column_name ) {
			case 'size':
				return wpforo_human_filesize( $item[ $column_name ] );
			case 'userid':
				$userdata = get_userdata( $item[ $column_name ] );

				return ( ! empty( $userdata->user_nicename ) ? urldecode( $userdata->user_nicename ) : $item[ $column_name ] );
			case 'type':
				return $filetype;
			case 'posts':
				return ( $item[ $column_name ] == 0 ? WPF_ATTACH()->rebuild_post_count( $item['attachid'] ) : $item[ $column_name ] );
			case 'link':
				$attach_html = '';
				if( $filetype == 'picture' ) {
					$thumb_fileurl = WPF_ATTACH()->tools->get_public_url( $item, true );
					if( isset( WPF_ATTACH()->options['image_caption'] ) && WPF_ATTACH()->options['image_caption'] ) {
						$attach_html .= '<div class="wpfa-item wpfa-img"><a href="' . WPF_ATTACH()->tools->get_public_url( $item ) . '" data-gallery="#wpf-content-blueimp-gallery"><img src="' . $thumb_fileurl . '"/></a><div class="wpfa-info">' . urldecode( basename( $item['filename'] ) ) . '</div></div>';
					} else {
						$attach_html .= '<a href="' . WPF_ATTACH()->tools->get_public_url( $item ) . '" data-gallery="#wpf-content-blueimp-gallery"><img width="100" src="' . $thumb_fileurl . '"/></a>';
					}
				} elseif( $filetype == 'video' ) {
					$attach_html .= '<video src="' . WPF_ATTACH()->tools->get_public_url( $item ) . '" controls></video>';
				} elseif( $filetype == 'audio' ) {
					$attach_html .= '<audio src="' . WPF_ATTACH()->tools->get_public_url( $item ) . '" controls></audio>';
				} else {
					$attach_html .= '<div class="wpfa-item wpfa-file"><a href="' . WPF_ATTACH()->tools->get_public_url( $item ) . '" target="_blank" download="' . $item['filename'] . '">' . $this->get_file_fa_ico( $item['fileurl'] ) . urldecode( basename( $item['filename'] ) ) . '</a><div class="wpfa-info"></div></div>';
				}

				return $attach_html;
			case 'filename':
				return '<div class="wpfattach-filename-wrap" data-attachid="' . $item['attachid'] . '"><span class="wpfattach-filename">' . $item[ $column_name ] . '</span><span class="dashicons dashicons-edit"></span> </div>';
			default:
				return $item[ $column_name ];
		}

	}


	/** ************************************************************************
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 *
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************@see WP_List_Table::::single_row_columns()
	 */
	function column_attachid( $item ) {
		$href = wp_nonce_url( sprintf( '?page=%s&action=%s&attachid=%s', $_REQUEST['page'], 'delete', $item['attachid'] ), 'delete_attach_' . $item['attachid'] );
		//Build row actions
		$actions = [
			'delete' => '<a onclick="return confirm(\'' . wpforo_phrase( "Are you sure you want to DELETE this item?", false ) . '\');" href="' . $href . '">Delete</a>',
		];

		//Return the title contents
		return sprintf(
			         '%1$s %2$s',
			/*$1%s*/ $item['attachid'],
			/*$2%s*/ $this->row_actions( $actions )
		);
	}


	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************@see WP_List_Table::::single_row_columns()
	 */
	function column_cb( $item ) {
		return sprintf(
			         '<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ 'attachids',  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['attachid']         //The value of the checkbox should be the record's id
		);
	}


	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************@see WP_List_Table::::single_row_columns()
	 */
	function get_columns() {
		$columns = [
			'cb'       => '<input type="checkbox" />', //Render a checkbox instead of text
			'attachid' => 'ID',
			'link'     => 'Link',
			'filename' => 'File Name',
			'mime'     => 'Type',
			'size'     => 'Size',
			'userid'   => 'User',
			'posts'    => 'Posts',
		];

		return $columns;
	}


	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = [
			'attachid' => [ 'attachid', false ],     //true means it's already sorted
			'userid'   => [ 'userid', false ],
			'size'     => [ 'size', false ],
			'posts'    => [ 'posts', false ],
			'mime'     => [ 'mime', false ],
		];

		return $sortable_columns;
	}


	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_bulk_actions() {
		$actions = [
			'delete' => 'Delete',
		];

		return $actions;
	}


	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if( wpforo_is_admin() && 'delete' === $this->current_action() ) {
			$d = false;
			if( ! empty( $_REQUEST['attachid'] ) && check_admin_referer( 'delete_attach_' . $_REQUEST['attachid'] ) ) $attachids = (array) $_REQUEST['attachid'];
			if( empty( $attachids ) && ! empty( $_REQUEST['attachids'] ) && check_admin_referer( 'bulk-attachs' ) ) $attachids = $_REQUEST['attachids'];
			if( ! empty( $attachids ) ) {
				foreach( $attachids as $attachid ) {
					if( WPF_ATTACH()->remove_file( $attachid ) ) {
						$d = true;
						WPF_ATTACH()->delete( $attachid );
					}
				}
			}
			if( $d ) {
				WPF()->notice->add( 'File(s) Deleted', 'success' );
			} else {
				WPF()->notice->add( 'File(s) delete Error', 'error' );
			}
			wp_safe_redirect( wp_get_referer() );
			exit();
		}

		if( isset( $_REQUEST['filter'] ) && $_REQUEST['filter'] == 'possible-spam' && isset( $_REQUEST['spam'] ) && $_REQUEST['spam'] == 'empty' && check_admin_referer( 'bulk-spam-empty' ) ) {
			if( $attachids = WPF_ATTACH()->search_possible_spams() ) {
				$d = false;
				foreach( $attachids as $attachid ) {
					if( WPF_ATTACH()->remove_file( $attachid ) ) {
						$d = true;
						WPF_ATTACH()->delete( $attachid );
					}
				}

				if( $d ) {
					WPF()->notice->add( 'File(s) Deleted', 'success' );
				} else {
					WPF()->notice->add( 'File(s) delete Error', 'error' );
				}
				wp_safe_redirect( wp_get_referer() );
				exit();
			}
		}
	}


	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items() {
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = wpforo_get_option( 'count_per_page', 10 );


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$args = [];
		if( isset( $_REQUEST['s'] ) && $_REQUEST['s'] ) {
			$args['include'] = WPF_ATTACH()->search( $_REQUEST['s'] );
		}
		if( isset( $_REQUEST['userid'] ) && $_REQUEST['userid'] != - 1 ) $args['userid'] = sanitize_text_field( $_REQUEST['userid'] );
		if( isset( $_REQUEST['mime'] ) && $_REQUEST['mime'] != - 1 ) $args['mime'] = sanitize_text_field( $_REQUEST['mime'] );
		if( isset( $_REQUEST['orderby'] ) ) $args['orderby'] = sanitize_text_field( $_REQUEST['orderby'] );
		if( isset( $_REQUEST['order'] ) ) $args['order'] = sanitize_text_field( $_REQUEST['order'] );

		$paged             = $this->get_pagenum();
		$args['offset']    = ( $paged - 1 ) * $per_page;
		$args['row_count'] = $per_page;

		if( isset( $_REQUEST['filter'] ) && $_REQUEST['filter'] == 'possible-spam' ) {
			unset( $args['include'], $args['userid'], $args['mime'] );
			$args['include'] = WPF_ATTACH()->search_possible_spams();
		}

		$items_count = 0;
		$this->items = ( isset( $args['include'] ) && empty( $args['include'] ) ? [] : WPF_ATTACH()->get_attachs( $args, $items_count ) );

		$this->set_pagination_args( [
			                            'total_items' => $items_count,                  //WE have to calculate the total number of items
			                            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			                            'total_pages' => ceil( $items_count / $per_page )   //WE have to calculate the total number of pages
		                            ] );
	}

	public function users_dropdown() {
		?>
        <select name="userid">
            <option value="-1">-- All Users --</option>

			<?php
			if( $userids = WPF_ATTACH()->get_distinct_userids() ) {
				$current_userid = - 1;
				if( isset( $_REQUEST['userid'] ) ) $current_userid = $_REQUEST['userid'];
				foreach( $userids as $userid ) {
					$userdata = get_userdata( $userid );
					?>
                    <option value="<?php echo $userid ?>" <?php echo( $current_userid == $userid ? 'selected' : '' ) ?> > <?php echo( ! empty( $userdata->user_nicename ) ? urldecode( $userdata->user_nicename ) : $userid ) ?> </option>
					<?php
				}
			}
			?>

        </select>
		<?php
	}

	public function mimes_dropdown() {
		?>
        <select name="mime">
            <option value="-1">-- All Types --</option>

			<?php
			if( $mimes = WPF_ATTACH()->get_distinct_mimes() ) {
				$current_mime = - 1;
				if( isset( $_REQUEST['mime'] ) ) $current_mime = sanitize_text_field( $_REQUEST['mime'] );
				foreach( $mimes as $mime ) {
					?>
                    <option value="<?php echo $mime ?>" <?php echo( $current_mime == $mime ? 'selected' : '' ) ?> > <?php echo $mime ?> </option>
					<?php
				}
			}
			?>

        </select>
		<?php
	}

}
