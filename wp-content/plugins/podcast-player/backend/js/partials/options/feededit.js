class FeedEditor {

	/**
	 * Manage Feed editor options.
	 * 
	 * @since 3.3
	 * 
	 * @param {string} id Podcast player ID. 
	 */
	constructor() {

		// Define variables.
		this.data = window.ppjsAdminOpt || {};
		this.url = jQuery('.pp-toolkit-url').first();
		this.index = jQuery('.select-pp-feed-index').first();
		this.refresh = jQuery('.pp-feed-refresh');
		this.reset = jQuery('.pp-feed-reset');
		this.feedback = jQuery('.pp-toolkit-feedback').first();

		// Run methods.
		this.events();
	}

	// Event handling.
	events() {
		const _this = this;
		this.refresh.on('click', function() { this.ajaxFeedEditor('refresh') }.bind(this) );
		this.reset.on('click', function() { this.ajaxFeedEditor('reset') }.bind(this) );
		jQuery('.pp-feed-del').on('click', function(){
			const val = _this.index.val() || _this.url.val();
			if (!val) {
				_this.response(_this.data.messages.nourl, 'pp-error');
			} else {
				jQuery(this).next('.pp-toolkit-del-confirm').slideDown("fast");
				_this.response();
			}
		});
		jQuery('.pp-feed-cancel').on('click', function(){
			jQuery(this).parents('.pp-toolkit-del-confirm').hide();
		});
	}

	/**
	 * Feed editor Ajax.
	 * 
	 * @since 2.0
	 * 
	 * @param string type
	 */
	ajaxFeedEditor(type) {
		const ajaxConfig = this.getAjaxConfig(type);
		this.response(this.data.messages.running, 'pp-running');
		if (!ajaxConfig) {
			this.response(this.data.messages.nourl, 'pp-error');
			return;
		}

		// Let's get next set of episodes.
		jQuery.ajax( {
			url: this.data.ajaxurl,
			data: ajaxConfig,
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.response(details.error, 'pp-error');
					} else if ('undefined' !== typeof details.message) {
						this.response(details.message, 'pp-success');
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.response(errorThrown, 'pp-error');
			}
		} );
	}

	/**
	 * Get args for Ajax request.
	 * 
	 * @since 3.4.0
	 * 
	 * @param string type
	 */
	getAjaxConfig(type) {

		// Get feed key from dropdown list.
		let url = this.index.val();
		if ( url ) {
			return {
				action  : 'pp_feed_editor',
				security: this.data.security,
				atype   : type,
				feedUrl : url,
				rFrom    : 'indexKey',
			};
		}

		// Get feed url from input field.
		url = this.url.val();
		if ( url ) {
			return {
				action  : 'pp_feed_editor',
				security: this.data.security,
				atype   : type,
				feedUrl : url,
			}
		}

		return false;
	}

	/**
	 * Display request feedback.
	 * 
	 * @since 3.3.0
	 * 
	 * @param string message
	 * @param string type
	 */
	response(message = '', type = false) {
		this.feedback.removeClass('pp-error pp-success pp-running');
		if (false !== type) {
			this.feedback.addClass(type);
			this.feedback.find('.pp-feedback').text(message);
		}
	}
}

export default FeedEditor;
