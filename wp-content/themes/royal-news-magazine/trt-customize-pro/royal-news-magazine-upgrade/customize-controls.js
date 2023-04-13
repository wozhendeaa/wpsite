( function( api ) {

	// Extends our custom "royal-news-magazine-upgrade" section.
	api.sectionConstructor['royal-news-magazine-upgrade'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );
