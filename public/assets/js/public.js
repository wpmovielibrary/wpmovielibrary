jQuery(document).ready(function($) {

	$(".wpml-list").change(function() {
		if ( this.options[ this.selectedIndex ].value.length > 0 )
			location.href = this.options[ this.selectedIndex ].value;
	});
});