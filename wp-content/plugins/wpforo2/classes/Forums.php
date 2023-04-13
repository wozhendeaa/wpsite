<?php

namespace wpforo\classes;

use stdClass;

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

class Forums {
	static $cache = [ 'forums' => [], 'forum' => [], 'item' => [] ];

	public static function _get_forumids( $table, $layout = null ) {
        $sql = "SELECT `forumid` FROM `$table` WHERE `status` = 1";
        if( !is_null( $layout ) ) $sql .= WPF()->db->prepare( " AND `layout` = %d", $layout );
		return WPF()->db->get_col($sql);
    }

	public static function get_forumids( $table, $layout = null ) {
        return wpforo_ram_get( [self::class, '_get_forumids'], $table, $layout );
    }

	public function __construct() {
		$this->init_hooks();
	}

	public function reset() {
		self::$cache = [ 'forums' => [], 'forum' => [], 'item' => [] ];
	}

	private function init_hooks() {
		add_action( 'wpforo_after_add_usergroup',  [$this, 'after_add_usergroup'] );

        add_action( 'wpforo_after_merge_forum',    [$this, 'after_merge_forum'], 10, 2 );

        add_action( 'wpforo_after_add_topic',      [$this, 'after_add_topic'] );
		add_action( 'wpforo_after_delete_topic',   [$this, 'after_delete_topic'] );
		add_action( 'wpforo_topic_status_update',  [$this, 'after_topic_status_update'] );
        add_action( 'wpforo_topic_private_update', [$this, 'topic_private_update'] );
        add_action( 'wpforo_after_merge_topic',    [$this, 'after_merge_topic'], 10, 2 );
        add_action( 'wpforo_after_move_topic',     [$this, 'after_move_topic'], 10, 2 );

        add_action( 'wpforo_after_add_post',       [$this, 'after_add_post'], 10, 3 );
        add_action( 'wpforo_after_delete_post',    [$this, 'after_delete_post'] );
		add_action( 'wpforo_post_status_update',   [$this, 'after_post_status_update'] );
	}

	public function get_cache( $var ) {
		if( isset( self::$cache[ $var ] ) ) return self::$cache[ $var ];
	}

	private function unique_slug( $slug, $parentid = 0, $forumid = 0 ) {
		$new_slug = wpforo_text( $slug, 250, false );
		$forumid  = intval( $forumid );
		$i        = 2;
		while( ! WPF()->can_use_this_slug( $new_slug ) || WPF()->db->get_var( "SELECT `forumid` FROM " . WPF()->tables->forums . " WHERE `slug` = '" . esc_sql( $new_slug ) . "'" . ( $forumid ? ' AND `forumid` != ' . intval( $forumid ) : '' ) ) ) {
			if( ! isset( $parent_slug ) && $parentid = intval( $parentid ) ) {
				$parent_slug = WPF()->db->get_var( "SELECT `slug` FROM " . WPF()->tables->forums . " WHERE `forumid` = " . intval( $parentid ) );
				$new_slug    = $parent_slug . "-" . wpforo_text( $slug, 250, false );
			} else {
				$new_slug = wpforo_text( $slug, 250, false ) . '-' . $i ++;
			}
		}

		return $new_slug;
	}

	public function add( $args = [], $checkperm = true ) {
		if( $checkperm && ! WPF()->usergroup->can_manage_forum() ) {
			WPF()->notice->add( 'Permission denied for add forum', 'error' );

			return false;
		}

		if( empty( $args ) && empty( $_REQUEST['forum'] ) ) return false;
		if( empty( $args ) && ! empty( $_REQUEST['forum'] ) ) $args = $_REQUEST['forum'];

		$args['title'] = sanitize_text_field( stripslashes( wpfval( $args, 'title' ) ) );
		if( ! $args['title'] ) {
			WPF()->notice->add( 'Please insert required fields!', 'error' );

			return false;
		}

		$args['title']    = wpforo_text( $args['title'], 250, false );
		$args['parentid'] = (int) wpfval( $args, 'parentid' );

		$args['slug'] = ( $ss = trim( sanitize_title( wpfval( $args, 'slug' ) ) ) ) ? $ss : ( ( $st = trim( sanitize_title( $args['title'] ) ) ) ? $st : md5( time() ) );
		$args['slug'] = $this->unique_slug( $args['slug'], $args['parentid'] );

		$args['description'] = wpforo_kses( stripslashes( wpfval( $args, 'description' ) ) );

		$group_access_relation = WPF()->usergroup->get_usergroup_access_relation();
		$args['permission']    = ( (array) wpfval( $args, 'permission' ) ) + $group_access_relation;
		$args['permission']    = serialize( array_map( 'sanitize_text_field', $args['permission'] ) );

		$args['meta_key']  = sanitize_text_field( stripslashes( wpfval( $args, 'meta_key' ) ) );
		$args['meta_desc'] = sanitize_text_field( stripslashes( wpfval( $args, 'meta_desc' ) ) );
		$args['icon']      = sanitize_text_field( wpfval( $args, 'icon' ) );
		$args['cover']     = wpforo_bigintval( wpfval( $args, 'cover' ) );
		$args['cover_height'] = intval( wpfval( $args, 'cover_height' ) );
        if( ! $args['cover_height'] ) $args['cover_height'] = 150;
		$args['topics']    = (int) wpfval( $args, 'topics' );
		$args['posts']     = (int) wpfval( $args, 'posts' );
		$args['order']     = (int) wpfval( $args, 'order' );

		$args['status'] = (int) wpfval( $args, 'status' );
		if( ! $args['status'] ) $args['status'] = 1;

		$args['color'] = sanitize_text_field( stripslashes( wpfval( $args, 'color' ) ) );
		if( ! $args['color'] ) $args['color'] = '#888888';

		$args['is_cat'] = (int) wpfval( $args, 'is_cat' );
		if( ! $args['parentid'] ) $args['is_cat'] = 1;

		$args['layout'] = (int) wpfval( $args, 'layout' );
		if( $args['parentid'] ) {
			$layout = (int) WPF()->db->get_var( "SELECT `layout` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = " . $args['parentid'] );
			if( $layout ) $args['layout'] = $layout;
		}
		if( ! $args['layout'] ) $args['layout'] = 1;

		$args = apply_filters( 'wpforo_before_add_forum', $args, $checkperm );
		if( ! $args ) return false;

		if( WPF()->db->insert( WPF()->tables->forums,
		                       [
			                       'title'        => $args['title'],
			                       'slug'         => $args['slug'],
			                       'description'  => $args['description'],
			                       'parentid'     => $args['parentid'],
			                       'icon'         => $args['icon'],
			                       'cover'        => $args['cover'],
			                       'cover_height' => $args['cover_height'],
			                       'topics'       => $args['topics'],
			                       'posts'        => $args['posts'],
			                       'permissions'  => $args['permission'],
			                       'meta_key'     => $args['meta_key'],
			                       'meta_desc'    => $args['meta_desc'],
			                       'status'       => $args['status'],
			                       'is_cat'       => $args['is_cat'],
			                       'layout'   => $args['layout'],
			                       'order'        => $args['order'],
			                       'color'        => $args['color'],
		                       ],
		                       [ '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', ]
        ) ) {
			$args['forumid'] = WPF()->db->insert_id;

			do_action( 'wpforo_after_add_forum', $args, $checkperm );

			$this->delete_tree_cache();
			wpforo_clean_cache();
			WPF()->notice->add( 'Your forum successfully added', 'success' );

			return $args['forumid'];
		}

		WPF()->notice->add( 'Can\'t add forum', 'error' );

		return false;
	}

