@extends('layout')

@section('content')
    <div class="wpmovielibrary-panels">
        <div class="wpmovielibrary-panel">
            <div class="wpmovielibrary-panel-header">
                {!! esc_html( $post_type_label ) !!}
                <div class="actions">
                    <a href="{{ esc_url( admin_url( "post-new.php?post_type={$post_type}" ) ) }}" class="button button-primary">{{ esc_html( $post_type_labels->add_new_item ) }}</a>
                </div>
            </div>
            <div class="wpmovielibrary-panel-content">
                <div id="wpmovielibrary-index" class="wpmovielibrary wpmovielibrary-index wpmovielibrary-{{ esc_attr( $post_type ) }}-index" data-post-type="{{ esc_attr( $post_type ) }}" data-posts-per-page="{{ esc_attr( $posts_per_page ) }}" data-post-type-label="{{ $post_type_label }}" data-post-type-labels='{{ wp_json_encode( $post_type_labels ) }}'>
                    <noscript>
                        <div class="notice notice-error notice-alt"><p>wpMovieLibrary requires JavaScript. Please enable JavaScript in your browser settings.</p></div>
                    </noscript>
                </div>
            </div>
        </div>
    </div>
@endsection