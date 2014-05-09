
wpml = wpml || {};

wpml.settings = wpml_settings = {

	init: function() {},

	panels: {},
	sortable: {},

	api_ckeck: function() {},
	toggle_radio: function() {}
}

	wpml.settings.init = function() {

		wpml.settings.panels.init();
		wpml.settings.sortable.init();
		
		$('input#APIKey_check').on( 'click', function( e ) {
			e.preventDefault();
			wpml_settings.api_ckeck();
		});

		$('.label_onoff .label_on').on( 'click', function() {
			wpml_settings.toggle_radio( this, true );
		});

		$('.label_onoff .label_off').on( 'click', function() {
			wpml_settings.toggle_radio( this, false );
		});
	};

	wpml.settings.panels = wpml_panels = {

		links: '#wpml-tabs .wpml-tabs-nav a',
		link: '#wpml-tabs .wpml-tabs-nav li',
		link_active: '#wpml-tabs .wpml-tabs-nav li.active',
		panels: '#wpml-tabs .wpml-tabs-panels > .form-table',
		active: 0,

		init: function() {},
		switch_panel: function() {}
	};

		wpml.settings.panels.init = function() {

			if ( $(wpml_panels.link_active).length )
				wpml_panels.active = $(wpml_panels.link).index( $(wpml_panels.link_active) );

			var panel = $(wpml_panels.panels)[ wpml_panels.active ];

			$(wpml_panels.panels).hide();
			$(panel).addClass('active');

			$(wpml_panels.links).on( 'click', function( e ) {
				e.preventDefault();
				wpml_panels.switch_panel( this );
			});

		};

		wpml.settings.panels.switch_panel = function( link ) {

			__link = link;
			var index = $(wpml_panels.links).index( link );

			if ( wpml_panels.panels.length >= index )
				var panel = $(wpml_panels.panels)[ index ];

			var tab = $(link).attr('data-section');
			var url = link.href.replace(link.hash, '');
			if ( link.hash.length || '#' == url.substring( url.length, url.length - 1 ) )
				url = url.substring( 0, ( url.length - 1 ) );

			    var section = link.href.indexOf('&wpml_section');
			if ( section > 0 )
				url = url.substring( 0, section );

			$('.wpml-tabs-panels .form-table, .wpml-tabs-nav').removeClass('active');
			$(panel).addClass('active');
			$(link).parent('li').addClass('active');

			window.history.replaceState({}, '' + url + '&' + tab, '' + url + '&' + tab);
		};

	wpml.settings.sortable = wpml_sortable = {

		draggable: '#draggable',
		droppable: '#droppable',
		default_selected: 'default_movie_meta_selected',
		default_droppable: 'default_movie_meta_droppable',

		init: function() {},
		update_item_style: function() {},
		update_item: function() {}
	};

		wpml.settings.sortable.init = function() {

			$(wpml_sortable.draggable+', '+wpml_sortable.droppable).sortable({
				connectWith: 'ul',
				placeholder: 'highlight',
				update: function( event, ui ) {
					wpml_sortable.update_item_style( ui );
				},
				stop: function( event, ui ) {
					wpml_sortable.update_item();
				}
			});

			$(wpml_sortable.draggable+', '+wpml_sortable.droppable).disableSelection();
		};


		wpml.settings.sortable.update_item_style = function( ui ) {

			if ( undefined == ui.sender || ! ui.sender.length )
				return false;

			var _id = ui.sender[0].id;
			var item = ui.item[0];

			if ( 'draggable' == ui.sender[0].id )
				$(item).removeClass(wpml_sortable.default_selected).addClass(wpml_sortable.default_droppable);
			else if ( 'droppable' == ui.sender[0].id )
				$(item).removeClass(wpml_sortable.default_droppable).addClass(wpml_sortable.default_selected);
		};

		wpml.settings.sortable.update_item = function() {

			values = [];
			var items = $('.'+wpml_sortable.default_selected);
			$.each(items, function() {
				var value = $(this).attr('data-movie-meta');
				$('#wpml_settings-wpml-default_movie_meta option[value="'+value+'"]').prop('selected', true);
				values.push( value );
			});
			$('#default_movie_meta_sorted').val( values.join(',') );
		};

	wpml.settings.api_ckeck = function() {

		var $input = $('input#APIKey_check');

		var key = $('input#wpml_settings-tmdb-apikey').val();
		$('#api_status').remove();

		if ( '' == key ) {
			$input.after('<span id="api_status" class="invalid">'+wpml_ajax.lang.empty_key+'</span>');
			return false;
		}
		else if ( 32 != key.length ) {
			$input.after('<span id="api_status" class="invalid">'+wpml_ajax.lang.length_key+'</span>');
			return false;
		}
		
		wpml._get({
			data: {
				action: 'wpml_check_api_key',
				wpml_check: wpml_ajax.utils.wpml_check,
				key: key
			},
			error: function( response ) {
				$input.after( '<span id="api_status" class="invalid">' + response.responseJSON.errors.invalid[ 0 ] + '</span>' );
			},
			success: function( response ) {
				$input.after( '<span id="api_status" class="valid">' + response.data.message + '</span>' );
			}
		});
	};

	wpml.settings.toggle_radio = function( toggle, status ) {

		var $label_off = $(toggle).parent('.label_onoff').find('.label_off'),
		    $label_on = $(toggle).parent('.label_onoff').find('.label_on'),
		    $disable = $(toggle).parent('.label_onoff').next('.label_onoff_radio').find('.enable'),
		    $enable = $(toggle).parent('.label_onoff').next('.label_onoff_radio').find('.disable'),
		    disable = status || false;

		if ( ! disable ) {
			$enable.prop('checked', true);
			$label_on.removeClass('active');
			$label_off.addClass('active');
		}
		else {
			$disable.prop('checked', true);
			$label_off.removeClass('active');
			$label_on.addClass('active');
		}
	};

	wpml.settings.init();