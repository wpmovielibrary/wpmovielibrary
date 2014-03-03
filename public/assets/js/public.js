jQuery(document).ready(function($) {

	$("#genres-list, #actors-list, #collections-list").change(function() {
		if ( this.options[ this.selectedIndex ].value.length > 0 )
			location.href = this.options[ this.selectedIndex ].value;
	});
});