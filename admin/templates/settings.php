@extends('layout')

@props([
	'tmdb_api_key'  => '',
	'tmdb_language' => 'en',
	'tmdb_country'  => 'US',
	'saved'         => false,
])

@section('content')
	<div class="wpmovielibrary-panels">
		<form class="wpmovielibrary-panel wpmovielibrary-settings" method="post" action="{{ esc_url( admin_url( 'admin-post.php' ) ) }}">
			<input type="hidden" name="action" value="wpmoly_save_settings">
			{!! wp_nonce_field( 'wpmoly_save_settings', '_wpnonce', true, false ) !!}

				<div class="wpmovielibrary-panel-header">{{ esc_html__( 'Settings', 'wpmovielibrary' ) }}</div>
				<div class="wpmovielibrary-panel-content">
					<div class="wpmovielibrary-settings-section">
						<div class="wpmovielibrary-settings-section-header">
							{{ esc_html__( 'TMDb', 'wpmovielibrary' ) }}
						</div>
						<div class="wpmovielibrary-settings-section-fields">
							<div class="wpmovielibrary-settings-field">
								<div class="wpmovielibrary-settings-field-label">
									<label for="wpmoly-tmdb-api-key">{{ esc_html__( 'API Key', 'wpmovielibrary' ) }}</label>
									<p class="description">{{ esc_html__( 'Your TMDb API key. Required to search and import movie metadata.', 'wpmovielibrary' ) }}</p>
								</div>
								<div class="wpmovielibrary-settings-field-input">
									<input type="password" id="wpmoly-tmdb-api-key" name="wpmoly_settings[tmdb][api_key]" value="{{ $tmdb_api_key }}" class="regular-text" autocomplete="off">
								</div>
							</div>
							<div class="wpmovielibrary-settings-field">
								<div class="wpmovielibrary-settings-field-label">
									<label for="wpmoly-tmdb-language">{{ esc_html__( 'Language', 'wpmovielibrary' ) }}</label>
									<p class="description">{{ esc_html__( 'Preferred language for TMDb metadata, as an ISO 639-1 code.', 'wpmovielibrary' ) }}</p>
								</div>
								<div class="wpmovielibrary-settings-field-input">
									<div class="wpmovielibrary-searchable-select" data-search-placeholder="{{ esc_attr__( 'Search languages…', 'wpmovielibrary' ) }}">
										<select id="wpmoly-tmdb-language" name="wpmoly_settings[tmdb][language]">
											@foreach ( $languages as $code => $language )
												<option value="{{ $code }}" data-flag="{{ $language['flag'] }}" data-name="{{ $language['name'] }}"{!! selected( $tmdb_language, $code, false ) !!}>{{ $language['flag'] }} {{ $language['name'] }} ({{ $code }})</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="wpmovielibrary-settings-field">
								<div class="wpmovielibrary-settings-field-label">
									<label for="wpmoly-tmdb-country">{{ esc_html__( 'Country', 'wpmovielibrary' ) }}</label>
									<p class="description">{{ esc_html__( 'Preferred country for release dates and certifications, as an ISO 3166-1 code.', 'wpmovielibrary' ) }}</p>
								</div>
								<div class="wpmovielibrary-settings-field-input">
									<div class="wpmovielibrary-searchable-select" data-search-placeholder="{{ esc_attr__( 'Search countries…', 'wpmovielibrary' ) }}">
										<select id="wpmoly-tmdb-country" name="wpmoly_settings[tmdb][country]">
											@foreach ( $countries as $code => $country )
												<option value="{{ $code }}" data-flag="{{ $country['flag'] }}" data-name="{{ $country['name'] }}"{!! selected( $tmdb_country, $code, false ) !!}>{{ $country['flag'] }} {{ $country['name'] }} ({{ $code }})</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="wpmovielibrary-panel-footer">
					<div class="wpmovielibrary-settings-notices">
						@if ( $saved )
							<div class="notice notice-success">{{ esc_html__( 'Settings saved.', 'wpmovielibrary' ) }}</div>
						@endif
					</div>
					<div class="wpmovielibrary-settings-actions">
						<button type="submit" class="button button-primary">{{ esc_html__( 'Save Settings', 'wpmovielibrary' ) }}</button>
					</div>
				</div>
			
		</form>
	</div>
@endsection
