/**
 * Settings page — searchable select enhancement.
 *
 * Progressively enhances `.wpmovielibrary-searchable-select` wrappers into
 * searchable comboboxes. The native <select> remains the submitted form
 * field — this script only ever updates its value — so the form keeps
 * working untouched when JavaScript is unavailable.
 */
document.addEventListener( 'DOMContentLoaded', function() {

	document.querySelectorAll( '.wpmovielibrary-searchable-select' ).forEach( ( wrapper ) => {

		const select = wrapper.querySelector( 'select' );
		if ( ! select ) {
			return;
		}

		const options = Array.from( select.options ).map( ( option ) => ( {
			value: option.value,
			name: option.dataset.name || option.textContent.trim(),
			flag: option.dataset.flag || '',
		} ) );

		// Toggle button displaying the current selection.
		const toggle = document.createElement( 'button' );
		toggle.type = 'button';
		toggle.className = 'wpmovielibrary-searchable-select-toggle';
		toggle.setAttribute( 'aria-haspopup', 'listbox' );
		toggle.setAttribute( 'aria-expanded', 'false' );

		// Dropdown: search field + filtered options list.
		const dropdown = document.createElement( 'div' );
		dropdown.className = 'wpmovielibrary-searchable-select-dropdown';

		const search = document.createElement( 'input' );
		search.type = 'text';
		search.className = 'wpmovielibrary-searchable-select-search';
		search.placeholder = wrapper.dataset.searchPlaceholder || '';
		search.autocomplete = 'off';

		const list = document.createElement( 'ul' );
		list.className = 'wpmovielibrary-searchable-select-options';
		list.setAttribute( 'role', 'listbox' );

		dropdown.append( search, list );
		wrapper.append( toggle, dropdown );
		wrapper.classList.add( 'is-enhanced' );

		let filtered = options;
		let active = -1;

		const buildLabel = ( option ) => {
			const fragment = document.createDocumentFragment();

			const flag = document.createElement( 'span' );
			flag.className = 'option-flag';
			flag.textContent = option.flag;

			const name = document.createElement( 'span' );
			name.className = 'option-name';
			name.textContent = option.name;

			const code = document.createElement( 'span' );
			code.className = 'option-code';
			code.textContent = option.value;

			fragment.append( flag, name, code );

			return fragment;
		};

		const updateToggle = () => {
			const current = options.find( ( option ) => option.value === select.value );
			toggle.textContent = '';
			if ( current ) {
				toggle.append( buildLabel( current ) );
			}
		};

		const renderList = () => {
			list.textContent = '';
			filtered.forEach( ( option, index ) => {
				const item = document.createElement( 'li' );
				item.setAttribute( 'role', 'option' );
				item.setAttribute( 'aria-selected', option.value === select.value ? 'true' : 'false' );
				item.classList.toggle( 'is-active', index === active );
				item.append( buildLabel( option ) );
				item.addEventListener( 'click', () => pick( option ) );
				list.append( item );
			} );

			const activeItem = list.querySelector( '.is-active' );
			if ( activeItem ) {
				activeItem.scrollIntoView( { block: 'nearest' } );
			}
		};

		const filter = () => {
			const query = search.value.trim().toLowerCase();
			filtered = options.filter( ( option ) => {
				return ! query || option.name.toLowerCase().includes( query ) || option.value.toLowerCase().includes( query );
			} );
			if ( query ) {
				active = filtered.length ? 0 : -1;
			} else {
				active = filtered.findIndex( ( option ) => option.value === select.value );
			}
			renderList();
		};

		const open = () => {
			wrapper.classList.add( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'true' );
			search.value = '';
			filter();
			search.focus();
		};

		const close = () => {
			wrapper.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		};

		const pick = ( option ) => {
			select.value = option.value;
			select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			updateToggle();
			close();
			toggle.focus();
		};

		toggle.addEventListener( 'click', () => {
			if ( wrapper.classList.contains( 'is-open' ) ) {
				close();
			} else {
				open();
			}
		} );

		search.addEventListener( 'input', filter );

		search.addEventListener( 'keydown', ( event ) => {
			if ( 'ArrowDown' === event.key ) {
				event.preventDefault();
				active = Math.min( active + 1, filtered.length - 1 );
				renderList();
			} else if ( 'ArrowUp' === event.key ) {
				event.preventDefault();
				active = Math.max( active - 1, 0 );
				renderList();
			} else if ( 'Enter' === event.key ) {
				event.preventDefault();
				if ( filtered[ active ] ) {
					pick( filtered[ active ] );
				}
			} else if ( 'Escape' === event.key ) {
				event.stopPropagation();
				close();
				toggle.focus();
			}
		} );

		document.addEventListener( 'click', ( event ) => {
			if ( ! wrapper.contains( event.target ) ) {
				close();
			}
		} );

		updateToggle();
	} );
} );
