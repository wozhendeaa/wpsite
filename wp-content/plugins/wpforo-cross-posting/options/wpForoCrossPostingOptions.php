<?php

class wpForoCrossPostingOptions implements wpForoCrossPostingConst {

	public $postTypes;
	public $commentsReplyCrossing;
	public $postCrossingEdit;
	public $postCrossingDelete;
	public $commentCrossingAdd;
	public $commentCrossingEdit;
	public $commentCrossingDelete;
	public $forumPostCrossingAdd;
	public $forumPostCrossingEdite;
	public $forumPostCrossingDelete;
	public $showPostTopicRel;
	public $showCommentReplyRel;
	public $showTopicPostRel;
	public $showReplyCommentRel;
	public $crossPostingContent;
	public $crossPostFeaturedImage;
	public $crossPostFImageSize;
	public $crossPostTopicCanonicalUrl;

	public function __construct() {
		$this->initOptions();
		add_action('wpforo_after_change_board', [&$this,'initOptions']);
		$this->saveOptions();
	}

	public function saveOptions() {
		if ( isset( $_POST[ self::OPTION_SUBMIT ] ) ) {
			if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
				die( _e( 'Hacker?', 'wpforo_cross' ) );
			}
			if ( function_exists( 'check_admin_referer' ) ) {
				check_admin_referer( self::OPTION_NONCE );
			}
			$this->postTypes                  = isset( $_POST[ self::OPTION_POST_TYPES ] ) ? $_POST[ self::OPTION_POST_TYPES ] : [];
			$this->postCrossingEdit           = isset( $_POST[ self::OPTION_POST_CROSSING_EDIT ] ) ? $_POST[ self::OPTION_POST_CROSSING_EDIT ] : 0;
			$this->postCrossingDelete         = isset( $_POST[ self::OPTION_POST_CROSSING_DELETE ] ) ? $_POST[ self::OPTION_POST_CROSSING_DELETE ] : 0;
			$this->commentCrossingAdd         = isset( $_POST[ self::OPTION_COMMENT_ADD ] ) ? $_POST[ self::OPTION_COMMENT_ADD ] : 0;
			$this->commentCrossingEdit        = isset( $_POST[ self::OPTION_COMMENT_EDIT ] ) ? $_POST[ self::OPTION_COMMENT_EDIT ] : 0;
			$this->commentCrossingDelete      = isset( $_POST[ self::OPTION_COMMENT_DELETE ] ) ? $_POST[ self::OPTION_COMMENT_DELETE ] : 0;
			$this->forumPostCrossingAdd       = isset( $_POST[ self::OPTION_FORUM_POST_ADD ] ) ? $_POST[ self::OPTION_FORUM_POST_ADD ] : 0;
			$this->forumPostCrossingEdite     = isset( $_POST[ self::OPTION_FORUM_POST_EDIT ] ) ? $_POST[ self::OPTION_FORUM_POST_EDIT ] : 0;
			$this->forumPostCrossingDelete    = isset( $_POST[ self::OPTION_FORUM_POST_DELETE ] ) ? $_POST[ self::OPTION_FORUM_POST_DELETE ] : 0;
			$this->showPostTopicRel           = isset( $_POST[ self::OPTION_SHOW_POST_TOPIC_REL ] ) ? $_POST[ self::OPTION_SHOW_POST_TOPIC_REL ] : 0;
			$this->showCommentReplyRel        = isset( $_POST[ self::OPTION_SHOW_COMMENT_REPLY_REL ] ) ? $_POST[ self::OPTION_SHOW_COMMENT_REPLY_REL ] : 0;
			$this->showTopicPostRel           = isset( $_POST[ self::OPTION_SHOW_TOPIC_POST_REL ] ) ? $_POST[ self::OPTION_SHOW_TOPIC_POST_REL ] : 0;
			$this->showReplyCommentRel        = isset( $_POST[ self::OPTION_SHOW_REPLY_COMMENT_REL ] ) ? $_POST[ self::OPTION_SHOW_REPLY_COMMENT_REL ] : 0;
			$this->crossPostingContent        = isset( $_POST[ self::OPTION_CROSPOSTING_CONTENT ] ) ? $_POST[ self::OPTION_CROSPOSTING_CONTENT ] : 1;
			$this->crossPostFeaturedImage     = isset( $_POST[ self::OPTION_CROSPOSTING_FEATURED_IMAGE ] ) ? 1 : 0;
			$this->crossPostFImageSize        = isset( $_POST[ self::OPTION_CROSPOSTING_FIMAGE_SIZE ] ) ? $_POST[ self::OPTION_CROSPOSTING_FIMAGE_SIZE ] : self::OPTION_CROSPOSTING_FIMAGE_MEDIUM;
			$this->crossPostTopicCanonicalUrl = isset( $_POST[ self::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL ] ) ? $_POST[ self::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL ] : 'blog_post';

			wpforo_update_option( self::OPTIONS_SLUG, [
				self::OPTION_POST_TYPES                      => $this->postTypes,
				self::OPTION_POST_CROSSING_EDIT              => $this->postCrossingEdit,
				self::OPTION_POST_CROSSING_DELETE            => $this->postCrossingDelete,
				self::OPTION_COMMENT_ADD                     => $this->commentCrossingAdd,
				self::OPTION_COMMENT_EDIT                    => $this->commentCrossingEdit,
				self::OPTION_COMMENT_DELETE                  => $this->commentCrossingDelete,
				self::OPTION_FORUM_POST_ADD                  => $this->forumPostCrossingAdd,
				self::OPTION_FORUM_POST_EDIT                 => $this->forumPostCrossingEdite,
				self::OPTION_FORUM_POST_DELETE               => $this->forumPostCrossingDelete,
				self::OPTION_SHOW_POST_TOPIC_REL             => $this->showPostTopicRel,
				self::OPTION_SHOW_COMMENT_REPLY_REL          => $this->showCommentReplyRel,
				self::OPTION_SHOW_TOPIC_POST_REL             => $this->showTopicPostRel,
				self::OPTION_SHOW_REPLY_COMMENT_REL          => $this->showReplyCommentRel,
				self::OPTION_CROSPOSTING_CONTENT             => $this->crossPostingContent,
				self::OPTION_CROSPOSTING_FEATURED_IMAGE      => $this->crossPostFeaturedImage,
				self::OPTION_CROSPOSTING_FIMAGE_SIZE         => $this->crossPostFImageSize,
				self::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL => $this->crossPostTopicCanonicalUrl,
			] );
			wp_redirect( wpforo_get_request_uri() );
			exit();
		}
	}

	public function getDefaultOptions() {
		return [
			self::OPTION_POST_TYPES                      => [ 'post' ],
			self::OPTION_POST_CROSSING_EDIT              => '1',
			self::OPTION_POST_CROSSING_DELETE            => '0',
			self::OPTION_COMMENT_ADD                     => '1',
			self::OPTION_COMMENT_EDIT                    => '1',
			self::OPTION_COMMENT_DELETE                  => '1',
			self::OPTION_FORUM_POST_ADD                  => '1',
			self::OPTION_FORUM_POST_EDIT                 => '1',
			self::OPTION_FORUM_POST_DELETE               => '1',
			self::OPTION_SHOW_POST_TOPIC_REL             => '1',
			self::OPTION_SHOW_COMMENT_REPLY_REL          => '1',
			self::OPTION_SHOW_TOPIC_POST_REL             => '1',
			self::OPTION_SHOW_REPLY_COMMENT_REL          => '1',
			self::OPTION_CROSPOSTING_CONTENT             => '1',
			self::OPTION_CROSPOSTING_FEATURED_IMAGE      => 1,
			self::OPTION_CROSPOSTING_FIMAGE_SIZE         => self::OPTION_CROSPOSTING_FIMAGE_MEDIUM,
			self::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL => 'blog_post',
		];
	}

	public function initOptions() {
		$options                          = wpforo_get_option( self::OPTIONS_SLUG, $this->getDefaultOptions());
		$this->postTypes                  = $options[ self::OPTION_POST_TYPES ];
		$this->postCrossingEdit           = $options[ self::OPTION_POST_CROSSING_EDIT ];
		$this->postCrossingDelete         = $options[ self::OPTION_POST_CROSSING_DELETE ];
		$this->commentCrossingAdd         = $options[ self::OPTION_COMMENT_ADD ];
		$this->commentCrossingEdit        = $options[ self::OPTION_COMMENT_EDIT ];
		$this->commentCrossingDelete      = $options[ self::OPTION_COMMENT_DELETE ];
		$this->forumPostCrossingAdd       = $options[ self::OPTION_FORUM_POST_ADD ];
		$this->forumPostCrossingEdite     = $options[ self::OPTION_FORUM_POST_EDIT ];
		$this->forumPostCrossingDelete    = $options[ self::OPTION_FORUM_POST_DELETE ];
		$this->showPostTopicRel           = $options[ self::OPTION_SHOW_POST_TOPIC_REL ];
		$this->showCommentReplyRel        = $options[ self::OPTION_SHOW_COMMENT_REPLY_REL ];
		$this->showTopicPostRel           = $options[ self::OPTION_SHOW_TOPIC_POST_REL ];
		$this->showReplyCommentRel        = $options[ self::OPTION_SHOW_REPLY_COMMENT_REL ];
		$this->crossPostingContent        = isset( $options[ self::OPTION_CROSPOSTING_CONTENT ] ) ? $options[ self::OPTION_CROSPOSTING_CONTENT ] : self::OPTION_CROSPOSTING_CONTENT_FULL;
		$this->crossPostFeaturedImage     = isset( $options[ self::OPTION_CROSPOSTING_FEATURED_IMAGE ] ) ? $options[ self::OPTION_CROSPOSTING_FEATURED_IMAGE ] : 1;
		$this->crossPostFImageSize        = isset( $options[ self::OPTION_CROSPOSTING_FIMAGE_SIZE ] ) ? $options[ self::OPTION_CROSPOSTING_FIMAGE_SIZE ] : self::OPTION_CROSPOSTING_FIMAGE_MEDIUM;
		$this->crossPostTopicCanonicalUrl = isset( $options[ self::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL ] ) ? $options[ self::OPTION_CROSPOSTING_TOPIC_CANONICAL_URL ] : 'blog_post';
		$this->addPhrases();
	}

	private function addPhrases() {
		$phrases = [
			'This article is also published as a forum topic here'           => __( 'This article is also published as a forum topic here',
			                                                                        'wpforo_cross' ),
			'More discussion'                                                => __( 'More discussion', 'wpforo_cross' ),
			'This comment is also posted as a reply in related forum topic.' => __( 'This comment is also posted as a reply in related forum topic.',
			                                                                        'wpforo_cross' ),
			'forum reply'                                                    => __( 'forum reply', 'wpforo_cross' ),
			'This reply is also posted as a comment under related article.'  => __( 'This reply is also posted as a comment under related article.',
			                                                                        'wpforo_cross' ),
			'Read more'                                                      => __( 'Read more', 'wpforo_cross' ),
			'comment'                                                        => __( 'comment', 'wpforo_cross' ),
		];
		if ( ! get_option( self::PLUGIN_VERSION ) ) {
			foreach ( $phrases as $key => $value ) {
				WPF()->phrase->add( [
					                    'key'     => $key,
					                    'value'   => $value,
					                    'package' => 'wpforo_cross',
				                    ] );
			}
			WPF()->phrase->clear_cache();
			WPF()->notice->clear();
		}
	}

}
