<?php
/**
 * Programs page rebuild.
 *
 * The page body carried a hand-written card for each program, using classes
 * (prog-grid, prog-card, prog-ico) that were never styled in either the static
 * site or the theme. So the cards were never a grid: six unstyled blocks,
 * 1,271px tall each, with unsized SVG icons drawn the size of a doorway.
 *
 * The cards now come from page-programs.php, built from the program pages
 * themselves. That also ends the copy drift: the body here still described the
 * chaplaincy with wording we corrected in October, called the MLK page the
 * scholarship and linked to the old slug, quoted a deadline that has passed,
 * and left the Community Center out of a list it introduced as "six programs".
 *
 * So this script:
 *   1. puts the "who to ask" details onto each program page as meta
 *   2. strips the duplicated cards out of the Programs page body
 *   3. leaves the CROP Walk, which is not a program page and has no card
 *
 *   wp eval-file ../import/programs.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

/* ------------------------------------------------- Who to ask, per program */

$contacts = array(
	'food'             => array( 'Barbara Healey, Damien Center Coordinator', '206', '', '' ),
	'damien-center'    => array( 'Barbara Healey, Damien Center Coordinator', '206', '', '' ),
	'furniture'        => array( 'Lyn Malatesta', '', '', 'lmalatesta@taum.org' ),
	'tech-for-teens'   => array( 'The program office', '202', '', '' ),
	'community-center' => array( 'The office', '', '', '' ),
	'chaplaincy'       => array( 'Darren Gundrum, Chaplain', '', '518-244-4522', 'gundrd@sage.edu' ),
	'mlk'              => array( 'Rev. Abby Norton-Levering, Executive Director', '', '', 'nortonlevering@taum.org' ),
);

foreach ( $contacts as $slug => $c ) {
	$pg = get_page_by_path( $slug );
	if ( ! $pg ) {
		WP_CLI::warning( "  $slug: page not found" );
		continue;
	}
	list( $name, $ext, $phone, $email ) = $c;
	update_post_meta( $pg->ID, 'taum_contact_name', $name );
	update_post_meta( $pg->ID, 'taum_contact_ext', $ext );
	update_post_meta( $pg->ID, 'taum_contact_phone', $phone );
	update_post_meta( $pg->ID, 'taum_contact_email', $email );
	WP_CLI::log( "  $slug: contact set to $name" );
}

/* ------------------------------------------- Strip the cards from the body */

$pg = get_page_by_path( 'programs' );
if ( ! $pg ) { WP_CLI::error( 'Programs page not found.' ); }

$content = $pg->post_content;

if ( false === strpos( $content, 'prog-grid' ) ) {
	WP_CLI::log( '  body already rebuilt' );
} else {
	// Everything from the opening prog-grid div through the last card. The
	// match is greedy and anchored on the callout that follows, because a
	// lazy one stops at the first </div></div>, which is inside card one.
	$cut = preg_replace( '#<div class="prog-grid">.*</div>\s*(?=<div class="callout")#s', '', $content, 1 );

	// The "not sure which door" row is in the template now, with the phone and
	// hours read from the Customizer rather than typed in. Drop the copy here
	// so the page does not say it twice. It runs to the end of the body.
	$cut = preg_replace( '#<div class="media-row" style="margin-top:44px;">.*$#s', '', $cut, 1 );

	$cut = trim( $cut );

	if ( false !== strpos( $cut, 'prog-grid' ) || false !== strpos( $cut, 'Not sure which door' ) ) {
		WP_CLI::warning( '  body: the cards did not come out cleanly, left alone. Check by hand.' );
	} else {
		$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $cut ) ), true );
		if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
		WP_CLI::log( '  body: cards and duplicate contact row removed, CROP Walk kept' );
	}
}

/* The intro counted six programs and named none of them. The page lists what
   it lists now, so the intro does not need to keep a tally that goes stale. */
$excerpt = 'Everything TAUM runs happens at 392 Second Street, each program with its own team. Find the one you need below and call the person who runs it. Not sure where to start? Ring the office on [taum phone] and we will point you the right way.';
$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_excerpt' => $excerpt ) ), true );
if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
WP_CLI::log( '  intro updated' );

WP_CLI::success( 'Programs page rebuilt.' );
