<?php
/**
 * Movies Credit Editor Item Template
 *
 * @since 3.0.0
 */
?>

						<div class="person cast-member">
							<# if ( ! _.isNull( actor.profile_path ) ) { #>
							<div class="person-picture" style="background-image:url({{ 'https://image.tmdb.org/t/p/w185' + actor.profile_path || '' }})">
							<# } else if ( 1 === actor.gender ) { #>
							<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png' ); ?>)">
							<# } else if ( 2 === actor.gender ) { #>
							<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png' ); ?>)">
							<# } else { #>
							<div class="person-picture" style="background-image:url(<?php echo esc_url( WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png' ); ?>)">
							<# } #>
								<a href="#" title="Edit Person"><span class="wpmolicon icon-export"></span></a>
							</div>
							<div class="person-name"><a href="#">{{ actor.name }}</a></div>
							<div class="person-job">{{ actor.character }}</div>
						</div>
