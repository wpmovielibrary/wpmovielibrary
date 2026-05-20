<div class="wpmovielibrary-widget library-widget">
    <div class="wpmovielibrary-widget-header">{{ esc_html__( 'My Library', 'wpmovielibrary' ) }}</div>
    <div class="wpmovielibrary-widget-content">
        <p>{!! esc_html( 'Here\'s some statistics about your library:', 'wpmovielibrary' ) !!}</p>
        <ul>
            <li>
                @if ( 0 < $totals['movies'] ) <a href="{{ esc_url( admin_url( 'edit.php?post_type=movie' ) ) }}"> @else <span> @endif
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Pro 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2024 Fonticons, Inc.--><path d="M361.9 32l-1 1-127 127h92.1l1-1 127-127H361.9zM512 160V41.9L393.9 160H512zM294.1 32H201.9l-1 1L73.9 160h92.1l1-1 127-127zM0 32V160H6.1l1-1 127-127H0zM512 192H0V480H512V192z"/></svg>
                @if ( 0 < $totals['movies'] )
                    {!! esc_html( sprintf( _n( '%s movie', '%s movies', $totals['movies'], 'wpmovielibrary' ), $totals['movies'] ) ) !!}</a>
                @else
                    {!! esc_html__('No draft', 'wpmovielibrary') !!}
                @endif
                @if ( 0 < $totals['movies'] ) </a> @else </span> @endif
            </li>
            <li>
                @if ( 0 < $totals['drafts'] ) <a href="{{ esc_url( admin_url( 'edit.php?post_status=draft&post_type=movie' ) ) }}"> @else <span> @endif
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Pro 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM144 288L252.7 179.3l80 80L224 368l-96 16 16-96zm256-96l-44.7 44.7-80-80L320 112l80 80z"/></svg>
                @if ( 0 < $totals['drafts'] )
                    {!! esc_html( sprintf( _n( '%s draft', '%s drafts', $totals['drafts'], 'wpmovielibrary' ), $totals['imported'] ) ) !!}</a>
                @else
                    {!! esc_html__('No draft', 'wpmovielibrary') !!}
                @endif
                @if ( 0 < $totals['drafts'] ) </a> @else </span> @endif
            </li>
            <li>
                @if ( 0 < $totals['imported'] ) <a href="{{ esc_url( admin_url( 'admin.php?page=wpmovielibrary-importer' ) ) }}"> @else <span> @endif
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Pro 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2024 Fonticons, Inc.--><path d="M144 480H0V336c0-62.7 40.1-116 96-135.8V192c0-88.4 71.6-160 160-160c59.3 0 111 32.2 138.7 80.2C409.9 102 428.3 96 448 96c53 0 96 43 96 96v36c55.2 14.2 96 64.3 96 124V480H512 144zm79-167l80 80 17 17 17-17 80-80 17-17L400 262.1l-17 17-39 39V184 160H296v24V318.1l-39-39-17-17L206.1 296l17 17z"/></svg>
                @if ( 0 < $totals['imported'] )
                    {!! esc_html( sprintf( _n( '%s import draft', '%s import drafts', $totals['imported'], 'wpmovielibrary' ), $totals['imported'] ) ) !!}</a>
                @else
                    {!! esc_html__('No import drafts.', 'wpmovielibrary') !!}
                @endif
                @if ( 0 < $totals['imported'] ) </a> @else </span> @endif
            </li>
            <li>
                @if ( 0 < $totals['queued'] ) <a href="{{ esc_url( admin_url( 'admin.php?page=wpmovielibrary-importer' ) ) }}"> @else <span> @endif
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Pro 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2024 Fonticons, Inc.--><path d="M0 32H576V480H0V32zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248H200v48h24H448h24V136H448 224zm0 96H200v48h24H448h24V232H448 224zm0 96H200v48h24H448h24V328H448 224z"/></svg>
                @if ( 0 < $totals['queued'] )
                    {!! esc_html( sprintf( _n( '%s queued import', '%s queued imports', $totals['queued'], 'wpmovielibrary' ), $totals['queued'] ) ) !!}</a>
                @else
                    {!! esc_html__('No queued import.', 'wpmovielibrary') !!}
                @endif
                @if ( 0 < $totals['queued'] ) </a> @else </span> @endif
            </li>
        </ul>
        <p>{!! sprintf(
                esc_html__( 'All combined you have a total of %1$s in your library, regrouped in %2$s, %3$s and %4$s.', 'wpmovielibrary' ),
                '<a href="' . esc_url( admin_url( 'edit.php?post_type=movie' ) ) . '"><strong>' . ( $totals['movies'] ?? '0' ) . '</strong> movie' . ( ( $totals['movies'] ?? 0 ) === 1 ? '' : 's' ) . '</a>',
                '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=collection&post_type=movie' ) ) . '">' . sprintf( _n( '<strong>1</strong> collection', '<strong>%s</strong> collection%s', $totals['collections'], 'wpmovielibrary' ), $totals['collections'] ?? '0', ( ( $totals['collections'] ?? 0 ) === 1 ? '' : 's' ) ) . '</a>',
                '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=genre&post_type=movie' ) ) . '">' . sprintf( _n( '<strong>1</strong> genre', '<strong>%s</strong> genre%s', $totals['genres'], 'wpmovielibrary' ), $totals['genres'] ?? '0', ( ( $totals['genres'] ?? 0 ) === 1 ? '' : 's' ) ) . '</a>',
                '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=actor&post_type=movie' ) ) . '">' . sprintf( _n( '<strong>1</strong> actor', '<strong>%s</strong> actor%s', $totals['actors'], 'wpmovielibrary' ), $totals['actors'] ?? '0', ( ( $totals['actors'] ?? 0 ) === 1 ? '' : 's' ) ) . '</a>'
            ) !!}</p>
    </div>
</div>