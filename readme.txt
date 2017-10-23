=== WPMovieLibrary ===
Contributors: caercam
Donate link: http://wpmovielibrary.com/contribute/#donate
Tags: movie, movies, movie database, movie library, movie collection, cinema, movie genre, actor, actor, movie image, movie poster, movie meta, movie metadata, tmdb
Requires at least: 4.2
Tested up to: 4.8.2
Stable tag: 2.1.4.7
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WordPress Movie Library is an advanced movie library managing plugin to turn your WordPress Blog into a Movie Library.

== Description ==

The best way to manage your personnal movie library. Handle collections of movies, automatically fetch metadata, images and posters, create collections by mass importing lists of movie titles… [Demo: See what it looks like!](http://demo.wpmovielibrary.com/)

= Announcing WPMovieLibrary 3.0 =
The plugin is under heavy work since late 2015 with a complete rewrite of the whole code in process. Be sure to check up frequently on the [official blog](http://wpmovielibrary.com/category/blog/) and social networks to see how developement is going!

= Simple yet powerfull =
WPMovieLibrary uses WordPress simple but efficient techniques to provide you a really simple tool to change your classic WordPress Blog to an extended management software for your movie collection.

= Automatic metadata =
WPMovieLibrary can import the metadata, images and posters of your movies. Simply fill in the title, hit the search button, and you're done. Director, genres, actors, producer, country, languages… All automatically fetched and related to your movie.

= Mass import = 
You own hundreds of DVDs? Provide a list of titles, WPMovieLibrary will import all your movies in a single shot.

= Custom dashboard =
WPMovieLibrary has its own custom dashboard and widgets to offer you a nice overview of your library.

= Customizable =
Using templates, WPMovieLibrary is extendable at will by themes or third-party plugins. The plugin is also open to developers and provides useful hooks to build external plugins. 

= Features =
Short list of supported features:

*    Custom Dashboard
*    Movie Custom Post Type
*    One click metadata import: title, overview, production, country(ies), language(s), runtime, release date, director of photography, composer, writer, screenwriter, cast & crew
*    One click images and posters download
*    Select your poster among a list of all available posters
*    Movie Status (Available, Scheduled, Loaned, Unavailable)
*    Movie Media (DVD, VOD, Blu-ray, Cinema, …)
*    Movie Rating
*    Bulk edit Movie Ratings, Medias and Statuses
*    Collection, Genre and Actor Custom Taxonomies to filter and organize your library
*    Mass import using lists of movie titles
*    Import Queue to easily import big collections in a couple of clicks
*    Movies, Details, Taxonomies and Statistics Widgets
*    Archive pages for Taxonomies
*    Complete configuration of the data you want to show on your blog
*    Much more!

= Get involved =
Developers and Translators can contribute to the source code on the [GitHub Repository](https://github.com/wpmovielibrary/wpmovielibrary/).

= Links =

*    [Official website](http://wpmovielibrary.com/)
*    [Documentation](http://wpmovielibrary.com/documentation/)
*    [Development](https://github.com/wpmovielibrary/wpmovielibrary/)

== Installation ==

= Minimum Requirements =

* WordPress 4.2 or greater
* PHP version 5.3 or greater (5.5 recommended)
* MySQL version 5.0 or greater

= Automatic installation =

The easiest Away to install WPMovieLibrary is to use the WordPress Extensions installer. Just log in to your WordPress dashboard, go to the Plugins page and hit "Add New". In the search field type input "wpmovielibrary" and click "Install". Easy!

= Manual installation =

1. Download the plugin from the WordPress.org Repository
1. Unzip the plugin archive file
1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use it!

== Frequently Asked Questions ==

= What exactly does it do? =
WordPress Movie Library is a plugin that helps you to create and manage your movie library.

= How does it work? =
You create/import movies like regular Posts/Pages. WPMovieLibrary provides various data related to your movies such as director, actors, genres, runtime, release date, production countries, budget… To help you sort your library efficiently. You have the possibility to affect specific, personal data to your movies: rating, media, status, language, subtitles…

Using WPMovieLibrary you will fetch all you need to know about a movie in a single click, metadata as well as images and posters.

= What's the difference between "Metadata" and "Details"? =
"Metadata" are generic data related to movies that are gathered from an external source, [TheMovieDB API](http://themoviedb.org): directors, actors, genres… "Details", on the other hand, are strictly personal and related to movies and most of all, you. How much you liked that movie (rating), how you own it / saw it (media), what's your copy current state (status), in which languages/subtitles did you saw it / do you own it, etc.

= How can I import metadata? =
Create a new Movie, input the movie title in search field below the content editor, hit "Search". If your search brings multiple results you will be presented a list of possible matches, else all fields will be automatically filled and you movie's poster automatically imported.

= How can I import my list of DVDs? =
Use the import tool: simply paste a list of titles, all your movies will be created instantly. Now you can import metadata one-by-one or group the search, and then import directly the movies or add it to the queue for later final import.

= Is this legal? =
Absolutely. Unlike most similar plugins, WPMovieLibrary uses TheMovieDB instead of IMDb due to the lack of proper official and legal API.

= Can I use another API (RottenTomatoes, IMDb, …)? =
No, at least not directly. WPMovieLibrary uses TMDb only.

= Why TMDb (and not another API)? =
Because TMDb seemed (and still does) the more effective and easy-to-use API out there. It may not be as complete as IMDb or other, but it is very openly usable; IMDb is great but does not provide an official API, requiring to use a workaround like OMDb API; RottenTomatoes is great too, but has strict terms of use including a restriction to use in US only. TMDb terms of use and features suits us most, so that's the one we use.

= Do I need an API key? =
No you don't. You can use your own TMDb API key for better results, but that is not required per se for WPMovieLibrary to work, the plugin can use its own API key if you don't provide one.

= My movies don't show in my homepage/slider/widget =
Your theme/plugin/widget most likely doesn't support Custom Post Types; open a thread in the support forum to get some help.

= Can I import TV Shows? =
No. As its name implies, WPMovieLibrary is about managing a movie library, and was never meant to include TV Shows.

= Do you plan to TV Shows support? =
Not really, no. It would require a huge lot of work to adapt the plugin's structure to TV Shows as it was built for movies only; we may develop a fork dedicated to TV Show, though, but that's not quite soon as WPMovieLibrary is already taking a lot of your time right now.

= All my metadata disappeared with the last update! =
Most likely they did not; if you've updated from a version 1.x your movies need to be updated to the new metadata format. You should be showed a notice in your dashboard informing you about this; if you're not, ask us for support on [WordPress.org Support](https://wordpress.org/support/plugin/wpmovielibrary) so that we can help you fix this.



== Screenshots ==

1. Plugin custom dashboard
2. All movies list
3. Edit movies
4. Download posters
5. Import by lists of titles
6. Imported list
7. Import queue

== Changelog ==

= 2.1.4.7 =
* Fix - Previously badly fixed rating formatting bug

= 2.1.4.6 =
* Feat - Add API support for Catalan language
* Fix - JavaScript error when trying to import already queued movies
* Fix - Missing importer spinner icon
* Fix - JavaScript error on featured image import
* Fix - Poster modal style
* Fix - Rating formatting bug
* Dev - Update ReduxFramework to version 3.6.7.7

= 2.1.4.5 =
* Fix - Metadata import bug (directors, producers, directors of photography... not properly imported since 2.1.4.4)

= 2.1.4.4 =
* Feat - Add a 'clear search' button the movie search form in metabox
* Tweak - Update user capabilities
* Fix - PHP7.1 bug resulting in server errors preventing import of new movies
* Dev - First draft of the Diagnose Tool

= 2.1.4.3 =
* Fix - URL escaping bug causing the movie grid letter URL malformation
* Fix - Empty date bug in importer resulting in movies being dated from January 1st, 1970
* Dev - Term ordering updated to match the modifications brought by WordPress 4.4

= 2.1.4.2 =
* Tweak - Movies Widget available/unavailable status distinction
* Tweak - About page style
* Tweak - Disable outdated notice
* Fix - Duplicate Dashboard menu entry
* Fix - SQL bug on term ordering
* Fix - PHP Notice on deprecated Widget constructor
* Dev - New filter for vintage content
* Dev - Fix potential XSS vulnerabilities
* Dev - Update Movies Widget Admin to Backbone.js

= 2.1.4.1 =
* Tweak - Edit misleading Settings description
* Tweak - Minor grid ordering tweak #243
* Tweak - Update l10n
* Fix - Minor JS messing with Customizer
* Fix - WordPress 4.2 WP_List_Table Class incompatibilities

= 2.1.4 =
* Feat - New Headbox styles: Allociné and IMDb
* Feat - Add a 'convert to movie' button to pages and posts
* Feat - Grid sorting modes: rating, release date, local release date
* Feat - Grid content modes: now supports collections, genres and actors
* Feat - Implement new [movie_posters] Shortcode
* Feat - Custom Post Types selection for taxonomies
* Tweak - New Archives position option
* Tweak - Implement [movie_rating] Shortcode
* Tweak - Add title attribute to meta links
* Tweak - New 'None' option for subtitles
* Fix - URL parsing bug when permalinks are disabled
* Fix - Rating stars erroneous title when using 10-base rating
* Fix - Permalink bug when permalink translation is set off
* Fix - Movies Widget title overflow	
* Fix - Archive pages title separator
* Dev - get_movie() / get_movie_by_title() parameters fix
* Dev - Updated permalink translation process
* Dev - Font update + Metabox tweaks

= 2.1.3.1 =
* Fix - Terms ordering minor bug fix
* Fix - Minor archives menu bug fix
* Fix - Runtime formatting bug when using strings
* Tweak - Bulk edit and metabox minor updates
* Tweak - Font update + minor style tweaks
* Dev - Smoother terms ordering process

= 2.1.3 =
* Fix - Details not shown properly on archive pages
* Fix - Possible empty grid bug on movie archives pages
* Fix - Images sizes labels in settings panel
* Fix - Movies Widget order by rating/media/status bug
* Tweak - Update Grid style to display properly titles and ratings
* Tweak - New Grid setting: show titles/ratings/years
* Tweak - Add years to importer movies choice
* Tweak - Add director and original title support to posters/images title
* Tweak - Updated Icon font
* Dev - Add a safety to meta queries preventing from overriding existing subpages
* Dev - Minor editor metabox tweaks

= 2.1.2 =
* Fix - Countries/languages names missing translation
* Fix - Release date formatting bug
* Fix - Movie archives pages titles missing translations
* Fix - Remove ReduxFramework potential crash-triggering part
* Fix - Poster size ignored on import
* Fix - Pagination bug on movie archive pages
* Tweak - Implement local release date meta query
* Tweak - Add numbers to actor taxonomy archives menu
* Tweak - Informative message in metabox details panel
* Dev - Update permalinks handling

= 2.1.1.2 =
* Fix - Forgotten debug notice

= 2.1.1.1 =
* Fix - PHP 5.3 bug in taxonomy archive pages
* Fix - [movie_grid] Shortcode bug returning empty

= 2.1.1 =
* Feat - Movie meta/detail archives use movie custom archives page
* Feat - New movie grid list and archives views
* Tweak - Improved handling of terms with apostrophes
* Tweak - Add missing meta Shortcodes: tagline, budget, revenue, certification, writer, imdb_id, tmdb_id, adult, homepage
* Tweak - Better meta URL and translation handling
* Tweak - Archives pages title rewrite
* Tweak - Empty grid message
* Fix - Movie archives page not falling back to default with no page set

= 2.1.0.1 =
* Fix - Permalink issue causing 404 on movie archives page

= 2.1 =
* Feat - Custom Archives pages
* Feat - Implement page creation tool
* Feat - Headbox customization settings
* Feat - Meta query URL by value and range of values
* Tweak - New metadata: local release date
* Tweak - Add year to movie select list in editor
* Tweak - Implement [movies] Shortcode pagination
* Tweak - Save metadata once they’re collected
* Tweak - Add movies to categories/tags archive pages
* Tweak - Not rated message instead of stars
* Tweak - Exclude current movie from Movies Widget in single
* Tweak - Add label for unknown movie duration
* Fix - Undeletable movie details
* Fix - Missing search results (pages not included)
* Fix - Empty search query
* Fix - Missing format in details view
* Fix - Apostrophe in actor name breaking taxonomies links
* Fix - Poster showing with poster=’none’ param in [movies] Shortcode
* Fix - ‘actors’ meta not working in [movie] Shortcode
* Fix - Collections added automatically despite settings in editor

= 2.0.2.2 =
* Tweak - Icon Font updated
* Fix - bugs in legacy mode
* Fix - Movie Headbox missing menu arrow
* Fix - 'Add New' link blocked by JS in movie editor

= 2.0.2.1 =
* Fix - Movie certification not correctly fetched on some occasions

= 2.0.2 =
* Fix - Remove deprecated assets images
* Fix - Remove depretated files from SVN repo

= 2.0.1 =
* Tweak - Add some responsive to editor metabox
* Fix - Few activation/deactivation missing tweaks

= 2.0 =
* Feature - Complete reboot of Settings
* Feature - Include movies in search results
* Feature - Coutry names and languages translation
* Feature - Production countries flags
* Feature - Find movies by metadata
* Feature - Grid Shortcode
* Tweak - Dedicated icon font
* Tweak - Updated Admin Bar menu
* Tweak - Updated Metabox in editor
* Tweak - Attachment Editor Modal Window accessible for imported images in movie editor (WP4+)
* Tweak - Random sorting added to Movies Widget
* Tweak - Movies by Meta in Movies Widget
* Tweak - Added Language, Subtitles and Video Format details
* Tweak - Added Certification, Adult, Budget, Revenue, Tagline and Homepage metadata
* Dev - Work in progress: lots of hooks and tweaks to make the plugin more extendable
* Dev - Updated metadata format

= 1.2.2 =
* Tweak - Media Modals CSS fixes for WordPress 4.0
* Dev - Manually add 'movies' permalink structure as it seem to conflict with some themes/plugins
* Fix - Genres and Actors Shortcodes missing labels
* Fix - Cache cleaning updated for WordPress 4.0 (like_escape deprecated since WordPress 4.0)

= 1.2.1 =
* Tweak - Added color effects on status box
* Tweak - Dashboard now showing plugin version
* Fix - Minor bug when movie runtime is set to 0
* Fix - Images/Modal not showing if no movie metadata has been imported
* Fix - Caching feature generating fatal errors with PHP 5.3
* Fix - Caching feature messing with Shortcodes
* Fix - CSS bug causing crushed posters in Movies Widget
* Fix - PHP notice in Dashboard Movies Widget

= 1.2 =
* Feature - Implement caching
* Feature - Implement templates
* Feature - Complete Widgets reboot
* Tweak - AJAX Queue for movie images import
* Tweak - Runtime and Release date Shortcodes format support
* Tweak - Movie lang Shortcode alias
* Tweak - Updated default dashboard Movies Widget when no movies
* Tweak - No load more button if no movie in dashboard Movies Widget
* Tweak - Custom Dashboard movies poster size update
* Tweak - Import queue styling (progress bar) and i10n
* Tweak - WP < 3.8 icons update
* Tweak - Custom Dashboard Vendor Widget
* Dev - WP_List_Table compat with WP4.0
* Dev - Metaboxes now defined in config to allow filtering
* Dev - API and Shortcodes more extendable
* Dev - Images and Posters Shortcodes more extendable
* Dev - Better API request error handling
* Dev - i10n handling enhanced
* Fix - Images/Posters modal bug causing random empty modals
* Fix - Custom Dashboard multiple minor fixes
* Fix - Don’t import unchecked movies in importer
* Fix - Minor nonce error in detail metabox
* Fix - Taxonomies not removed when emptying metadata
* Fix - Erratic enqueue_admin_scripts/styles
* Fix - Widgets checkbox options update
* Fix - Taxonomies Widget minor style bug

= 1.1.2 =
* Fix - PHP Warnings with shortcodes when no attribute is passed
* Tweak - Movie Images shortcodes basic styling

= 1.1.1 =
* Fix - Default poster for movies only
* Fix - PHP Warning with runtime and release date

= 1.1 =
* Features - Implements shortcodes
* Features - Create 8 new shortcodes
* Features - Create 16+ aliases for specific shortcodes
* Features - New default poster
* Tweak - Better display of directors, actors and genres in fronted, metadata are now matched against existing taxonomies to provide relevant links
* Tweak - Custom archive pages pagination
* Tweak - Custom archive pages specific titles
* Tweak - Show a dash instead of empty metadata in frontend
* Tweak - Add default runtime and release date/time formats
* Tweak - Limited number of items in Collections and Actors Widgets to lighten loading time
* Tweak - Updated default poster
* Dev - Better use of filters to display movie metadata and details and handle shortcodes and metadata aliases
* Dev - Cleaner Widgets views
* Fix - JavaScript bug in the movie editor preventing from manually setting a featured image after metadata import
* Fix - missing links on directors when a movie has two or more directors
* Fix - frontend PHP Warning when movie runtime is empty
* Fix - JavaScript bug when setting collections in movie editor
* Fix - Warnings on plugin activation when updating settings

= 1.0.2 =
* Fix - Markup error in readme
* Fix - Wrong PHP version requirement (PHP 5.3 required, not 5.2) - Thanks Ravavamouna

= 1.0.1 =
* Dev - Check plugin requirements before loading
* Fix - Missing status icon in frontend
* Fix - JS Search error in Movie Meta metabox
* Fix - Missing nonce in importer
* Fix - 12-hour time format bug on runtime
* Fix - JS error with custom dashboard widgets
* Tweak - Add 20+ new language for the API
* Tweak - New item in Dashboard "Right Now" Widget
* Tweak - WordPress < 3.8 icons and styling update
* Tweak - Backend CSS
* Tweak - Add an admin notice in case of missing Archive page
* Tweak - Language packs updated

= 1.0 =
* First stable release
