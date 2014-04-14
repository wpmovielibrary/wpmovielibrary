
$ = jQuery;

wpml = {
	__ajax: function() {},
	get: function() {},
	post: function() {},
	switch_data: function() {},
	http_query_var: function() {},

	status: {}
};

	/**
	 * WPML filter for AJAX Request
	 * 
	 * @param    string      Request type: GET, POST
	 * @param    object      Data object to pass
	 * @param    function    Function to run on success
	 * @param    function    Function to run on complete
	 */
	wpml.__ajax = function( type, data, success, complete ) {

		var type = type || 'GET';
		var data = data || {};
		var success = success || function() {};
		var complete = complete || function() {};

		$.ajax({
			type: type,
			url: ajaxurl,
			data: data,
			success: success,
			complete: complete
		});
	},

	/**
	 * WPML filter for AJAX GET Request
	 * 
	 * @param    object      Data object to pass
	 * @param    function    Function to run on success
	 * @param    function    Function to run on complete
	 */
	wpml.get = function( data, success, complete ) {
		wpml.__ajax( 'GET', data, success, complete );
	},

	/**
	 * WPML filter for AJAX POST Request
	 * 
	 * @param    object      Data object to pass
	 * @param    function    Function to run on success
	 * @param    function    Function to run on complete
	 */
	wpml.post = function( data, success, complete ) {
		wpml.__ajax( 'POST', data, success, complete );
	},

	/**
	 * Determine which data package the submitted field name belongs to.
	 * 
	 * @param    string    Field name
	 * 
	 * @return   string    Data package name
	 */
	wpml.switch_data = function( f_name ) {

		switch ( f_name ) {
			case "poster":
			case "title":
			case "original_title":
			case "overview":
			case "production_companies":
			case "production_countries":
			case "spoken_languages":
			case "runtime":
			case "genres":
			case "release_date":
				var _data = 'meta';
				break;
			case "director":
			case "producer":
			case "photography":
			case "composer":
			case "author":
			case "writer":
			case "cast":
				var _data = 'crew';
				break;
			default:
				var _data = 'data';
				break;
		}

		return _data;
	};

	/**
	 * Status indicator
	 */
	wpml.status = wpml_status = {

		container: '#tmdb_status',

		set: function() {},
		clear: function() {}
	};

		/**
		 * Update status
		 * 
		 * @param    string    Status Message
		 * @param    string    Status type: error, update
		 */
		wpml.status.set = function( message, style ) {
			$(wpml_status.container).text( message ).removeClass().addClass( style ).show();
		};

		/**
		 * Clear status
		 */
		wpml.status.clear = function() {
			$(wpml_status.container).empty().removeClass().hide();
		};

	/**
	 * Parse URL Query part to extract specific variables
	 * 
	 * @param    string    URL Query part to parse
	 * @param    string    Wanted variable name
	 * 
	 * @return   string|boolean    Variable value if available, false else
	 */
	wpml.http_query_var = function( query, variable ) {

		var vars = query.split("&");
		for ( var i = 0; i <vars.length; i++ ) {
			var pair = vars[ i ].split("=");
			if ( pair[0] == variable )
				return pair[1];
		}
		return false;
	};
