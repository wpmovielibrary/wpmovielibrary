=== WPMovieLibrary ===
Contributors: askelon
Donate link: http://wpmovielibrary.com/contribute/#donate
Tags: movie, movies, movie database, movie library, movie collection, cinema, movie genre, actor, actor, movie image, movie poster, movie meta, movie metadata, tmdb
Requires at least: 3.8
Tested up to: 4.0.1
Stable tag: 2.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WordPress Movie Library is an advanced movie library managing plugin to turn your WordPress Blog into a Movie Library.

== Description ==

The best way to manage your personnal movie library. Handle collections of movies, automatically fetch metadata, images and posters, create collections by mass importing lists of movie titles…

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

= Extensions =
Want to extend WPMovieLibrary? Additionnal extensions are available on [WPMovieLibrary/Extensions](http://wpmovielibrary.com/extensions/)

= Get involved =
Developers and Translators can contribute to the source code on the [GitHub Repository](https://github.com/CaerCam/wpmovielibrary/).

= Links =

*    [Official website](http://wpmovielibrary.com/)
*    [Documentation](http://wpmovielibrary.com/documentation/)
*    [Development](https://github.com/CaerCam/wpmovielibrary/)

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* PHP version 5.3 or greater (5.4 recommended)
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

= 2.0.2.2 =
* Tweak - Icon Font updated
* Fix bugs in legacy mode
* Fix Movie Headbox missing menu arrow
* Fix 'Add New' link blocked by JS in movie editor

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

== Upgrade notice ==
= 1.3 =
WPMovieLibrary 1.3 is a major update. Test extensions and your theme prior to updating, see that extensions are up to date and 1.3 compatible, and be sure to keep backups of your databases. Reading the [release notes](http://wpmovielibrary.com/development/release-notes/#version-1.3) is a good idea too.