	public function edit( $args = [], $checkperm = true ) {
		if( $checkperm && ! WPF()->usergroup->can_manage_forum() ) {
			WPF()->notice->add( 'Permission denied for edit forum', 'error' );

			return false;
		}

		if( empty( $args ) && empty( $_REQUEST['forum'] ) ) return false;
		if( empty( $args ) && ! empty( $_REQUEST['forum'] ) ) $args = $_REQUEST['forum'];
		if( ! isset( $args['forumid'] ) && isset( $_GET['id'] ) ) $args['forumid'] = $_GET['id'];

		$args['forumid'] = (int) wpfval( $args, 'forumid' );
		if( ! $forum = $this->get_forum( $args['forumid'] ) ) {
			WPF()->notice->add( 'Forum update error', 'error' );

			return false;
		}

		$args['title'] = sanitize_text_field( stripslashes( wpfval( $args, 'title' ) ) );
		if( ! $args['title'] ) {
			WPF()->notice->add( 'Please insert required fields!', 'error' );

			return false;
		}

		$args['title']    = wpforo_text( $args['title'], 250, false );
		$args['parentid'] = (int) wpfval( $args, 'parentid' );
		if( $args['forumid'] === $args['parentid'] ) $args['parentid'] = (int) $forum['parentid'];

		$args['slug'] = ( $ss = trim( sanitize_title( wpfval( $args, 'slug' ) ) ) ) ? $ss : ( ( $st = trim( sanitize_title( $args['title'] ) ) ) ? $st : md5( time() ) );
		$args['slug'] = $this->unique_slug( $args['slug'], $args['parentid'], $args['forumid'] );

		$args['description'] = wpforo_kses( stripslashes( wpfval( $args, 'description' ) ) );

		$group_access_relation = WPF()->usergroup->get_usergroup_access_relation();
		$args['permission']    = ( (array) wpfval( $args, 'permission' ) ) + $group_access_relation;
		$args['permission']    = serialize( array_map( 'sanitize_text_field', $args['permission'] ) );

		$args['meta_key']  = sanitize_text_field( stripslashes( wpfval( $args, 'meta_key' ) ) );
		$args['meta_desc'] = sanitize_text_field( stripslashes( wpfval( $args, 'meta_desc' ) ) );
		$args['icon']      = sanitize_text_field( wpfval( $args, 'icon' ) );
		$args['cover']     = wpforo_bigintval( wpfval( $args, 'cover' ) );
		$args['cover_height'] = intval( wpfval( $args, 'cover_height' ) );
		if( ! $args['cover_height'] ) $args['cover_height'] = 150;
		$args['topics']    = (int) wpfval( $args, 'topics' );
		$args['posts']     = (int) wpfval( $args, 'posts' );
		$args['order']     = (int) wpfval( $args, 'order' );

		$args['status'] = (int) wpfval( $args, 'status' );
		if( ! $args['status'] ) $args['status'] = 1;

		$args['color'] = sanitize_text_field( stripslashes( wpfval( $args, 'color' ) ) );
		if( ! $args['color'] ) $args['color'] = '#888888';

		$args['is_cat'] = (int) wpfval( $args, 'is_cat' );
		if( ! $args['parentid'] ) $args['is_cat'] = 1;

		$args['layout'] = (int) wpfval( $args, 'layout' );
		if( $args['parentid'] ) {
			$layout = (int) WPF()->db->get_var( "SELECT `layout` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = " . $args['parentid'] );
			if( $layout ) $args['layout'] = $layout;
		}
		if( ! $args['layout'] ) $args['layout'] = 1;

		$args = apply_filters( 'wpforo_before_edit_forum', $args, $forum, $checkperm );
		if( ! $args ) return false;

		if( false !== WPF()->db->update( WPF()->tables->forums,
		                                 [
			                                 'title'        => $args['title'],
			                                 'slug'         => $args['slug'],
			                                 'description'  => $args['description'],
			                                 'parentid'     => $args['parentid'],
			                                 'icon'         => $args['icon'],
			                                 'cover'        => $args['cover'],
			                                 'cover_height' => $args['cover_height'],
			                                 'permissions'  => $args['permission'],
			                                 'meta_key'     => $args['meta_key'],
			                                 'meta_desc'    => $args['meta_desc'],
			                                 'status'       => $args['status'],
			                                 'is_cat'       => $args['is_cat'],
			                                 'layout'   => $args['layout'],
			                                 'color'        => $args['color'],
		                                 ],
		                                 [ 'forumid' => $args['forumid'] ],
		                                 [ '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%s' ],
		                                 [ '%d' ] ) ) {
			if( $childs = $this->get_childs( $args['forumid'] ) ) {
				$sql = "UPDATE `" . WPF()->tables->forums . "` SET `layout` = " . $args['layout'] . " WHERE `forumid` IN(" . implode( ',', $childs ) . ")";
				WPF()->db->query( $sql );
			}

			do_action( 'wpforo_after_edit_forum', $args, $forum, $checkperm );

			$this->delete_tree_cache();
			wpforo_clean_cache();
			WPF()->notice->add( 'Forum successfully updated', 'success' );

			return $args['forumid'];
		}

		WPF()->notice->add( 'Forum update error', 'error' );

		return false;
	}

	public function delete( $forumid = 0, $checkperm = true ) {
		$forumid = intval( $forumid );
		if( ! $forumid && isset( $_REQUEST['id'] ) ) $forumid = intval( $_REQUEST['id'] );

		if( $checkperm && ! WPF()->usergroup->can_manage_forum() ) {
			WPF()->notice->add( 'Permission denied for delete forum', 'error' );

			return false;
		}

		$forumids   = $this->get_childs( $forumid );
		$forumids[] = $forumid;
		foreach( $forumids as $forumid ) $this->__delete( $forumid );

		$this->delete_tree_cache();
		wpforo_clean_cache();
		WPF()->notice->add( 'Your forum successfully deleted', 'success' );

		return true;
	}

	private function __delete( $forumid ) {
		$forumid = intval( $forumid );
		// START delete topic posts include first post
		if( $topicids = WPF()->db->get_col( "SELECT `topicid` FROM " . WPF()->tables->topics . " WHERE `forumid` = " . $forumid ) ) {
			foreach( $topicids as $topicid ) {
				WPF()->topic->delete( $topicid, false, false );
			}
		}
		// END delete topic posts include first post

		WPF()->db->delete( WPF()->tables->forums, [ 'forumid' => $forumid ], [ '%d' ] );

		do_action( 'wpforo_after_delete_forum', $forumid );
	}

