<?php
/**
 * WP_List_Table Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

error_reporting( ~E_NOTICE );

class WPMOLY_Import_Table extends WPMOLY_List_Table {

	/**
	 * Constructor. Calls WP_List_Table and set up data.
	 * 
	 * @since    1.0
	 */
	function __construct() {

		if ( ! is_admin() )
			return false;

		global $status, $page;

		parent::__construct( array(
			'singular'  => 'movie',
			'plural'    => 'movies',
			'ajax'      => true,
			'screen'    => get_current_screen()
		) );

		$this->posts_per_page = 15;

		$this->metadata = WPMOLY_Settings::get_supported_movie_meta();

		$this->columns = WPMOLY_Import::get_imported_movies();

		$this->column_names = array(
			'poster'     => __( 'Poster', 'wpmovielibrary' ),
			'movietitle' => __( 'Title', 'wpmovielibrary' ),
			'director'   => __( 'Director', 'wpmovielibrary' ),
			'actions'    => __( 'Actions', 'wpmovielibrary' )
		);
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 * @since    1.0
	 */
	function _js_vars() {
		$current_screen = get_current_screen();

		$args = array(
			'class'  => get_class( $this ),
			'screen' => array(
				'id'   => $current_screen->id,
				'base' => $current_screen->base,
			)
		);

		printf( "<script type='text/javascript'>list_args = %s;</script>\n", json_encode( $args ) );
	}
	
	/**
	 * Message to be displayed when there are no items
	 * 
	 * @since    1.0
	 */
	function no_items() {
		_e( 'No movies found, dude.', 'wpmovielibrary' );
	}

	/**
	 * Display the pagination.
	 *
	 * @since    1.0
	 * 
	 * @param    string    $which The location of the paginate links: 'top' or 'bottom'.
	 */
	function pagination( $which ) {

		if ( empty( $this->_pagination_args ) )
			return;

		// Fix 'indirect modification of overloaded property has no effect' notice
		$pagination_args = $this->_pagination_args;
		extract( $pagination_args, EXTR_SKIP );

		$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s' onclick=' return false;'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s' onclick='wpmoly_import_view.navigate( this ); return false;'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' oninput='wpmoly_import_view.paginate(); return false;' />",
				esc_attr__( 'Current page' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s' onclick='wpmoly_import_view.navigate( this ); return false;'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
			'&rsaquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s' onclick='wpmoly_import_view.navigate( this ); return false;'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
			'&raquo;'
		);

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) )
			$pagination_links_class = ' hide-if-js';
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}
	
	/**
	 * Set default columns if any
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * @param    string    $column_name Name of the column.
	 * 
	 * @return   string|null    The item row corresponding to the column if available, null else
	 */
	function column_default( $item, $column_name ) {

		if ( in_array( $column_name, array_keys( $this->column_names ) ) )
			return $item[ $column_name ];
		else
			return null;
	}
 
	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since    1.0
	 *
	 * @return   array
	 */
	function get_sortable_columns() {

		$sortable_columns = array();

		foreach ( $this->column_names as $slug => $title )
			$sortable_columns[$slug] = array( $slug, false );

		return $sortable_columns;
	}
	
	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 * 
	 * @since    1.0
	 * 
	 * @return   array
	 */
	function get_columns(){

		$columns = array(
			'cb'        => '<input type="checkbox"  />',
		);

		foreach ( $this->column_names as $slug => $title )
			$columns[$slug] = $title;

		return $columns;
	}
	
	/**
	 * Change the items order.
	 * 
	 * If use a valid orderby if set in request, use movietitle as a
	 * fallback. Same for order.
	 * 
	 * NOTE Huh. Not quite sure what I added this... It does change the
	 * columns order, though, so lets keep it here for the moment.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    $a Associative array containing Movie data.
	 * @param    string    $b Associative array containing Movie data.
	 * 
	 * @return   array
	 */
	function usort_reorder( $a, $b ) {

		$orderby = 'movietitle';
		$order   = 'asc';

		// If no sort, default to title
		if ( ! empty( $_REQUEST['orderby'] ) && in_array( strtolower( $_REQUEST['orderby'] ), array( 'ID', 'poster', 'movietitle', 'director', 'tmdb_id' ) ) )
			$orderby = esc_attr( $_REQUEST['orderby'] );

		// If no order, default to asc
		if ( ! empty( $_REQUEST['order'] ) && in_array( strtolower( $_REQUEST['order'] ), array( 'asc', 'desc' ) ) )
			$order = esc_attr( $_REQUEST['order'] );

		// Determine sort order
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}
	
	/**
	 * Show a checkbox related to the Movie's ID.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_cb( $item ) {
		    return sprintf( '<input type="checkbox" id="post_%s" name="movie[]" value="%s"  />', $item['ID'], $item['ID'] );
	}
 
	/**
	 * Show the Movie's ID column.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_ID( $item ) {
		return sprintf('<span class="movie_ID">%1$s</span>', $item['ID'] );
	}
 
	/**
	 * Show the Movie's Title column.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_movietitle( $item ) {

		$inline_item  = '<input id="p_'.$item['ID'].'_meta_data_post_id" type="hidden" name="movies[p_'.$item['ID'].'][post_id]" value="'.$item['ID'].'" />';
		$inline_item .= '<input id="p_'.$item['ID'].'_meta_data_poster" type="hidden" name="movies[p_'.$item['ID'].'][poster]" value="" />';

		foreach ( array_keys( $this->metadata ) as $slug )
				$inline_item .= '<input id="p_'.$item['ID'].'_meta_data_'.$slug.'" type="hidden" name="movies[p_'.$item['ID'].']['.$slug.']" value="" />';

		$inline_item = '<div id="p_'.$item['ID'].'_meta_data">'.$inline_item.'</div>';

		return sprintf('<span class="movie_title">%1$s</span> %2$s', $item['movietitle'], $inline_item );
	}
 
	/**
	 * Show the Movie's Director column.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_director( $item ) {
		return sprintf('<span class="movie_director">%1$s</span>', $item['director'] );
	}
 
	/**
	 * Show the Movie's TMDb ID column.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_tmdb_id( $item ) {
		return sprintf('<span class="movie_tmdb_id">%1$s</span>', $item['tmdb_id'] );
	}

	function column_actions( $item ) {

		$actions = array(
			'edit'      => sprintf('<a class="edit_movie" id="edit_%1$s" href="%2$s" title="%3$s"><span class="wpmolicon icon-edit-page"></span></a>', $item['ID'], get_edit_post_link( $item['ID'] ), __( 'Edit', 'wpmovielibrary' ) ),
			'metadata'  => sprintf('<a class="search_movie" id="search_%1$s" href="%2$s" title="%3$s" onclick="wpmoly_import_meta.search(%4$s); return false;"><span class="wpmolicon icon-import"></span></a>', $item['ID'], wp_nonce_url( get_edit_post_link( $item['ID'] ) . "&amp;wpmoly_search_movie=1&amp;search_by=title&amp;search_query={$item['movietitle']}", 'search-movies' ), __( 'Fetch data from TMDb', 'wpmovielibrary' ), $item['ID'] ),
			'enqueue hide-if-no-js'   => sprintf('<a class="enqueue_movie" id="enqueue_%1$s" href="#" title="%2$s" onclick="wpmoly_movies_queue.add(%3$s); return false;"><span class="wpmolicon icon-plus"></span></a>', $item['ID'], __( 'Enqueue', 'wpmovielibrary' ), $item['ID'] ),
			'delete'    => sprintf('<a class="delete_movie" id="delete_%1$s" href="%2$s" title="%3$s" onclick="wpmoly_import_movies.delete(%4$s); return false;"><span class="wpmolicon icon-trash"></span></a>', $item['ID'], get_delete_post_link( $item['ID'] ), __( 'Delete', 'wpmovielibrary' ), $item['ID'] ),
		);

		return $this->row_actions( $actions, $always_visible = true );
	}

	/**
	 * Apply the search. If a keyword is set in request, filter the columns
	 * for matching Movie titles and return the filtered list; return the
	 * full list if no search is asked.
	 * 
	 * @since    1.0
	 * 
	 * @return   array    Associative array of Movies
	 */
	function filter_search() {

		if ( empty( $this->columns ) || ! isset( $_REQUEST['s'] ) || '' == $_REQUEST['s'] )
			return $this->columns;

		$results = array();
		$search = esc_attr( $_REQUEST['s'] );

		foreach ( $this->columns as $column )
			if ( false !== stristr( $column['movietitle'], $search ) )
				$results[] = $column;

		$this->columns = $results;
	}
	
	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 * 
	 * @since    1.0
	 * 
	 * @return   array
	 */
	function get_bulk_actions() {

		$actions = array(
			'search'  => __( 'Find Metadata', 'wpmovielibrary' ),
			'enqueue' => __( 'Enqueue Movie', 'wpmovielibrary' ),
			'delete'  => __( 'Delete Movie', 'wpmovielibrary' ),
		);

		return $actions;
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since    1.0
	 * 
	 * @param    string    $which The location of the bulk actions: 'top' or 'bottom'. This is designated as optional for backwards-compatibility.
	 */
	function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			/**
			 * Filter the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, $this->screen->id, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @since 3.5.0
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo "<select name='action$two'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = in_array( $name, array( 'edit', 'meta_data' ) ) ? ' class="hide-if-no-js"' : '';

			echo "\t<option value='$name'$class>$title</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', false, false, array( 'id' => "doaction$two", 'onclick' => "wpmoly_import_meta.do( 'action$two' ); return false;" ) );
		echo "\n";
	}

	/**
	 * Prepares the list of items for displaying.
	 * 
	 * Applies the search on items first thing, then handle the columns,
	 * sorting and pagination.
	 * 
	 * @uses WP_List_Table::set_pagination_args()
	 * @uses WPMOLY_List_Table::get_columns()
	 * @uses WPMOLY_List_Table::get_sortable_columns()
	 * @uses WPMOLY_List_Table::get_items_per_page()
	 * @uses WPMOLY_List_Table::filter_search()
	 *
	 * @since 1.0
	 */
	function prepare_items() {

		$this->filter_search();

		$columns  = $this->get_columns();

		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->columns, array( &$this, 'usort_reorder' ) );
		
		$per_page = $this->get_items_per_page( 'drafts_per_page', $this->posts_per_page );
		$current_page = $this->get_pagenum();
		$total_items = count( $this->columns );
		$total_pages = ceil( $total_items / $per_page );

		$columns = array_slice( $this->columns, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
				'orderby'     => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'title',
				'order'       => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc'
			)
		);

		$this->items = $columns;
	}

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 * 
	 * Copy of parent::ajax_response() with minor editing to return the
	 * pagination links along with rows.
	 *
	 * @since    1.0
	 */
	function ajax_response() {

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();

		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers( $with_id = true, $cb_counter = 1 );
		$headers = ob_get_clean();

		ob_start();
		$this->print_column_headers( $with_id = false, $cb_counter = 2 );
		$footers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();

		$response = array( 'rows' => $rows );
		$i18n = array();

		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;
		$response['column_footers'] = $footers;

		if ( isset( $total_items ) ) {
			$response['total_items'] = $total_items;
			$i18n['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );
		}

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$i18n['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		wpmoly_ajax_response( $response, $i18n );
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $with_id Whether to set the id attribute or not
	 */
	function print_column_headers( $with_id = true, $cb_counter = 1 ) {

		list( $columns, $hidden, $sortable ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) )
			$current_orderby = $_GET['orderby'];
		else
			$current_orderby = '';

		if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] )
			$current_order = 'desc';
		else
			$current_order = 'asc';

		if ( ! empty( $columns['cb'] ) ) {
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"  />';
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			$style = '';
			if ( in_array( $column_key, $hidden ) )
				$style = 'display:none;';

			$style = ' style="' . $style . '"';

			if ( 'cb' == $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby == $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<th scope='col' $id $class $style>$column_display_name</th>";
		}
	}

	/**
	 * Display the table
	 * 
	 * Copy of parent::display() adding an extra Nonce field.
	 *
	 * @since    1.0
	 */
	function display() {

		// Fix 'indirect modification of overloaded property has no effect' notice
		$args = $this->_args;
		extract( $args );

		$this->display_tablenav( 'top' );

		echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args['order'] . '" />';
		echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
 

?>
<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
	<thead>
	<tr>
		<?php $this->print_column_headers( $with_id = true, $cb_counter = 1 ); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<?php $this->print_column_headers( $with_id = false, $cb_counter = 2 ); ?>
	</tr>
	</tfoot>

	<tbody id="the-list"<?php if ( $singular ) echo " data-wp-lists='list:$singular'"; ?>>
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>
</table>
<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 1.0
	 */
	function display_tablenav( $which ) {

		if ( 'top' == $which )
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions(); ?>
			<span class="spinner"></span>
		</div>
<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}
 
}

?>