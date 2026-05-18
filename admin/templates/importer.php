@extends('layout')

@section('content')
    <div id="wpmovielibrary-importer" style="display:none">
        <div class="notice notice-error notice-alt"><p>{!! esc_html__( 'wpMovieLibrary requires JavaScript. Please enable JavaScript in your browser settings.', 'wpmovielibrary' ) !!}</p></div>
    </div>
    <div class="wpmovielibrary-panels">
        <div class="wpmovielibrary-panel wpmovielibrary-import-editor" data-portal-target="import-editor"></div>
        <div class="wpmovielibrary-panel wpmovielibrary-import-manager" data-portal-target="import-manager"></div>
    </div>
    <div class="wpmovielibrary-sidebar is-wide">
        <div class="wpmovielibrary-widget presentation-widget importer-widget">
            <div class="wpmovielibrary-widget-header">{{ esc_html__( 'Movie Importer', 'wpmovielibrary' ) }}</div>
            <div class="wpmovielibrary-widget-content">
                <p>{!! esc_html( 'The importer makes it easy to add multiple movies to your library without having to import each one individually.', 'wpmovielibrary' ) !!}</p>
                <ul>
                    <li>
                        <span data-portal-target="imported-movies-counter"></span>
                    </li>
                    <li>
                        <span data-portal-target="queued-movies-counter"></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="wpmovielibrary-widget wpmovielibrary-queue-manager queue-manager-widget" data-portal-target="queue-manager"></div>
    </div>
@endsection