	public function merge( $forumid = 0, $mergeid = 0 ) {
		$forumid = intval( $forumid );
		$mergeid = intval( $mergeid );

		if( ! $forumid && isset( $_REQUEST['id'] ) ) $forumid = intval( $_REQUEST['id'] );
		if( ! $mergeid && isset( $_REQUEST['forum']['mergeid'] ) ) $mergeid = intval( $_REQUEST['forum']['mergeid'] );

		if( ! $forumid || ! $mergeid ) return false;

		if( $child_forumids = $this->get_child_forums( $forumid ) ) {
			$forumids = implode( ',', $child_forumids );
			$merge_layout = $this->get_layout( $mergeid );

			if( ! WPF()->db->query( "UPDATE " . WPF()->tables->forums . " SET `parentid` = " . $mergeid . ", `layout` = " . $merge_layout . " WHERE `forumid` IN(" . $forumids . ")" ) ) {
				WPF()->notice->add( 'Forum merging error', 'error' );

				return false;
			}

            if( $childs = $this->get_childs( $forumid ) ){
	            WPF()->db->query( "UPDATE " . WPF()->tables->forums . " SET `layout` = " . $merge_layout . " WHERE `forumid` IN(" . implode( ',', $childs ) . ")" );
            }
		}

		WPF()->db->update( WPF()->tables->topics, [ 'forumid' => $mergeid ], [ 'forumid' => $forumid ], [ '%d' ], [ '%d' ] );
		WPF()->db->update( WPF()->tables->posts,  [ 'forumid' => $mergeid ], [ 'forumid' => $forumid ], [ '%d' ], [ '%d' ] );

		if( WPF()->db->delete( WPF()->tables->forums, [ 'forumid' => $forumid ], [ '%d' ] ) ) {

            do_action( 'wpforo_after_merge_forum', $forumid, $mergeid );

			$this->delete_tree_cache();
			wpforo_clean_cache( 'forum' );
			WPF()->notice->add( 'Forum is successfully merged', 'success' );
			return true;
		}

		WPF()->notice->add( 'Forum merging error', 'error' );

		return false;
	}

	public function rebuild_last_infos( $forumid ) {
		if( ! $forumid = intval( $forumid ) ) return;

		$last_topicid   = 0;
		$last_postid    = 0;
		$last_userid    = 0;
		$last_post_date = '0000-00-00 00:00:00';

		if( $last_topics = WPF()->topic->get_topics( [
			                                             'forumid'   => $forumid,
			                                             'status'    => 0,
			                                             'private'   => 0,
			                                             'orderby'   => 'topicid',
			                                             'order'     => 'DESC',
			                                             'row_count' => 1,
		                                             ] ) ) {
			$last_topic   = $last_topics[0];
			$last_topicid = $last_topic['topicid'];
		}

		$sql = "SELECT `postid` FROM `" . WPF()->tables->posts . "` WHERE `status` = 0 AND `private` = 0 AND `forumid` = %d ORDER BY `created` DESC, `postid` DESC LIMIT 1";
		if( $last_postid = WPF()->db->get_var( WPF()->db->prepare( $sql, $forumid ) ) ) {
			if( $last_post_data = WPF()->post->get_post( $last_postid ) ) {
				$last_postid    = $last_post_data['postid'];
				$last_userid    = $last_post_data['userid'];
				$last_post_date = $last_post_data['created'];
			}
		} else {
			$last_postid = 0;
		}

		$parent_ids = [];
		$this->get_parents( $forumid, $parent_ids );
		$parent_ids = array_unique( array_filter( array_map( 'wpforo_bigintval', (array) $parent_ids ) ) );

		if( $parent_ids ) {
			$sql = "UPDATE `" . WPF()->tables->forums . "` 
                SET `last_topicid` = %d,
                `last_postid` = %d,
                `last_userid` = %d,
                `last_post_date` = %s
                WHERE `forumid` IN(" . implode( ',', $parent_ids ) . ")";
			WPF()->db->query( WPF()->db->prepare( $sql, $last_topicid, $last_postid, $last_userid, $last_post_date ) );

            $parent_ids = $this->get_parent_forumids_static( $forumid );
            if( !empty( $parent_ids ) ){
                foreach( $parent_ids as $parent_id ){
                    wpforo_clean_cache( 'forum-soft', $parent_id );
                }
            }
            wpforo_clean_cache( 'forum-soft' );
		}
	}

	public function rebuild_stats( $forumid ) {
		if( ! $forumid = intval( $forumid ) ) return false;
		$topics = WPF()->topic->get_count( [ 'forumid' => $forumid, 'status' => 0, 'private' => 0 ] );
		$posts  = WPF()->post->get_count( [ 'forumid' => $forumid, 'status' => 0, 'private' => 0 ] );

		if( false !== WPF()->db->update( WPF()->tables->forums, [ 'topics' => $topics, 'posts' => $posts ], [ 'forumid' => $forumid ], [ '%d', '%d' ], [ '%d' ] ) ) {
            $parent_ids = $this->get_parent_forumids_static($forumid);
            if( !empty( $parent_ids ) ){
                foreach( $parent_ids as $parent_id ){
                    wpforo_clean_cache( 'forum-soft', $parent_id );
                }
            }
            wpforo_clean_cache( 'forum-soft' );
			return true;
		}

		return false;
	}

    function get_parent_forumids_static( $forumid ){
        $parent_ids = [];
        $level_0 = wpforo_forum( $forumid );
        if( wpfval( $level_0, 'parentid') ) {
            $level_1 = wpforo_forum( $level_0['parentid'] );
            $parent_ids[] = $level_1['forumid'];
            if( wpfval( $level_1, 'parentid') ){
                $level_2 = wpforo_forum( $level_1['parentid'] );
                $parent_ids[] = $level_2['forumid'];
                if( wpfval( $level_2, 'parentid') ){
                    $level_3 = wpforo_forum( $level_2['parentid'] );
                    $parent_ids[] = $level_3['forumid'];
                }
            }
        }
        return $parent_ids;
    }

	function _get_forum( $args ) {
		$forum = [];
		if( ! $args ) return $forum;

		$default = [
			'forumid' => null,
			'slug'    => '',
			'status'  => null,
			'type'    => 'all',
		];
		if( ! is_array( $args ) ) {
			if( is_numeric( $args ) ) {
				$default['forumid'] = intval( $args );
				if( $default['forumid'] === WPF()->current_object['forumid'] ) return WPF()->current_object['forum'];
			} elseif( is_string( $args ) ) {
				$default['slug'] = trim( $args );
				if( $default['slug'] && $default['slug'] === wpfval( WPF()->current_object['forum'], 'slug' ) ) return WPF()->current_object['forum'];
			}
		}
		$args = wpforo_parse_args( $args, $default );

		$wheres = [];
		if( $args['forumid'] ) $wheres[] = "`forumid` = " . intval( $args['forumid'] );
		if( $args['slug'] ) $wheres[] = "`slug` = '" . esc_sql( $args['slug'] ) . "'";
		if( ! is_null( $args['status'] ) ) $wheres[] = "`status` = " . intval( $args['status'] );
		switch( $args['type'] ) {
			case 'category':
				$wheres[] = "`is_cat` = 1";
			break;
			case 'forum':
				$wheres[] = "`is_cat` = 0";
			break;
		}

		if( $wheres ) {
			$sql = "SELECT * FROM `" . WPF()->tables->forums . "` WHERE " . implode( " AND ", $wheres );
			if( $forum = WPF()->db->get_row( $sql, ARRAY_A ) ) {
				if( ! $forum['layout'] ) $forum['layout'] = 1;
				$forum['url'] = $this->get_forum_url( $forum );
                if( $forum['cover'] = wpforo_bigintval( wpfval($forum, 'cover') ) ){
	                $image = wp_get_attachment_image_src( $forum['cover'], 'full' );
	                $forum['cover_url'] = (string) wpfval($image, 0);
                }else{
	                $forum['cover_url'] = '';
                }
			}
		}

		return apply_filters( 'wpforo_get_forum', $forum, $args );
	}

