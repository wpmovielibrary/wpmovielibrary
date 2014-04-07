<?php
/**
 * WP_List_Table Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

error_reporting( ~E_ALL );

class WPML_Import_Table extends WP_List_Table {

	/**
	 * Constructor. Calls WP_List_Table and set up data.
	 * 
	 * @since    1.0.0
	 * 
	 * @access   protected
	 * 
	 * @param    array    $columns  Associative array containing all the Movies
	 *                              imported from list
	 * @param    array    $metadata Associative array containing Movies metadata
	 */
	function __construct() {

		global $status, $page;

		parent::__construct( array(
			'singular'  => 'movie',
			'plural'    => 'movies',
			'ajax'      => true,
			'screen'    => get_current_screen()
		) );

		$this->metadata = WPML_Settings::wpml_get_supported_movie_meta( $type = null, $merge = false );

		$this->columns = WPML_Import::wpml_get_imported_movies();;

		$this->column_names = array(
			'ID'         => __( 'ID', 'wpml' ),
			'poster'     => __( 'Poster', 'wpml' ),
			'movietitle' => __( 'Title', 'wpml' ),
			'director'   => __( 'Director', 'wpml' ),
			'tmdb_id'    => __( 'TMDb ID', 'wpml' ),
			'actions'    => __( 'Actions', 'wpml' )
		);
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 * @access private
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
	 * @since    1.0.0
	 * 
	 * @access   public
	 */
	function no_items() {
		_e( 'No movies found, dude.', 'wpml' );
	}
	
	/**
	 * Set default columns if any
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * @param    string    $column_name Name of the column.
	 * 
	 * @return   string|null    The item row corresponding to the column if
	 *                          available, null else
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
	 * @since    1.0.0
	 * 
	 * @access   protected
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
	 * @since    1.0.0
	 * 
	 * @access   protected
	 * 
	 * @return   array
	 */
	function get_columns(){

		$columns = array(
			'cb'        => '<input type="checkbox" />',
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
	 * @since    1.0.0
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
	 * @since    1.0.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_cb( $item ) {
		    return sprintf( '<input type="checkbox" id="post_%s" name="movie[]" value="%s" />', $item['ID'], $item['ID'] );
	}
 
	/**
	 * Show the Movie's ID column.
	 * 
	 * @since    1.0.0
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
	 * @since    1.0.0
	 * 
	 * @param    string    $item Associative array containing the item data.
	 * 
	 * @return   string    HTML markup
	 */
	function column_movietitle( $item ) {

		$inline_item  = '<input id="p_'.$item['ID'].'_tmdb_data_post_id" type="hidden" name="tmdb[p_'.$item['ID'].'][post_id]" value="'.$item['ID'].'" />';
		$inline_item .= '<input id="p_'.$item['ID'].'_tmdb_data_tmdb_id" type="hidden" name="tmdb[p_'.$item['ID'].'][tmdb_id]" value="0" />';
		$inline_item .= '<input id="p_'.$item['ID'].'_tmdb_data_poster" type="hidden" name="tmdb[p_'.$item['ID'].'][poster]" value="" />';

		foreach ( $this->metadata as $id => $box )
			foreach ( $box['data'] as $slug => $meta )
				$inline_item .= '<input id="p_'.$item['ID'].'_tmdb_data_'.$slug.'" type="hidden" name="tmdb[p_'.$item['ID'].']['.$id.']['.$slug.']" value="" />';

		$inline_item = '<div id="p_'.$item['ID'].'_tmdb_data">'.$inline_item.'</div>';

		return sprintf('<span class="movie_title">%1$s</span> %2$s', $item['movietitle'], $inline_item );
	}
 
	/**
	 * Show the Movie's Director column.
	 * 
	 * @since    1.0.0
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
	 * @since    1.0.0
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
			'edit'      => sprintf('<a class="edit_movie" id="edit_%1$s"  href="%2$s" title="%3$s"><span class="dashicons dashicons-welcome-write-blog"></span></a>', $item['ID'], get_edit_post_link( $item['ID'] ), __( 'Edit', 'wpml' ) ),
			'tmdb_data' => sprintf('<a class="fetch_movie" id="fetch_%1$s"  href="%2$s" title="%3$s"><span class="dashicons dashicons-download"></span></a>', $item['ID'], get_edit_post_link( $item['ID'] ) . "&wpml_auto_fetch=1", __( 'Fetch data from TMDb', 'wpml' ) ),
			'import'    => sprintf('<a class="import_movie" id="import_%1$s" href="#" title="%2$s"><span class="dashicons dashicons-welcome-add-page"></span></a>', $item['ID'], __( 'Import Movie', 'wpml' ) ),
			'enqueue'   => sprintf('<a class="enqueue_movie" id="enqueue_%1$s" href="#" title="%2$s"><span class="dashicons dashicons-plus"></span></a>', $item['ID'], __( 'Enqueue', 'wpml' ) ),
			'delete'    => sprintf('<a class="delete_movie" id="delete_%1$s" href="#" title="%2$s"><span class="dashicons dashicons-post-trash"></span></a>', $item['ID'], __( 'Delete', 'wpml' ) ),
		);

		return $this->row_actions( $actions, $always_visible = true );
	}

	/**
	 * Apply the search. If a keyword is set in request, filter the columns
	 * for matching Movie titles and return the filtered list; return the
	 * full list if no search is asked.
	 * 
	 * @since    1.0.0
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
	 * @since    1.0.0
	 * 
	 * @access   protected
	 * 
	 * @return   array
	 */
	function get_bulk_actions() {

		$actions = array(
			'delete'    => __( 'Delete', 'wpml' ),
			'tmdb_data' => __( 'Fetch data from TMDb', 'wpml' ),
		);

		return $actions;
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since    1.0.0
	 * 
	 * @access   public
	 */
	function bulk_actions() {
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
			$class = in_array( $name, array( 'edit', 'tmdb_data' ) ) ? ' class="hide-if-no-js"' : '';

			echo "\t<option value='$name'$class>$title</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', false, false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}

	/**
	 * Prepares the list of items for displaying.
	 * 
	 * Applies the search on items first thing, then handle the columns,
	 * sorting and pagination.
	 * 
	 * @uses WP_List_Table::set_pagination_args()
	 * @uses WPML_List_Table::get_columns()
	 * @uses WPML_List_Table::get_sortable_columns()
	 * @uses WPML_List_Table::get_items_per_page()
	 * @uses WPML_List_Table::filter_search()
	 *
	 * @since 1.0.0
	 * 
	 * @access public
	 */
	function prepare_items() {

		$this->filter_search();

		$columns  = $this->get_columns();

		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->columns, array( &$this, 'usort_reorder' ) );
		
		$per_page = $this->get_items_per_page( 'drafts_per_page', 30 );
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
	 * @since    1.0.0
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
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();

		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		wp_die( json_encode( $response ) );
	}

	/**
	 * Display the table
	 * 
	 * Copy of parent::display() adding an extra Nonce field.
	 *
	 * @since    1.0.0
	 */
	function display() {

		extract( $this->_args );

		$this->display_tablenav( 'top' );

		wp_nonce_field( 'wpml-fetch-imported-movies-nonce', 'wpml_fetch_imported_movies_nonce' );

		echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args['order'] . '" />';
		echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
 

?>
<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
	<thead>
	<tr>
		<?php $this->print_column_headers(); ?>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<?php $this->print_column_headers( false ); ?>
	</tr>
	</tfoot>

	<tbody id="the-list"<?php if ( $singular ) echo " data-wp-lists='list:$singular'"; ?>>
		<?php $this->display_rows_or_placeholder(); ?>
		<tr>
			<td colspan="<?php echo count( $this->column_names ); ?>">
				<div id="progressbar_bg"><div id="progressbar"><div class="progress-label">0</div></div><a href="#" id="hide_progressbar"><?php _e( 'Hide', 'wpml' ) ?></a></div>
			</td>
		</tr>
	</tbody>
</table>
<?php
		$this->display_tablenav( 'bottom' );
	}
 
}

?>