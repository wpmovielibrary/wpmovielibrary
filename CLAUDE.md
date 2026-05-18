# WPMovieLibrary — Claude Code Instructions

## Project context

WPMovieLibrary is a WordPress plugin to manage a personal movie library. This is version 6, a complete rewrite building on several previous incomplete attempts (versions 2 through 5). The primary goal is to serve the author's personal needs, with a potential public release as a secondary objective.

## Scope — v6.0.0

The v6.0.0 scope is deliberately limited to back-office features:

- **Movie editor** — create and edit individual movie entries, search and import metadata from TMDb, automatic categorization, poster and image download
- **Bulk importer** — parse a list of movie titles, auto-create drafts, automatic and manual TMDb matching, bulk data and image download
- **Dashboard** — manage the movie library (search, quick add, recently added, quick categorization, statistics)

**Explicitly out of scope for v6.0.0:**
- Any front-end display feature (grids, filterable lists, archive page overrides)
- Shortcodes
- Complex settings/options panel
- Public-facing blocks
- Migration tools from previous versions

## Architecture

### Pages & interfaces

- **Dashboard** — dedicated admin page (`/wp-admin/admin.php?page=wpmovielibrary`), server-side rendered HTML, Alpine.js for interactivity
- **Importer** — dedicated admin page, React app with three parallel columns (list parsing, TMDb matching, import queue)
- **Movie editor** — Gutenberg block `wpmovielibrary/movie`, registered as default block for the `movie` post type; handles metadata display/editing, TMDb search, and poster/backdrop download via custom REST endpoint

### Directory structure

```
admin/
  templates/       # Server-side PHP templates
  assets/
    css/
    js/
    images/
blocks/
  movie/           # Gutenberg block — built by @wordpress/scripts
    block.json
    edit.js
    save.js
    index.js
    style.css
    editor.css
src/
  importer/        # Importer React app
  shared/          # Shared hooks, components, utilities
    hooks/
    components/
    utils/
public/
  assets/
    css/
    js/
```

### JavaScript entry points

- `src/importer/index.js`
- `blocks/movie/index.js`
- Shared hooks and components in `src/shared/` (`hooks/`, `components/`, `utils/`)

### REST API

- Custom namespace: `wpmovielibrary/v1`
- Media download endpoint for posters and backdrops (returns attachment ID)

### TMDb API

- The TMDb API key is stored as a simple WordPress option, with no settings UI in v6.0.0
- TMDb calls are made server-side only — the API key is never exposed to the client
- No proxy endpoint, no multi-user API key management — that belongs to a future public release

## Data structure

### Custom post type

- Slug: `movie`
- Managed by `Post_Types` class

### Taxonomies

- `genre` — movie genres
- `actor` — cast members
- `director` — directors
- `country` — production countries
- `language` — spoken languages
- Managed by `Taxonomies` class

### Post meta

- All meta keys prefixed with `_wpmoly_` (underscore prefix hides them from native WordPress UI)
- Core TMDb metadata: `_wpmoly_title`, `_wpmoly_year`, `_wpmoly_overview`, `_wpmoly_runtime`, `_wpmoly_rating`, `_wpmoly_tmdb_id`, `_wpmoly_imdb_id`, `_wpmoly_poster_path`, `_wpmoly_backdrop_path`
- Taxonomies are stored as standard WordPress terms, not as post meta
- Managed by `Post_Meta` class

## Templating system

Admin pages use a custom Blade-inspired template engine (`includes/class-template.php`). It is **not** a singleton — it is instantiated explicitly with a template directory and a cache directory.

**Key directives supported:** `@extends`, `@section`, `@endsection`, `@yield`, `@include`, `@props`, `@if / @elseif / @else / @endif`, `@foreach / @endforeach`, `@for / @endfor`, `@while / @endwhile`, `{{ }}` (escaped output), `{!! !!}` (raw output), `{{-- --}}` (comments).

**Cache behavior:**
- `$cache = true` (production) — compiles templates to disk, executes via `include`
- `$cache = false` (development) — compiles in memory, executes via `eval()` to avoid writing broken templates to disk

**Usage:**
```php
$template = new Template( WPMOLY_PATH . 'admin/templates', WPMOLY_PATH . 'cache', WP_DEBUG ? false : true );
echo $template->render( 'dashboard', [ 'movies' => $movies ] );
```

Templates use dot or slash notation: `'dashboard'`, `'partials/recently-added-movies'`. Never add `.php` extension manually.

## PHP code conventions

- PHP 8.x, strict WordPress coding standards
- Namespace `WPMovieLibrary` throughout
- All classes follow the singleton pattern with `private __construct()`, `private static $_instance`, and `public static get_instance()` calling a `private init()`
- Hooks are registered in `init()`, never in the constructor
- Class files named `class-{name}.php`, one class per file
- Docblocks on every class, method and property — include `@since`, `@access`, `@param`, `@return`; `@static` for static properties and methods
- No procedural code outside of the main plugin file `wpmovielibrary.php`
- Constants: `WPMOLY_VERSION`, `WPMOLY_PATH`, `WPMOLY_URL`
- Dependencies loaded explicitly via `require_once` in `load_dependencies()`, no autoloader
- No static utility methods, no helper functions — logic belongs in dedicated classes

## JavaScript code conventions

### Tooling

- `@wordpress/scripts` for build tooling (Webpack), not Vite
- Multiple entry points, one per app
- Alpine.js loaded via `wp_enqueue_script`, not bundled with `@wordpress/scripts`

### React (Importer & Movie Editor block)

- Functional components only, no class components
- Hooks for all state and side effects
- Prefer `@wordpress/components` for UI elements where appropriate
- No external React UI libraries (no MUI, no Ant Design, etc.)
- TMDb API calls via custom hooks in `src/shared/hooks/`
- WordPress REST API calls via `@wordpress/api-fetch`
- No inline styles — CSS classes only

### Dashboard

- Plain HTML rendered server-side
- Alpine.js for interactivity
- No React on the dashboard page

### General

- No jQuery, ever
- No direct `fetch()` calls — use `@wordpress/api-fetch` for WordPress endpoints, custom hooks for TMDb
- Nonces handled via `wp_localize_script` or `@wordpress/api-fetch` middleware

## Instructions for Claude Code

### General behavior

- Always analyze existing code before writing new code — understand the structure, follow the patterns
- When in doubt about a design decision, ask before implementing
- Never introduce new dependencies (Composer packages, npm packages) without explicit approval
- Prefer simple and explicit over clever and implicit

### PHP

- Always follow the singleton pattern defined in existing classes
- Never use procedural code outside of `wpmovielibrary.php`
- Never use static utility methods or helper functions — create a dedicated class instead
- Always use `require_once` explicitly, never assume autoloading
- Always add complete docblocks (`@since 6.0.0`, `@access`, `@param`, `@return`)

### JavaScript

- Never use jQuery
- Never use `fetch()` directly — use `@wordpress/api-fetch` for WordPress endpoints
- Never introduce React on the dashboard page
- Never use inline styles
- Always use functional components and hooks, never class components

### Out of scope — do not implement unless explicitly asked

- Any front-end display feature
- Shortcodes
- Settings/options panel beyond what is explicitly specified
- Migration tools from previous versions

## Language

- All code, comments, docblocks and variable names in English
- This file (`CLAUDE.md`) and commit messages in English
- The language of the conversation with Claude Code does not influence the language of the code it produces