	public function get_forum( $args ) {
		return wpforo_ram_get( [ $this, '_get_forum' ], $args );
	}

	function get_forums( $args = [], &$items_count = 0, $count = false ) {
		$cache = WPF()->cache->on('forum');

		$default = [
			'include'        => [],  // array( 2, 10, 25 )
			'exclude'        => [],  // array( 2, 10, 25 )
			'parent_include' => [],  // array( 2, 10, 25 )
			'parent_exclude' => [],  // array( 2, 10, 25 )
			'parentid'       => null,
			'parent_slug'    => '',
			'status'         => null,
			'type'           => 'all',   // category, forum
			'orderby'        => 'order', // order by `field`
			'order'          => 'ASC',   // ASC DESC
			'offset'         => null,    // OFFSET
			'row_count'      => null,    // ROW COUNT
			'layout'         => null,    // 1, 2, 3, 4
		];

		$args = wpforo_parse_args( $args, $default );
		extract( $args, EXTR_OVERWRITE );

		$include        = wpforo_parse_args( $include );
		$exclude        = wpforo_parse_args( $exclude );
		$parent_include = wpforo_parse_args( $parent_include );
		$parent_exclude = wpforo_parse_args( $parent_exclude );

		$sql    = "SELECT * FROM `" . WPF()->tables->forums . "`";
		$wheres = [];

		if( ! empty( $include ) ) $wheres[] = "`forumid` IN(" . implode( ', ', array_map( 'intval', $include ) ) . ")";
		if( ! empty( $exclude ) ) $wheres[] = "`forumid` NOT IN(" . implode( ', ', array_map( 'intval', $exclude ) ) . ")";
		if( ! empty( $parent_include ) ) $wheres[] = "`parentid` IN(" . implode( ', ', array_map( 'intval', $parent_include ) ) . ")";
		if( ! empty( $parent_exclude ) ) $wheres[] = "`parentid` NOT IN(" . implode( ', ', array_map( 'intval', $parent_exclude ) ) . ")";
		if( $parentid != null ) $wheres[] = " `parentid` = " . intval( $parentid );
		if( $layout != null ) $wheres[] = " `layout` = " . intval( $layout );
		if( $status != null ) $wheres[] = " `status` = " . intval( $status );

		if( $type === 'category' ) {
			$wheres[] = " `is_cat` = 1";
		} elseif( $type === 'forum' ) {
			$wheres[] = " `is_cat` = 0";
		}

		if( $parent_slug != '' ) $wheres[] = "`slug` = '" . esc_sql( $parent_slug ) . "'";

		if( ! empty( $wheres ) ) $sql .= " WHERE " . implode( " AND ", $wheres );

		if( $count ) {
			$item_count_sql = preg_replace( '#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql );
			//				$item_count_sql = preg_replace('#ORDER.+$#is', '', $item_count_sql);
			if( $item_count_sql ) $items_count = WPF()->db->get_var( $item_count_sql );
		}

		$sql .= esc_sql( " ORDER BY `$orderby` " . $order );

		if( $row_count != null ) {
			if( $offset != null ) {
				$sql .= esc_sql( " LIMIT $offset,$row_count" );
			} else {
				$sql .= esc_sql( " LIMIT $row_count" );
			}
		}

		if( $cache ) {
			$object_key   = md5( $sql . WPF()->current_user_groupid );
			$object_cache = WPF()->cache->get( $object_key );
			if( ! empty( $object_cache ) ) {
				$items_count = $object_cache['items_count'];

				return $object_cache['items'];
			}
		}

		$forums = WPF()->db->get_results( $sql, ARRAY_A );

        $forums = array_map( function( $forum ){
	        if( $forum['cover'] = wpforo_bigintval( wpfval($forum, 'cover') ) ){
		        $image = wp_get_attachment_image_src( $forum['cover'], 'full' );
		        $forum['cover_url'] = (string) wpfval($image, 0);
	        }else{
		        $forum['cover_url'] = '';
	        }

            return $forum;
        }, $forums );

		$forums = apply_filters( 'wpforo_get_forums', $forums, $args );

		if( $cache && isset( $object_key ) && ! empty( $forums ) ) {
			self::$cache['forums'][ $object_key ]['items']       = $forums;
			self::$cache['forums'][ $object_key ]['items_count'] = $items_count;
		}

		return $forums;
	}

	function search( $needle, $fields = [] ) {

		if( $needle ) {
			$needle = sanitize_text_field( $needle );
			if( empty( $fields ) ) {
				$fields = [
					'title',
					'description',
					'meta_key',
					'meta_desc',
				];
			}

			$sql    = "SELECT `forumid` FROM `" . WPF()->tables->forums . "`";
			$wheres = [];

			foreach( $fields as $field ) {
				$wheres[] = "`" . esc_sql( $field ) . "` LIKE '%" . esc_sql( $needle ) . "%'";
			}

			$sql .= " WHERE " . implode( " OR ", $wheres );

			return WPF()->db->get_col( $sql );
		}

		return [];
	}

	function update_hierarchy() {
		if( is_array( $_REQUEST['forum'] ) && ! empty( $_REQUEST['forum'] ) ) {
			$i = 0;
			foreach( $_REQUEST['forum'] as $hierarchy ) {

				extract( $hierarchy );

				if( ! isset( $forumid ) || ! $forumid = intval( $forumid ) ) continue;

				if( false !== WPF()->db->update( WPF()->tables->forums, [
					                                                      'parentid' => ( isset( $parentid ) ? intval( $parentid ) : 0 ),
					                                                      'order'    => ( isset( $order ) ? intval( $order ) : 0 ),
				                                                      ], [ 'forumid' => intval( $forumid ) ], [
					                                 '%d',
					                                 '%d',
				                                 ], [ '%d' ] ) ) {
					$i ++;
				}

				if( isset( $parentid ) && $parentid = intval( $parentid ) ) {
					$layout = WPF()->db->get_var( "SELECT `layout` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = " . intval( $parentid ) );
					WPF()->db->query( "UPDATE `" . WPF()->tables->forums . "` SET `layout` = " . intval( $layout ) . " WHERE `forumid` = " . intval( $forumid ) );
				}

			}

			WPF()->db->query( "UPDATE `" . WPF()->tables->forums . "` SET `is_cat` = 0" );
			WPF()->db->query( "UPDATE `" . WPF()->tables->forums . "` SET `is_cat` = 1 WHERE `parentid` = 0" );

			if( $i ) {
				$this->delete_tree_cache();
				WPF()->notice->add( 'Forum hierarchy successfully updated', 'success' );
			} else {
				WPF()->notice->add( 'Cannot update forum hierarchy', 'error' );
			}

		}
	}

	/**
	 * @param int $forumid
	 *
	 * @return array array of child forumids
	 */
	public function get_childs( $forumid ) {
		$key = [ 'forums', 'get_childs', $forumid ];
		if( WPF()->ram_cache->exists( $key ) ) return WPF()->ram_cache->get( $key );

		if( wpforo_is_db_mysql8() ) {
			$forumids = $this->__get_childs_mysql8( $forumid );
		} else {
			$forumids = $this->__get_childs( $forumid );
		}

		WPF()->ram_cache->set( $key, $forumids );

		return $forumids;
	}

	/**
	 * @param int $forumid
	 *
	 * @return array array of child forumids
	 */
	private function __get_childs( $forumid ) {
		if( $forumid = intval( $forumid ) ) {
			$sql = "SELECT GROUP_CONCAT(
                        @id :=  ( 
                        SELECT GROUP_CONCAT(`forumid` ORDER BY `order` ASC) 
                            FROM  `" . WPF()->tables->forums . "` 
                            WHERE FIND_IN_SET( `parentid`, @id )
                        )
                    ) AS forumids
                    FROM ( SELECT  @id := %s ) vars 
                    STRAIGHT_JOIN `" . WPF()->tables->forums . "` 
                    WHERE @id IS NOT NULL";
			$sql = WPF()->db->prepare( $sql, $forumid );

			$forumids = explode( ',', (string) WPF()->db->get_var( $sql ) );

			return array_values( array_unique( array_filter( array_map( 'intval', $forumids ) ) ) );
		}

		return [];
	}

