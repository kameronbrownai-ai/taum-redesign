/**
 * Click a gallery photo or an event flyer to see it full size.
 *
 * Works on what WordPress already outputs, so Abby can add photos to the
 * gallery block the normal way and they pick this up with no extra step.
 * The full-size file comes out of the image's own srcset rather than from a
 * link, because the gallery block is not set to link images to their media
 * file and we do not want to depend on an editor remembering to set that.
 *
 * Accessibility: each figure keeps its figure and figcaption semantics and
 * gains an explicit "Enlarge" button, rather than being turned into a button
 * and losing them. The dialog traps focus, closes on Escape, moves on arrow
 * keys, and hands focus back to whatever opened it.
 */
( function () {
	'use strict';

	var GROUPS = [
		{ sel: '.page-body .wp-block-gallery.has-nested-images > figure.wp-block-image', noun: 'photo' },
		{ sel: '.flyer-archive > figure', noun: 'flyer' }
	];

	var sets = [];
	var lb, img, cap, count, btnPrev, btnNext, btnClose;
	var current = null;   // { items: [], i: 0 }
	var lastFocus = null;

	/* The widest file the browser was offered. srcset is "url 1024w, url 1600w". */
	function fullSrc( el ) {
		var best = el.getAttribute( 'src' ) || '';
		var bestW = 0;
		( el.getAttribute( 'srcset' ) || '' ).split( ',' ).forEach( function ( part ) {
			var m = part.trim().match( /^(\S+)\s+(\d+)w$/ );
			if ( m && parseInt( m[ 2 ], 10 ) > bestW ) {
				bestW = parseInt( m[ 2 ], 10 );
				best = m[ 1 ];
			}
		} );
		return best;
	}

	function captionOf( figure, el ) {
		var fc = figure.querySelector( 'figcaption' );
		var text = fc ? fc.textContent.trim().replace( /\s+/g, ' ' ) : '';
		return text || el.getAttribute( 'alt' ) || '';
	}

	function build() {
		lb = document.createElement( 'div' );
		lb.className = 'lb';
		lb.setAttribute( 'role', 'dialog' );
		lb.setAttribute( 'aria-modal', 'true' );
		lb.setAttribute( 'aria-label', 'Image viewer' );
		lb.hidden = true;
		lb.innerHTML =
			'<button type="button" class="lb-close" aria-label="Close">&times;</button>' +
			'<button type="button" class="lb-nav lb-prev" aria-label="Previous">&#8249;</button>' +
			'<button type="button" class="lb-nav lb-next" aria-label="Next">&#8250;</button>' +
			'<figure class="lb-stage">' +
			'<img alt="">' +
			'<figcaption></figcaption>' +
			'</figure>' +
			'<p class="lb-count" aria-live="polite"></p>';
		document.body.appendChild( lb );

		img      = lb.querySelector( 'img' );
		cap      = lb.querySelector( 'figcaption' );
		count    = lb.querySelector( '.lb-count' );
		btnPrev  = lb.querySelector( '.lb-prev' );
		btnNext  = lb.querySelector( '.lb-next' );
		btnClose = lb.querySelector( '.lb-close' );

		btnClose.addEventListener( 'click', close );
		btnPrev.addEventListener( 'click', function () { step( -1 ); } );
		btnNext.addEventListener( 'click', function () { step( 1 ); } );

		/* Only the backdrop closes. A click on the photo itself should not. */
		lb.addEventListener( 'click', function ( e ) {
			if ( e.target === lb || e.target === lb.querySelector( '.lb-stage' ) ) {
				close();
			}
		} );

		img.addEventListener( 'load', function () { lb.classList.remove( 'is-loading' ); } );

		/* Swipe sideways to move through the set, which is what a phone
		   expects. A mostly-vertical drag is left alone so the gesture does
		   not fight scrolling. */
		var startX = 0, startY = 0, tracking = false;
		lb.addEventListener( 'touchstart', function ( e ) {
			if ( 1 !== e.touches.length ) {
				return;
			}
			startX = e.touches[ 0 ].clientX;
			startY = e.touches[ 0 ].clientY;
			tracking = true;
		}, { passive: true } );
		lb.addEventListener( 'touchend', function ( e ) {
			if ( ! tracking ) {
				return;
			}
			tracking = false;
			var dx = e.changedTouches[ 0 ].clientX - startX;
			var dy = e.changedTouches[ 0 ].clientY - startY;
			if ( Math.abs( dx ) > 48 && Math.abs( dx ) > Math.abs( dy ) * 1.5 ) {
				step( dx < 0 ? 1 : -1 );
			}
		}, { passive: true } );
	}

	function show( i ) {
		var item = current.items[ i ];
		current.i = i;

		lb.classList.add( 'is-loading' );
		img.src = item.src;
		img.alt = item.alt;
		cap.textContent = item.caption;
		cap.hidden = ! item.caption;

		var many = current.items.length > 1;
		btnPrev.hidden = ! many;
		btnNext.hidden = ! many;
		count.textContent = many ? ( i + 1 ) + ' of ' + current.items.length : '';

		/* Have the next one ready before it is asked for. */
		if ( many ) {
			[ i + 1, i - 1 ].forEach( function ( j ) {
				var n = ( j + current.items.length ) % current.items.length;
				var pre = new Image();
				pre.src = current.items[ n ].src;
			} );
		}
	}

	function step( delta ) {
		if ( ! current || current.items.length < 2 ) {
			return;
		}
		show( ( current.i + delta + current.items.length ) % current.items.length );
	}

	function open( items, i ) {
		if ( ! lb ) {
			build();
		}
		lastFocus = document.activeElement;
		current = { items: items, i: i };

		/* Lock the page behind the dialog without letting it jump sideways
		   when the scrollbar goes away. */
		var gap = window.innerWidth - document.documentElement.clientWidth;
		document.body.style.overflow = 'hidden';
		if ( gap > 0 ) {
			document.body.style.paddingRight = gap + 'px';
		}

		lb.hidden = false;
		show( i );
		btnClose.focus();
	}

	function close() {
		if ( ! lb || lb.hidden ) {
			return;
		}
		lb.hidden = true;
		img.removeAttribute( 'src' );
		document.body.style.overflow = '';
		document.body.style.paddingRight = '';
		current = null;
		if ( lastFocus && lastFocus.focus ) {
			lastFocus.focus();
		}
	}

	document.addEventListener( 'keydown', function ( e ) {
		if ( ! lb || lb.hidden ) {
			return;
		}
		if ( 'Escape' === e.key ) {
			e.preventDefault();
			close();
		} else if ( 'ArrowRight' === e.key ) {
			e.preventDefault();
			step( 1 );
		} else if ( 'ArrowLeft' === e.key ) {
			e.preventDefault();
			step( -1 );
		} else if ( 'Tab' === e.key ) {
			/* Keep focus inside the dialog. */
			var focusable = [].slice.call( lb.querySelectorAll( 'button:not([hidden])' ) );
			if ( ! focusable.length ) {
				return;
			}
			var first = focusable[ 0 ];
			var last = focusable[ focusable.length - 1 ];
			if ( e.shiftKey && document.activeElement === first ) {
				e.preventDefault();
				last.focus();
			} else if ( ! e.shiftKey && document.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		}
	} );

	/* Wire up every figure we are willing to enlarge. */
	GROUPS.forEach( function ( group ) {
		var figures = [].slice.call( document.querySelectorAll( group.sel ) );
		if ( ! figures.length ) {
			return;
		}

		var items = [];
		figures.forEach( function ( figure ) {
			var el = figure.querySelector( 'img' );
			if ( ! el ) {
				return;
			}

			var index = items.length;
			items.push( {
				src: fullSrc( el ),
				alt: el.getAttribute( 'alt' ) || '',
				caption: captionOf( figure, el )
			} );

			figure.classList.add( 'has-lb' );

			var zoom = document.createElement( 'button' );
			zoom.type = 'button';
			zoom.className = 'lb-zoom';
			zoom.setAttribute( 'aria-label', 'Enlarge this ' + group.noun +
				( items[ index ].caption ? ': ' + items[ index ].caption : '' ) );
			zoom.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' +
				'<circle cx="10.5" cy="10.5" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/>' +
				'<path d="M15.5 15.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
				'<path d="M10.5 7.5v6M7.5 10.5h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
				'</svg>';
			zoom.addEventListener( 'click', function ( e ) {
				e.stopPropagation();
				open( items, index );
			} );
			figure.appendChild( zoom );

			el.addEventListener( 'click', function () { open( items, index ); } );
		} );

		sets.push( items );
	} );
}() );
