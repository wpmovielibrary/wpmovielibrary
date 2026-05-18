@extends('layout')

@section('content')
    <div class="wpmovielibrary-panels">
        @include( 'partials.recently-added-movies' )
        @include( 'partials.most-rated-movies' )
        @include( 'partials.most-hated-movies' )
        @include( 'partials.my-collections' )
        @include( 'partials.my-genres' )
        @include( 'partials.my-actors' )
    </div>
    <div class="wpmovielibrary-sidebar is-wide">
        @include( 'partials.library-widget' )
        @include( 'partials.shortcuts-widget' )
        @include( 'partials.support-widget' )
        @include( 'partials.rating-widget' )
    </div>
@endsection