	/**
	 * @param int $forumid
	 *
	 * @return array array of child forumids
	 */
	private function __get_childs_mysql8( $forumid ) {
		if( $forumid = intval( $forumid ) ) {
			$sql = "WITH RECURSIVE `forum_path` AS (
              SELECT `forumid`, `parentid`
                FROM `" . WPF()->tables->forums . "`
                WHERE `parentid` = %d
              UNION
              SELECT f.`forumid`, f.`parentid`
                FROM `" . WPF()->tables->forums . "` AS f 
                INNER JOIN `forum_path` AS fp ON fp.`parentid` <> fp.`forumid` AND fp.`forumid` = f.`parentid`
            ) SELECT * FROM `forum_path`";
			$sql = WPF()->db->prepare( $sql, $forumid );

			$forumids = WPF()->db->get_col( $sql );

			return array_values( array_unique( array_filter( array_map( 'intval', $forumids ) ) ) );
		}

		return [];
	}

	// get forums tree for drop down menu

	/**
	 * Returns depth for this item.
	 *
	 * @param int $forumid
	 *
	 * @param int $depth
	 *
	 * @since 1.0.0
	 *
	 */
	function count_depth( $forumid, &$depth ) {
		if( wpforo_is_db_mysql8() ) {
			$depth = $this->__count_depth_mysql8( $forumid );

			return;
		}

		$sql = "SELECT `parentid` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = %d AND `parentid` <> %d";
		$sql = WPF()->db->prepare( $sql, intval( $forumid ), intval( $forumid ) );

		if( WPF()->ram_cache->exists( $sql ) ) {
			$parentid = WPF()->ram_cache->get( $sql );
		} else {
			$parentid = WPF()->db->get_var( $sql );
			WPF()->ram_cache->set( $sql, $parentid );
		}

		if( $parentid ) {
			$depth ++;
			$this->count_depth( $parentid, $depth );
		}
	}

	private function __count_depth_mysql8( $forumid ) {
		$sql = "WITH RECURSIVE `forum_path` AS(
            SELECT `forumid`, `parentid`, 0 AS `depth`
            FROM `" . WPF()->tables->forums . "`
            WHERE `forumid` = %d
            UNION
            SELECT f.`forumid`, f.`parentid`, `depth` + 1
            FROM `" . WPF()->tables->forums . "` f
            INNER JOIN `forum_path` fp ON fp.`parentid` <> fp.`forumid` AND fp.`parentid` = f.`forumid`
        ) SELECT `depth` FROM `forum_path` ORDER BY `depth` DESC LIMIT 1";

		return (int) WPF()->db->get_var( WPF()->db->prepare( $sql, intval( $forumid ) ) );
	}

	/**
	 * @param int $forumid
	 *
	 * @return array
	 */
	function get_child_forums( $forumid ) {
		$sql = "SELECT `forumid` 
                  FROM `" . WPF()->tables->forums . "` 
                  WHERE `parentid` = " . intval( $forumid ) . " 
                  AND `forumid` <> " . intval( $forumid ) . " 
                  ORDER BY `order`";

		return array_map( 'intval', WPF()->db->get_col( $sql ) );
	}

	function forum_list( $forumids, $type = 'select_box', $selected = [], $cats = true, $disabled = [] ) {
		static $old_depth;
		$disabled = (array) $disabled;
		$selected = (array) $selected;

		foreach( $forumids as $forumid ) {
			$forumid = intval( $forumid );
			if( ! $forumid || ( ! wpforo_is_admin() && ! WPF()->perm->forum_can( 'vf', $forumid ) ) || ( wpforo_is_admin() && ! WPF()->usergroup->can_manage_forum() ) ) continue;
            if( $type === 'subscribe_manager_form'  && ! WPF()->perm->forum_can( 'sb', $forumid ) ) continue;
			$depth = 0;
			$this->count_depth( $forumid, $depth );
            $forum = wpforo_forum( $forumid );
            $name = wpfval($forum, 'title');
            if( $type === 'select_box' ) { ?>
                <option value="<?php echo $forumid ?>" <?php echo( ( ! $cats && $depth == 0 || ( in_array( $forumid, $disabled ) ) ) ? ' disabled ' : '' );
                echo( in_array( $forumid, $selected ) ? ' selected ' : '' ) ?> > <?php echo esc_html( str_repeat( '— ', $depth ) . trim( $name ) ) ?></option><?php
			} elseif( $type === 'drag_menu' ) {
				switch( $forum['layout'] ) {
					case 2:
						$layout_name = 'Simplified Layout';
					break;
					case 3:
						$layout_name = 'Q&A Layout';
					break;
					case 4:
						$layout_name = 'Threaded Layout';
					break;
					default:
						$layout_name = 'Extended Layout';
				}
				?>

                <li id="menu-item-<?php echo $forumid ?>" class="menu-item menu-item-depth-<?php echo esc_attr( $depth ) ?>">
                    <input id="forumid-<?php echo $forumid ?>" type="hidden" name="forum[<?php echo $forumid ?>][forumid]"/>
                    <input id="parentid-<?php echo $forumid ?>" type="hidden" name="forum[<?php echo $forumid ?>][parentid]"/>
                    <input id="order-<?php echo $forumid ?>" type="hidden" name="forum[<?php echo $forumid ?>][order]"/>
                    <dl class="menu-item-bar">
                        <dt class="menu-item-handle forum_width">
                            <span class="item-title forumtitle"><span style="font-weight:400; cursor:help;" title="Forum ID"><?php echo $forumid; ?> &nbsp;|&nbsp;</span> <?php echo apply_filters( 'wpforo_dashboard_forums_item_name', esc_html($name), $forumid); ?></span>
                            <span class="item-controls">
                            	<span class="wpforo-cat-layout"><?php echo( $depth != 0 ? __( 'Topics', 'wpforo' ) . '&nbsp;(' . intval( $forum['topics'] ) . ')&nbsp;,&nbsp;' . __( 'Posts', 'wpforo' ) . '&nbsp;(' . intval( $forum['posts'] ) . ')&nbsp; | &nbsp;' : '' ) ?><?php echo( $depth == 0 ? '(&nbsp;<i>' . esc_html( $layout_name ) . '</i>&nbsp;)&nbsp; | &nbsp;' : '' ); ?></span>
								<span class="menu_add">
                                    <a href="<?php echo admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'forums' ) . '&action=add&parentid=' . $forumid ) ?>">
                                        <span class="dashicons dashicons-plus" title="<?php if( $depth ) : _e( 'Add a new Subforum', 'wpforo' ); else: _e( 'Add a new Forum in this Category', 'wpforo' ); endif; ?>"></span>
                                    </a>
                                </span> &nbsp;|&nbsp;
                                <span class="menu_edit">
                                    <a href="<?php echo admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'forums' ) . '&id=' . $forumid . '&action=edit' ) ?>">
                                        <span class="dashicons dashicons-edit" title="<?php _e( 'edit', 'wpforo' ) ?>"></span>
                                    </a>
                                </span>&nbsp;|&nbsp;
                                <?php if( WPF()->usergroup->can_manage_forum() ): ?>
                                    <span class="menu_delete">
                                        <a href="<?php echo admin_url( 'admin.php?page=' . wpforo_prefix_slug( 'forums' ) . '&id=' . $forumid . '&action=del' ) ?>">
                                            <span class="dashicons dashicons-trash" title="<?php _e( 'delete', 'wpforo' ) ?>"></span>
                                        </a>
                                    </span>&nbsp;|&nbsp;
                                <?php endif; ?>
								<span class="menu_view">
                                    <a href="<?php echo esc_url( wpforo_forum( $forumid, 'url' ) ); ?>">
                                        <span class="dashicons dashicons-visibility" title="<?php _e( 'View', 'wpforo' ) ?>"></span>
                                    </a>
                                </span>

                            </span>
                        </dt>
                    </dl>
                    <ul class="menu-item-transport"></ul>
                </li>

				<?php
			} elseif( $type === 'front_list' ) {
				if( isset( $old_depth ) && $old_depth == $depth ) echo '</dd><dd>';
				if( isset( $old_depth ) && $old_depth < $depth ) echo '<dl><dd>';
				if( isset( $old_depth ) && $old_depth > $depth ) echo '</dd></dl>';
				$old_depth = $depth;
                printf(
	                '<span class="wpf-dl-item %1$s"><a href="%2$s"><span class="wpf-circle wpf-s" style="display: inline-flex; %3$s"><i class="%4$s"></i></span><span class="wpf-dl-item-label">%5$s</span></a></span>',
	                ( in_array( $forumid, $selected ) ? 'wpf-dl-current' : '' ),
	                esc_url( wpforo_forum( $forumid, 'url' ) ),
                    ( ($forum['color'] = trim( $forum['color'] )) ? 'color: ' . $forum['color'] . ';' : ''),
	                $forum['icon'],
	                esc_html( $name )
                );
			} elseif( $type === 'subscribe_manager_form' ) {
				?>
                <li>
					<?php if( $depth > 0 ) :
						$forum_topic_attr = '';
						$forum_attr = '';
						if( key_exists( $forumid, $selected ) ) {
							if( $selected[ $forumid ] === 'forum-topic' ) {
								$forum_topic_attr = ' checked ';
							} elseif( $selected[ $forumid ] === 'forum' ) {
								$forum_attr = ' checked ';
							}
						}
						?>
                        <div class="wpf-sbs-div wpf-sbs-checkbox">
                            <input id="wpf_sbs_allposts_<?php echo $forumid ?>" type="checkbox" name="wpforo[forums][<?php echo $forumid ?>]" value="forum-topic" <?php echo $forum_topic_attr ?>><label class="wpf-sbsp" for="wpf_sbs_allposts_<?php echo $forumid ?>"><?php wpforo_phrase( 'topics and posts' ) ?></label>
                            <input id="wpf_sbs_alltopics_<?php echo $forumid ?>" type="checkbox" name="wpforo[forums][<?php echo $forumid ?>]" value="forum" <?php echo $forum_attr ?>><label class="wpf-sbst" for="wpf_sbs_alltopics_<?php echo $forumid ?>"><?php wpforo_phrase( 'topics' ) ?></label>
                        </div>
					<?php endif; ?>
                    <div class="wpf-sbs-div wpf-sbs-form-title<?php echo ( $depth > 0 ) ? ' wpf-sbs-forum' : ' wpf-sbs-cat'; ?>"><?php echo esc_html( str_repeat( '— ', $depth ) ) . trim( $name ) ?></div>
                </li>
				<?php
			}
			$subforums = $this->get_child_forums( $forumid );
			if( ! empty( $subforums ) ) {
				$this->forum_list( $subforums, $type, $selected, true, $disabled );
			}
		}
	}

	function tree( $type = 'select_box', $cats = true, $selected = [], $cache = true, $disabled = [], $parentids = [] ) {
		$disabled  = (array) $disabled;
		$selected  = (array) $selected;
		$parentids = (array) $parentids;
		if( ! $parentids ) $parentids = WPF()->db->get_col( "SELECT `forumid` FROM `" . WPF()->tables->forums . "` WHERE `parentid` = 0 ORDER BY `order`" );
		if( ! empty( $parentids ) ) {
			if( $cache && ! wpforo_is_admin() ) {
				$key                    = md5( serialize( $parentids ) . $type . (int) $cats . WPF()->current_user_groupid );
				$html                   = wpforo_get_option( 'forum_tree_' . $key, '' );
				$pattern_strip_selected = '#(<(?:option|input)[^<>]*?)[\r\n\t\s]*(?:selected|checked)[^\r\n\t\s]*?((?:[\r\n\t\s][^<>]*)?>)#isu';

				if( $html ) {
					if( $type === 'select_box' || $type === 'subscribe_manager_form' ) $html = preg_replace( $pattern_strip_selected, '$1$2', $html );
					if( $selected ) {
						if( $type === 'select_box' ) {
							foreach( $selected as $sfid ) {
								$html = str_replace( 'value="' . $sfid . '"', 'value="' . $sfid . '" selected ', $html );
							}
						} elseif( $type === 'subscribe_manager_form' ) {
							foreach( $selected as $forumid => $stype ) {
								$html = preg_replace( '#(name=[\'"]wpforo\[forums\]\[' . intval( $forumid ) . '\][\'"][^<>]*?value=[\'"]' . preg_quote( $stype ) . '[\'"]|value=[\'"]' . preg_quote( $stype ) . '[\'"][^<>]*?name=[\'"]wpforo\[forums\]\[' . intval( $forumid ) . '\][\'"])#isu', '$1 checked', $html );
							}
						}
					}
					echo $html;
				} elseif( function_exists( 'ob_start' ) ) {
					ob_start();
					$this->forum_list( $parentids, $type, $selected, $cats, $disabled );
					$html       = ob_get_clean();
					$cache_html = ( $type === 'select_box' ? preg_replace( $pattern_strip_selected, '$1$2', $html ) : $html );
					if( $type !== 'drag_menu' ) wpforo_update_option( 'forum_tree_' . $key, $cache_html );
					echo $html;
				}
			} else {
				$this->forum_list( $parentids, $type, $selected, $cats, $disabled );
			}
		}
	}

	public function delete_tree_cache() {
		WPF()->db->query( "DELETE FROM `" . WPF()->db->options . "` WHERE `option_name` REGEXP '^" . wpforo_prefix( 'forum_tree_' ) . "'" );
	}

	function parentid( $topicid = 0 ) {
		if( isset( $_GET['page'] ) && preg_match( '#^wpforo-(?:\d+-)?forums$#iu', $_GET['page'] ) ) {
			if( isset( $_GET['id'] ) ) return WPF()->db->get_var( "SELECT `parentid` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = " . intval( $_GET['id'] ) );
		} elseif( isset( $_GET['page'] ) && preg_match( '#^wpforo-(?:\d+-)?topics$#iu', $_GET['page'] ) ) {
			if( isset( $_GET['id'] ) ) return WPF()->db->get_var( "SELECT `forumid` FROM `" . WPF()->tables->topics . "` WHERE `topicid` = " . wpforo_bigintval( $_GET['id'] ) );
		} else {
			if( $topicid ) return WPF()->db->get_var( "SELECT `forumid` FROM `" . WPF()->tables->topics . "` WHERE `topicid` = " . wpforo_bigintval( $topicid ) );
		}
	}

	function permissions() {
		$access_arr = WPF()->perm->get_accesses();
		if( ! empty( $access_arr ) ) {

			if( isset( $_GET['id'] ) ) {
				if( $permissions_srlz = WPF()->db->get_var( "SELECT `permissions` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = " . intval( $_GET['id'] ) ) ) {
					$permissions_arr = unserialize( $permissions_srlz );
				}
			}

			if( $usergroups = WPF()->db->get_results( "SELECT `groupid`, `name` FROM `" . WPF()->tables->usergroups . "`", ARRAY_A ) ) {
				foreach( $usergroups as $usergroup ) {
					extract( $usergroup, EXTR_OVERWRITE );
					echo '
						<tr>
							<td>' . esc_html( $name ) . '</td>
							<td>
								<select name="forum[permission][' . intval( $groupid ) . ']">';
					foreach( $access_arr as $value ) {

						echo '<option value="' . esc_attr(
								$value['access']
							) . '" ' . ( ( isset( $permissions_arr[ $groupid ] ) && $value['access'] == $permissions_arr[ $groupid ] ) || ( ! isset( $permissions_arr[ $groupid ] ) && ( ( $name == 'Guest' && $value['access'] == 'read_only' ) || ( $name == 'Registered' && $value['access'] == 'standard' ) || ( $name == 'Customer' && $value['access'] == 'standard' ) || ( $name == 'Moderator' && $value['access'] == 'moderator' ) || ( $name == 'Admin' && $value['access'] == 'full' ) || ( $name != 'Guest' && $name != 'Registered' && $name != 'Customer' && $name != 'Moderator' && $name != 'Admin' && $value['access'] == 'standard' ) ) ) ? ' selected ' : '' ) . '>' . esc_html(
							     __( $value['title'], 'wpforo' )
						     ) . '</option>';
					}
					echo '
								</select>
							</td>
						</tr>
					';
				}


			}
		}
	}

	/**
	 * array get_counts(array or id(num))
	 *
	 * @param array defined arguments array for returning
	 *
	 * @return array array('topics' => 0, 'posts' => 0)
	 * @since 1.0.0
	 *
	 */
	function get_counts( $forumids ) {
		$result   = [ 'topics' => 0, 'posts' => 0 ];
		$forumids = array_filter( array_map( 'intval', (array) $forumids ) );
		if( $forumids ) {
			$sql              = "SELECT SUM(`topics`) as topics, SUM(`posts`) as posts FROM `" . WPF()->tables->forums . "` WHERE `forumid` IN(" . implode( ',', $forumids ) . ")";
			$row              = (array) WPF()->db->get_row( $sql, ARRAY_A );
			$result['topics'] = (int) wpfval( $row, 'topics' );
			$result['posts']  = (int) wpfval( $row, 'posts' );
		}

		return $result;
	}

	/**
	 * array get_layout(array)
	 *
	 * @param array defined arguments array for returning
	 *
	 * @return int layout id
	 * @since 1.0.0
	 *
	 */
	function get_layout( $args = null ) {
		if( is_null( $args ) ) $args = WPF()->current_object['forum'];
		if( ! $args ) return 1;
		if( $layout = (int) wpfval( $args, 'layout' ) ) return $layout;
		if( is_array( $args ) ) {
			$default = [
				'forumid' => null, // forum id
				'topicid' => null, // topic id
				'postid'  => null, // post id
			];
		} else {
			$default = [
				'forumid' => $args,    // forumid
				'topicid' => null,    // topic id
				'postid'  => null,    // post id
			];
		}
		$args = wpforo_parse_args( $args, $default );
		if( ! empty( $args ) ) {
			extract( $args, EXTR_OVERWRITE );

			if( $args['forumid'] ) {
				$layout = (int) wpforo_forum( $args['forumid'], 'layout' );

				return ( $layout ?: 1 );
			} elseif( $args['topicid'] ) {
				$sql     = "SELECT `forumid` FROM `" . WPF()->tables->topics . "` WHERE `topicid` = " . intval( $args['topicid'] );
				$forumid = WPF()->db->get_var( $sql );

				return $this->get_layout( [ 'forumid' => $forumid ] );
			} elseif( $args['postid'] ) {
				$sql     = "SELECT `forumid` FROM `" . WPF()->tables->posts . "` WHERE `postid` = " . intval( $args['postid'] );
				$forumid = WPF()->db->get_var( $sql );

				return $this->get_layout( [ 'forumid' => $forumid ] );
			}
		}

		return 1;
	}

	function get_forum_url( $forum ) {

		if( ! is_array( $forum ) ) {
			if( is_numeric( $forum ) ) {
				$forum = $this->get_forum( $forum );
			} else {
				$forum = [ 'slug' => $forum ];
			}
		}

		if( is_array( $forum ) && ! empty( $forum ) ) {
			return wpforo_home_url( utf8_uri_encode( $forum['slug'] ) );
		} else {
			return wpforo_home_url();
		}
	}

	function get_parents( $forumid, &$relative_ids ) {
		if( wpforo_is_db_mysql8() ) {
			$relative_ids = $this->__get_parents_mysql8( $forumid );

			return;
		}

		$sql = "SELECT `parentid`, `forumid` FROM `" . WPF()->tables->forums . "` WHERE `forumid` = %d";
		$sql = WPF()->db->prepare( $sql, $forumid );
		if( WPF()->ram_cache->exists( $sql ) ) {
			$forum = WPF()->ram_cache->get( $sql );
		} else {
			$forum = WPF()->db->get_row( $sql, ARRAY_A );
			WPF()->ram_cache->set( $sql, $forum );
		}
		if( $forum ) {
			$relative_ids[] = $forum['forumid'];
			if( $forum['parentid'] ) {
				$this->get_parents( $forum['parentid'], $relative_ids );
			} else {
				$relative_ids = array_reverse( $relative_ids );
			}
		}
	}

	/**
	 * @param int $forumid
	 *
	 * @return array
	 */
	private function __get_parents_mysql8( $forumid ) {
		$sql = "WITH RECURSIVE `forum_path` AS(
                SELECT `forumid`, `parentid`, 0 AS `depth` 
                FROM `" . WPF()->tables->forums . "`
                WHERE `forumid` = %d
                UNION
                SELECT f.`forumid`, f.`parentid`, `depth` + 1 
                FROM `" . WPF()->tables->forums . "` f
                INNER JOIN `forum_path` fp ON fp.`parentid` <> fp.`forumid` AND fp.`parentid` = f.`forumid`
            ) SELECT `forumid` FROM `forum_path` ORDER BY `depth` DESC";
		$sql = WPF()->db->prepare( $sql, intval( $forumid ) );
		if( WPF()->ram_cache->exists( $sql ) ) {
			$relative_ids = WPF()->ram_cache->get( $sql );
		} else {
			$relative_ids = (array) WPF()->db->get_col( $sql );
			WPF()->ram_cache->set( $sql, $relative_ids );
		}

		return $relative_ids;
	}

	function get_count( $args = [] ) {
		$sql = "SELECT SQL_NO_CACHE COUNT(*) FROM `" . WPF()->tables->forums . "`";
		if( ! empty( $args ) ) {
			$wheres = [];
			foreach( $args as $key => $value ) $wheres[] = "`$key` = " . intval( $value );
			if( $wheres ) $sql .= " WHERE " . implode( ' AND ', $wheres );
		}

		return WPF()->db->get_var( $sql );
	}

	function get_lastinfo( $ids = [] ) {
		$lastinfo = [];
		if( ! empty( $ids ) ) {
			$ids      = implode( ',', array_map( 'intval', $ids ) );
			$lastinfo = WPF()->db->get_row( "SELECT `userid` as last_userid, `topicid` as last_topicid, `postid` as last_postid, `created` as last_post_date FROM `" . WPF()->tables->posts . "` WHERE `status` = 0 AND `private` = 0 AND forumid IN(" . $ids . ") ORDER BY `created` DESC LIMIT 1", ARRAY_A );
		}

		return $lastinfo;
	}

	function forums() {
		$forums = $this->get_forums( [ 'parentid' => 0 ] );

		return $this->children( $forums );
	}

	function children( $forums, $parentId = 0, $level = 0 ) {
		if( empty( $forums ) || ! is_array( $forums ) ) return;
		$items = [];
		$level = $level + 1;
		foreach( $forums as $forum ) {
			if( ! isset( $forum['forumid'] ) || ! WPF()->perm->forum_can( 'vf', $forum['forumid'] ) ) continue;
			$forum['level'] = $level + 1;
			if( $forum['parentid'] == $parentId ) {
				$children = $this->children( $forums, $forum['forumid'], $level );
				if( $children ) {
					$forum['children'] = $children;
				}
				$items[] = $forum;
			}
		}

		return $items;
	}

	function dropdown( $forums = [] ) {
		if( empty( $forums ) ) {
			$forums = $this->forums();
		}
		foreach( $forums as $forum ) {
			if( isset( $forum['level'] ) ) $forum['level'] = $forum['level'] - 2;
			$prefix = ( $forum['level'] == 0 ) ? '' : str_repeat( '&mdash;', $forum['level'] );
			echo '<option value="' . esc_attr( $forum['forumid'] ) . '"> ' . $prefix . '&nbsp;' . esc_html( $forum['title'] ) . '</option>';
			if( ! empty( $forum['children'] ) ) {
				$this->dropdown( $forum['children'] );
			}
		}
	}

	function private_forum( $forumid, $groupids = [] ) {
		if( $forumid = intval( $forumid ) ) {
			if( ! $groupids ) $groupids = WPF()->current_user_groupids;
			if( ($groupids = array_map( 'intval', (array) $groupids)) && !WPF()->perm->forum_can( 'vf', $forumid, $groupids ) ) return true;
		}

		return false;
	}

	public function after_add_usergroup( $group ) {
		if( $group['groupid'] ) {
			if( $forums = $this->get_forums() ) {
				foreach( $forums as $forum ) {
					if( $permissions = (string) wpfval( $forum, 'permissions' ) ) {
						if( $permissions = maybe_unserialize( $permissions ) ) {
							$permissions[ $group['groupid'] ] = $group['access'];
							WPF()->db->update( WPF()->tables->forums, [ 'permissions' => serialize( $permissions ) ], [ 'forumid' => $forum['forumid'] ], [ '%s' ], [ '%d' ] );
						}
					}
				}
			}
		}
	}

	public function after_merge_forum( $forumid, $mergeid ) {
		$this->rebuild_last_infos( $mergeid );
		$this->rebuild_stats( $mergeid );
    }

	public function after_add_topic( $topic ) {
        if( !intval($topic['status']) && !intval($topic['private']) ){
	        $this->rebuild_last_infos( $topic['forumid'] );
	        $this->rebuild_stats( $topic['forumid'] );
        }
    }

	public function after_delete_topic( $topic ) {
        if( !intval($topic['status']) && !intval($topic['private']) ){
	        $this->rebuild_last_infos( $topic['forumid'] );
	        $this->rebuild_stats( $topic['forumid'] );
        }
    }

	public function after_topic_status_update( $topic ) {
		if( !intval($topic['private']) ){
			$this->rebuild_last_infos( $topic['forumid'] );
			$this->rebuild_stats( $topic['forumid'] );
		}
    }

	public function topic_private_update( $topicid ) {
        if( $topic = WPF()->topic->get_topic( $topicid ) ){
	        $this->rebuild_last_infos( $topic['forumid'] );
	        $this->rebuild_stats( $topic['forumid'] );
        }
    }

	public function after_merge_topic( $target, $current ) {
		$this->rebuild_last_infos( $target['forumid'] );
		$this->rebuild_stats( $target['forumid'] );
		$this->rebuild_last_infos( $current['forumid'] );
		$this->rebuild_stats( $current['forumid'] );
    }

	public function after_move_topic( $topic, $forumid ) {
		$this->rebuild_last_infos( $topic['forumid'] );
		$this->rebuild_stats( $topic['forumid'] );
		$this->rebuild_last_infos( $forumid );
		$this->rebuild_stats( $forumid );
    }

	public function after_add_post( $post, $topic, $forum ) {
        if( !intval( $post['status'] ) ){
	        $this->rebuild_last_infos( $forum['forumid'] );
	        $this->rebuild_stats( $forum['forumid'] );
        }
    }

	public function after_delete_post( $post ) {
		if( !intval( $post['status'] ) ){
			$this->rebuild_last_infos( $post['forumid'] );
			$this->rebuild_stats( $post['forumid'] );
		}
    }

	public function after_post_status_update( $post ) {
		if( !intval( $post['private'] ) ){
			$this->rebuild_last_infos( $post['forumid'] );
			$this->rebuild_stats( $post['forumid'] );
		}
    }
}
