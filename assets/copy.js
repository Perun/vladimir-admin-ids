( function () {
	'use strict';

	const timers = new WeakMap();

	document.addEventListener( 'click', async function ( event ) {
		const button = event.target.closest( '.vladimir-admin-ids-copy' );
		if ( ! button || button.dataset.copying ) {
			return;
		}

		const cell = button.closest( '.vladimir-admin-ids-link' );
		const input = cell.querySelector( '.vladimir-admin-ids-url' );
		const status = cell.querySelector( '.vladimir-admin-ids-status' );

		clearTimeout( timers.get( status ) );
		status.textContent = '';
		button.dataset.copying = 'true';

		try {
			await navigator.clipboard.writeText( input.value );
			status.textContent = button.dataset.success;
			timers.set( status, setTimeout( function () {
				status.textContent = '';
			}, 2500 ) );
		} catch {
			input.focus();
			input.select();
			status.textContent = button.dataset.manual;
		} finally {
			delete button.dataset.copying;
		}
	} );

	document.documentElement.classList.add( 'vladimir-admin-ids-ready' );
}() );
