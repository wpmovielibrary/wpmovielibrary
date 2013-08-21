<?php
/**
 * WP_List_Table Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <contact@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2013 CaerCam.org
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPML_List_Table extends WP_List_Table {

	function __construct( $columns ) {

		global $status, $page;

		parent::__construct( array(
			'singular'  => __( 'movie', 'mylisttable' ),
			'plural'    => __( 'movies', 'mylisttable' ),
			'ajax'      => false
		) );

		$this->columns = $columns;

		$this->column_names = array(
			'ID'         => __( 'ID', 'wpml' ),
			'poster'     => __( 'Poster', 'wpml' ),
			'movietitle' => __( 'Title', 'wpml' ),
			'director'   => __( 'Director', 'wpml' ),
			'tmdb_id'    => __( 'TMDb ID', 'wpml' )
		);
	}
	
	function no_items() {
		_e( 'No movies found, dude.' );
	}
	
	function column_default( $item, $column_name ) {

		if ( in_array( $column_name, array_keys( $this->column_names ) ) )
			return $item[ $column_name ];
		else
			return print_r( $item, true );
	}
 
	function get_sortable_columns() {

		$sortable_columns = array();

		foreach ( $this->column_names as $slug => $title )
			$sortable_columns[$slug] = array( $slug, false );

		return $sortable_columns;
	}
	
	function get_columns(){

		$columns = array(
			'cb'        => '<input type="checkbox" />',
		);

		foreach ( $this->column_names as $slug => $title )
			$columns[$slug] = $title;

		return $columns;
	    }
	
	function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'movietitle';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}
 
	function column_movietitle( $item ) {

		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">%s</a>', $_REQUEST['page'], 'edit', $item['ID'], __( 'Edit', 'wpml' ) ),
			'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">%s</a>', $_REQUEST['page'], 'delete', $item['ID'], __( 'Delete', 'wpml' ) ),
		);

		$inline_item = '';
		

		return sprintf('<span class="movie_title">%1$s</span> %2$s', $item['movietitle'], $this->row_actions($actions) );
	}
 
	function column_director( $item ) {
		return sprintf('<span class="movie_director">%1$s</span>', $item['director'] );
	}
 
	function column_tmdb_id( $item ) {
		return sprintf('<span class="movie_tmdb_id">%1$s</span>', $item['tmdb_id'] );
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete'    => __( 'Delete', 'wpml' ),
			'tmdb_data' => __( 'Fetch data from TMDb', 'wpml' ),
		);
		return $actions;
	}
	
	function column_cb( $item ) {
		    return sprintf(
			    '<input type="checkbox" name="movie[]" value="%s" />', $item['ID']
		    );
	}
	
	function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->columns, array( &$this, 'usort_reorder' ) );
		
		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = count( $this->columns );
		
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page
			)
		);
		$this->items = $this->columns;
	}
 
}

?>