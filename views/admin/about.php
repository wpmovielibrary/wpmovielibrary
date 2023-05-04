
	<div class="wrap about-wrap">

		<h1><?php printf( __( 'Welcome to WordPress Movie Library&nbsp;%s', 'wpmovielibrary' ), WPMOLY_VERSION ); ?></h1>

		<div class="about-text"><?php _e( 'Thank you for updating! Discover what this new version of WordPress Movie Library brought you.', 'wpmovielibrary' ); ?></div>

		<div class="wp-badge wpmoly-badge"><span class="wpmolicon icon-wpmoly"></span><?php printf( __( 'Version %s', 'wpmovielibrary' ), WPMOLY_VERSION ); ?></div>

		<h2 class="nav-tab-wrapper">
			<a href="#features" class="nav-tab nav-tab-active">
				<?php _e( 'Features', 'wpmovielibrary' ); ?>
			</a><a href="#recommended" class="nav-tab">
				<?php _e( 'Recommendations', 'wpmovielibrary' ); ?>
			</a><a href="#credits" class="nav-tab">
				<?php _e( 'Credits', 'wpmovielibrary' ); ?>
			</a>
		</h2>

		<div id="features" class="changelog">

			<div class="feature-section col two-col">
				<div class="col-1">
					<h3><?php _e( 'Find the essential', 'wpmovielibrary' ); ?></h3>
					<p><?php _e( 'Because your visitors and users want to know everything about your movies in a sight, the public movie information box has been remodeled to show you all you need to see in a single glimpse.', 'wpmovielibrary' ); ?></p>
					<p><?php _e( 'Furthermore, metadata have been completely reviewed to make you able to list all movies by a composer, a specific year or langague…', 'wpmovielibrary' ); ?></p>
				</div>
				<div class="col-2 last-feature">
					<img src="http://wpmovielibrary.com/media/2.0/newheadbox.jpg" />
				</div>
			</div>

			<hr />

			<div class="feature-section col two-col">
				<div class="col-1">
					<img src="http://wpmovielibrary.com/media/2.0/newmetabox.jpg" />
				</div>
				<div class="col-2 last-feature">
					<h3><?php _e( 'Smooth metadata editing', 'wpmovielibrary' ); ?></h3>
					<p><?php _e( 'Just as you want to find key information quickly, you do not want to spend hours editing your movies. The new metabox makes all the more easier for you to edit whatever information you want: metadata, details, images… The built-in preview tab gives you a nice glimpse of what you have collected so far.', 'wpmovielibrary' ); ?></p>
				</div>
			</div>

			<hr />

			<div class="feature-section col two-col">
				<div class="col-1">
					<h3><?php _e( 'Introducing the grid', 'wpmovielibrary' ); ?></h3>
					<p><?php _e( 'WordPress Movie Library 2.0 introduces a highly requested feature that will be enhanced add extended in forthcoming versions: the Grid. Show all your movies in an alphabetically sorted grid view and browse through you library.', 'wpmovielibrary' ); ?></p>
				</div>
				<div class="col-2 last-feature">
					<img src="http://wpmovielibrary.com/media/2.0/thegrid.jpg" />
				</div>
			</div>

			<hr />

			<div class="feature-section col two-col">
				<div class="col-1">
					<img src="http://wpmovielibrary.com/media/2.0/settingspanel.jpg" />
				</div>
				<div class="col-2 last-feature">
					<h3><?php _e( 'You own the place', 'wpmovielibrary' ); ?></h3>
					<p><?php printf( __( 'And therefore you should be able to tune your library as you please. That is now possible with the new Settings panel powered by the powerful <a href="%s">ReduxFramework</a>.', 'wpmovielibrary' ), 'http://reduxframework.com/' ); ?></p>
				</div>
			</div>

			<hr />

			<h3><?php _e( 'Other features include:', 'wpmovielibrary' ); ?></h3>

			<div class="feature-section col three-col">
				<div>
					<h4><?php _e( 'Multiple details selection', 'wpmovielibrary' ); ?></h4>
					<p><?php _e( 'You can now set multiple medias, languages or subtitles for movies you own on different format or language, or movies you saw in theater before buying DVDs.', 'wpmovielibrary' ); ?></p>
				</div>
				<div>
					<h4><?php _e( 'New extendable details', 'wpmovielibrary' ); ?></h4>
					<p><?php _e( 'WordPress Movie Library 2.0 includes three new avalaible details: Language, Subtitles and Video Format. Details have also been reorganized to make it easier to programmatically add your own personal details.', 'wpmovielibrary' ); ?></p>
				</div>
				<div class="last-feature">
					<h4><?php _e( 'Dedicated icon font', 'wpmovielibrary' ); ?></h4>
					<p><?php _e( 'WordPress Movie Library now uses a customized 100+ icons font to ensure compatibility with older WordPress versions that lack Dashicons.', 'wpmovielibrary' ); ?></p>
				</div>
			</div>

			<div class="feature-section col three-col">
				<div>
					<h4><?php _e( 'Search support', 'wpmovielibrary' ); ?></h4>
					<p><?php _e( 'You can now chose to include movies to your search results and search movies by meta along with other WordPress regular contents.', 'wpmovielibrary' ); ?></p>
				</div>
				<div>
					<h4><?php _e( 'Countries and languages translation', 'wpmovielibrary' ); ?></h4>
					<p><?php _e( 'Country and language names can now be translated to your own language.', 'wpmovielibrary' ); ?></p>
				</div>
				<div class="last-feature">
					<h4><?php _e( 'Translated permalinks', 'wpmovielibrary' ); ?></h4>
					<p><?php _e( 'All meta permalinks can now be translated in your own language as well.', 'wpmovielibrary' ); ?></p>
				</div>
			</div>
		</div>

		<hr />

		<div class="changelog under-the-hood">
			<h3 id="recommended"><?php _e( 'Recommendations', 'wpmovielibrary' ); ?></h3>

			<div class="feature-section col two-col">
				<div class="col-1">
					<img src="http://wpmovielibrary.com/media/2.0/updatemovies.jpg" />
				</div>
				<div class="col-2 last-feature">
					<h3><?php _e( 'WordPress Movie Library 1.x movies update', 'wpmovielibrary' ); ?></h3>
					<p><?php _e( 'The movie metadata changes in WordPress Movie Library 2.0 require that you update all your movies to the new metadata format in order to access new features. You can use the builtin updater tool to update your movies in a few seconds.', 'wpmovielibrary' ); ?></p>
					<p><?php printf( __( '<strong>Make backups of your data before updating your movies</strong>. You should always do this before updating a plugin to the next major release, but in this particular it is most recommended that you backup your site before anything. <a href="%s">Learn why</a>.', 'wpmovielibrary' ), 'http://wpmovielibrary.com/development/release-notes/#version-1.3' ); ?></p>
				</div>
			</div>

			<hr />

			<h3 id="credits"><?php _e( 'Credits', 'wpmovielibrary' ); ?></h3>

			<div class="feature-section">
				<div>
					<strong><?php _e( 'Lead developer', 'wpmovielibrary' ); ?></strong>: <a href="http://www.caercam.org/">Charlie MERLAND</a><br />
					<strong><?php _e( 'Faithful Contributors', 'wpmovielibrary' ); ?></strong>: lesurfeur, Ravavamouna, xdarkevil, zack06007, stargatehome, Fjellz, raicabogdan, mstashev, andyshears<br />
					<strong><?php _e( 'German translation', 'wpmovielibrary' ); ?></strong>: Mario Winkler<br />
				</div>
			</div>

		</div>

	</div>
