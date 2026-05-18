@props([
    'plugin_page' => '',
    'hook_suffix' => '',
])
<div class="wpmovielibrary-wrapper">
    <div class="wpmovielibrary-header">
        <div class="wpmovielibrary-brand">
            <span class="wpmovielibrary-name">wpMovieLibrary</span>
            <span class="wpmovielibrary-version"><?php echo WPMOLY_VERSION; ?></span>
        </div>
    </div>
    <div class="wpmovielibrary-menu">
        <ul>
            <li class="<?php echo 'wpmovielibrary' === $plugin_page ? 'is-active' : ''; ?>"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpmovielibrary' ) ) ?>"><?php esc_html_e( 'My Library', 'wpmovielibrary' ); ?></a></li>
            <li class="<?php echo 'edit.php' === $hook_suffix && 'movie' === $post_type ? 'is-active' : ''; ?>"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=movie' ) ); ?>"><?php esc_html_e( 'Movies', 'wpmovielibrary' ); ?></a></li>
            <li class="<?php echo 'wpmovielibrary-importer' === $plugin_page ? 'is-active' : ''; ?>"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpmovielibrary-importer' ) ) ?>"><?php esc_html_e( 'Importer', 'wpmovielibrary' ); ?></a></li>
            <li class="<?php echo 'wpmovielibrary-settings' === $plugin_page ? 'is-active' : ''; ?>"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpmovielibrary-settings' ) ) ?>"><?php esc_html_e( 'Settings', 'wpmovielibrary' ); ?></a></li>
            <li class="<?php echo 'wpmovielibrary-help' === $plugin_page ? 'is-active' : ''; ?>"><a href="#"><?php esc_html_e( 'Help', 'wpmovielibrary' ); ?></a></li>
        </ul>
    </div>
    <div class="wpmovielibrary-content">
        @yield('content')
    </div>
</div>