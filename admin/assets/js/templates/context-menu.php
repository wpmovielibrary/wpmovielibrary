<?php
/**
 * Dashboard Context Menu Template
 *
 * @since 3.0.0
 */
?>

			<div class="context-menu-content">
				<# if ( data.groups.length ) { _.each( data.groups, function( group ) { #>
				<ul id="group-{{ group.id }}" class="context-menu-group">
					<# _.each( group.items, function( item ) { #>
					<# if ( ! item.groups.length ) { #>
					<li class="context-menu-item" data-item="{{ item.id }}">
					<# } else { #>
					<li class="context-menu-item">
					<# } #>
						<div class="context-menu-icon"><span class="{{ item.icon }}"></span></div>
						<div class="context-menu-text">{{ item.title }}</div>
						<# if ( item.groups.length ) { #>
						<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
						<div class="context-menu-sub-menu">
							<div class="context-menu-content context-sub-menu-content">
							<# _.each( item.groups, function( subgroup ) { #>
								<ul id="group-{{ subgroup.id }}" class="context-menu-group">
									<# if ( subgroup.items.length ) { _.each( subgroup.items, function( item ) { #>
										<# if ( item.selectable ) { #>
										<li class="context-menu-item">
											<input type="checkbox" class="hidden" id="{{ item.selectable.field }}-{{ item.selectable.value }}" data-field="{{ item.selectable.field }}" name="{{ item.selectable.field }}[]" value="{{ item.selectable.value }}">
											<label for="{{ item.selectable.field }}-{{ item.selectable.value }}">
												<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
												<div class="context-menu-text">{{ item.title }}</div>
											</label>
										</li>
										<# } else { #>
										<li class="context-menu-item" data-item="{{ item.id }}">
											<div class="context-menu-icon">{{ item.icon }}</div>
											<div class="context-menu-text">{{ item.title }}</div>
										</li>
										<# } #>
									<# } ); } #>
								</ul>
							<# } ); #>
							</div>
						</div>
						<# } #>
					</li>
					<# } ); #>
				</ul>
				<# } ); } #>
				<!--<div class="context-menu-item" data-action="preview">
					<div class="context-menu-icon"><span class="wpmolicon icon-movie"></span></div>
					<div class="context-menu-text">Preview</div>
				</div>
				<div class="context-menu-item" data-action="edit">
					<div class="context-menu-icon"><span class="wpmolicon icon-edit"></span></div>
					<div class="context-menu-text">Edit</div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-media"></span></div>
					<div class="context-menu-text">Media</div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-dvd" data-field="media" name="media[]" value="dvd">
								<label for="media-dvd">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">DVD</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-bluray" data-field="media" name="media[]" value="bluray">
								<label for="media-bluray">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Blu-ray</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-vod" data-field="media" name="media[]" value="vod" checked="checked">
								<label for="media-vod">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">VoD</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-divx" data-field="media" name="media[]" value="divx">
								<label for="media-divx">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">DivX</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-vhs" data-field="media" name="media[]" value="vhs">
								<label for="media-vhs">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">VHS</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-cinema" data-field="media" name="media[]" value="cinema">
								<label for="media-cinema">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Cinema</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="media-other" data-field="media" name="media[]" value="other">
								<label for="media-other">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Other</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-circle-thin"></span></div>
					<div class="context-menu-text">Status</div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item">
								<input type="radio" id="status-available" data-field="status" name="status[]" value="available">
								<label for="status-available">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Available</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="status-loaned" data-field="status" name="status[]" value="loaned">
								<label for="status-loaned">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Loaned</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="status-scheduled" data-field="status" name="status[]" value="scheduled">
								<label for="status-scheduled">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Scheduled</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="status-unavailable" data-field="status" name="status[]" value="unavailable">
								<label for="status-unavailable">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Unvailable</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-star"></span></div>
					<div class="context-menu-text">Rating</div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item">
								<input type="radio" id="rating-0.0" data-field="rating" name="rating[]" value="0.0">
								<label for="rating-0.0">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Not rated</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-0.5" data-field="rating" name="rating[]" value="0.5">
								<label for="rating-0.5">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Junk</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-1.0" data-field="rating" name="rating[]" value="1.0">
								<label for="rating-1.0">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Very bad</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-1.5" data-field="rating" name="rating[]" value="1.5">
								<label for="rating-1.5">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Bad</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-2.0" data-field="rating" name="rating[]" value="2.0">
								<label for="rating-2.0">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Not that bad</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-2.5" data-field="rating" name="rating[]" value="2.5">
								<label for="rating-2.5">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Average</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-3.0" data-field="rating" name="rating[]" value="3.0">
								<label for="rating-3.0">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Not bad</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-3.5" data-field="rating" name="rating[]" value="3.5">
								<label for="rating-3.5">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Good</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-4.0" data-field="rating" name="rating[]" value="4.0">
								<label for="rating-4.0">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Very good</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-4.5" data-field="rating" name="rating[]" value="4.5">
								<label for="rating-4.5">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Excellent</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="radio" id="rating-5.0" data-field="rating" name="rating[]" value="5.0">
								<label for="rating-5.0">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Masterpiece</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-format"></span></div>
					<div class="context-menu-text">Format</div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="format-3d" data-field="format" name="format[]" value="3d">
								<label for="format-3d">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">3D</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="format-sd" data-field="format" name="format[]" value="sd">
								<label for="format-sd">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">SD</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="format-hd" data-field="format" name="format[]" value="hd">
								<label for="format-hd">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">HD</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-subtitle"></span></div>
					<div class="context-menu-text">Subtitles</div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-none" data-field="subtitles" name="subtitles[]" value="none">
								<label for="subtitles-none">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Aucun</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-ar" data-field="subtitles" name="subtitles[]" value="ar">
								<label for="subtitles-ar">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Arabic</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-bg" data-field="subtitles" name="subtitles[]" value="bg">
								<label for="subtitles-bg">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Bulgarian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-cs" data-field="subtitles" name="subtitles[]" value="cs">
								<label for="subtitles-cs">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Czech</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-cn" data-field="subtitles" name="subtitles[]" value="cn">
								<label for="subtitles-cn">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Cantonese</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-da" data-field="subtitles" name="subtitles[]" value="da">
								<label for="subtitles-da">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Danish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-de" data-field="subtitles" name="subtitles[]" value="de">
								<label for="subtitles-de">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">German</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-el" data-field="subtitles" name="subtitles[]" value="el">
								<label for="subtitles-el">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Greek</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-en" data-field="subtitles" name="subtitles[]" value="en">
								<label for="subtitles-en">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">English</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-es" data-field="subtitles" name="subtitles[]" value="es">
								<label for="subtitles-es">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Spanish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-fa" data-field="subtitles" name="subtitles[]" value="fa">
								<label for="subtitles-fa">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Farsi</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-fi" data-field="subtitles" name="subtitles[]" value="fi">
								<label for="subtitles-fi">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Finnish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-fr" data-field="subtitles" name="subtitles[]" value="fr">
								<label for="subtitles-fr">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">French</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-he" data-field="subtitles" name="subtitles[]" value="he">
								<label for="subtitles-he">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Hebrew</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-hi" data-field="subtitles" name="subtitles[]" value="hi">
								<label for="subtitles-hi">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Hindi</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-hu" data-field="subtitles" name="subtitles[]" value="hu">
								<label for="subtitles-hu">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Hungarian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-it" data-field="subtitles" name="subtitles[]" value="it">
								<label for="subtitles-it">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Italian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-ja" data-field="subtitles" name="subtitles[]" value="ja">
								<label for="subtitles-ja">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Japanese</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-ko" data-field="subtitles" name="subtitles[]" value="ko">
								<label for="subtitles-ko">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Korean</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-nl" data-field="subtitles" name="subtitles[]" value="nl">
								<label for="subtitles-nl">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Dutch</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-no" data-field="subtitles" name="subtitles[]" value="no">
								<label for="subtitles-no">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Norwegian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-pl" data-field="subtitles" name="subtitles[]" value="pl">
								<label for="subtitles-pl">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Polish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-pt" data-field="subtitles" name="subtitles[]" value="pt">
								<label for="subtitles-pt">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Portuguese</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-ru" data-field="subtitles" name="subtitles[]" value="ru">
								<label for="subtitles-ru">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Russian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-sv" data-field="subtitles" name="subtitles[]" value="sv">
								<label for="subtitles-sv">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Swedish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-tr" data-field="subtitles" name="subtitles[]" value="tr">
								<label for="subtitles-tr">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Turkish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-uk" data-field="subtitles" name="subtitles[]" value="uk">
								<label for="subtitles-uk">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Ukrainian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="subtitles-zh" data-field="subtitles" name="subtitles[]" value="zh">
								<label for="subtitles-zh">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Mandarin</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item">
					<div class="context-menu-icon"><span class="wpmolicon icon-language"></span></div>
					<div class="context-menu-text">Languages</div>
					<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
					<div class="context-menu-sub-menu">
						<div class="context-menu-content context-sub-menu-content">
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-ar" data-field="language" name="language[]" value="ar">
								<label for="language-ar">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Arabic</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-bg" data-field="language" name="language[]" value="bg">
								<label for="language-bg">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Bulgarian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-cs" data-field="language" name="language[]" value="cs">
								<label for="language-cs">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Czech</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-cn" data-field="language" name="language[]" value="cn">
								<label for="language-cn">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Cantonese</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-da" data-field="language" name="language[]" value="da">
								<label for="language-da">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Danish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-de" data-field="language" name="language[]" value="de">
								<label for="language-de">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">German</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-el" data-field="language" name="language[]" value="el">
								<label for="language-el">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Greek</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-en" data-field="language" name="language[]" value="en">
								<label for="language-en">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">English</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-es" data-field="language" name="language[]" value="es">
								<label for="language-es">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Spanish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-fa" data-field="language" name="language[]" value="fa">
								<label for="language-fa">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Farsi</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-fi" data-field="language" name="language[]" value="fi">
								<label for="language-fi">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Finnish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-fr" data-field="language" name="language[]" value="fr">
								<label for="language-fr">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">French</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-he" data-field="language" name="language[]" value="he">
								<label for="language-he">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Hebrew</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-hi" data-field="language" name="language[]" value="hi">
								<label for="language-hi">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Hindi</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-hu" data-field="language" name="language[]" value="hu">
								<label for="language-hu">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Hungarian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-it" data-field="language" name="language[]" value="it">
								<label for="language-it">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Italian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-ja" data-field="language" name="language[]" value="ja">
								<label for="language-ja">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Japanese</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-ko" data-field="language" name="language[]" value="ko">
								<label for="language-ko">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Korean</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-nl" data-field="language" name="language[]" value="nl">
								<label for="language-nl">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Dutch</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-no" data-field="language" name="language[]" value="no">
								<label for="language-no">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Norwegian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-pl" data-field="language" name="language[]" value="pl">
								<label for="language-pl">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Polish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-pt" data-field="language" name="language[]" value="pt">
								<label for="language-pt">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Portuguese</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-ru" data-field="language" name="language[]" value="ru">
								<label for="language-ru">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Russian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-sv" data-field="language" name="language[]" value="sv">
								<label for="language-sv">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Swedish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-tr" data-field="language" name="language[]" value="tr">
								<label for="language-tr">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Turkish</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-uk" data-field="language" name="language[]" value="uk">
								<label for="language-uk">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Ukrainian</div>
								</label>
							</div>
							<div class="context-menu-item">
								<input type="checkbox" class="hidden" id="language-zh" data-field="language" name="language[]" value="zh">
								<label for="language-zh">
									<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
									<div class="context-menu-text">Mandarin</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="context-menu-item context-menu-separator"></div>
				<div class="context-menu-item draft-item" data-action="draft">
					<div class="context-menu-icon"><span class="wpmolicon icon-unpublish"></span></div>
					<div class="context-menu-text">Unpublish</div>
				</div>
				<div class="context-menu-item trash-item" data-action="trash">
					<div class="context-menu-icon"><span class="wpmolicon icon-trash"></span></div>
					<div class="context-menu-text">Delete</div>
				</div>-->
			</div>
