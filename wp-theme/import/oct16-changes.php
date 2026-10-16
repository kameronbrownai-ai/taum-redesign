<?php
/**
 * Changes from Abby, 16 October.
 *
 * Office hours are now Monday, Wednesday and Thursday, 10 to 4, not Monday to
 * Friday 9 to 4. That one value feeds the footer, the Contact page, the
 * Community Center page and the Programs page, so it changes in TAUM Info and
 * every page follows.
 *
 * The meals and households figures are annual, from last year, which is what
 * the labels were missing. Both now say so.
 *
 * MLK: the worship celebration is the day before MLK Day and the breakfast is
 * on the day itself. Abby said 2026, but MLK Day 2026 was in January and has
 * passed, so these are the 2027 dates: Sunday 17 January for the worship
 * service and Monday 18 January for the breakfast. Flagged for her to confirm.
 *
 * Also: Cameron Carter off the staff list, Emile Wager onto the board, a
 * directions map on Contact, and a building-use card above the
 * "Need a specific program?" box, where Abby wanted it.
 *
 *   wp eval-file ../import/oct16-changes.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

/* ------------------------------------------------- Settings in TAUM Info */

$settings = array(
	'hours'   => 'Monday, Wednesday and Thursday, 10 am to 4 pm',
	'stat2_l' => 'meals shared from our kitchen last year',
	'stat3_l' => 'households furnished last year',
);
foreach ( $settings as $k => $v ) {
	$before = get_theme_mod( 'taum_' . $k, '(theme default)' );
	if ( $before !== $v ) {
		set_theme_mod( 'taum_' . $k, $v );
		WP_CLI::log( "  $k: $before -> $v" );
	} else {
		WP_CLI::log( "  $k: already set" );
	}
}

/** Replace once, and say so if the text to replace has moved. */
function taum_swap( &$content, $label, $old, $new ) {
	if ( '' !== $new && false !== strpos( $content, $new ) ) {
		WP_CLI::log( "  $label: already applied" );
		return false;
	}
	if ( false === strpos( $content, $old ) ) {
		WP_CLI::warning( "  $label: could not find the text to replace, do it by hand" );
		return false;
	}
	$content = str_replace( $old, $new, $content );
	WP_CLI::log( "  $label" );
	return true;
}

/* ------------------------------------------------------- About: people */

$about = get_page_by_path( 'about' );
if ( $about ) {
	$c = $about->post_content;
	$n = 0;

	$n += (int) taum_swap( $c, 'Cameron Carter removed from the staff list',
		'<div class="person">
<div class="avatar">CC</div>
<strong>Cameron Carter</strong>
<div class="role">Custodian</div>
Cameron keeps 392 Second Street clean, safe, and ready to welcome the neighborhood.

</div>
',
		'' );

	$n += (int) taum_swap( $c, 'Emile Wager added to the board',
		'James Parmelee · Azra Haqqie · Sally Lauletta · Jean Howard',
		'James Parmelee · Azra Haqqie · Sally Lauletta · Jean Howard · Emile Wager' );

	if ( $n ) {
		$r = wp_update_post( wp_slash( array( 'ID' => $about->ID, 'post_content' => $c ) ), true );
		if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
		WP_CLI::log( '  about page saved' );
	}
}

/* ----------------------------------------------------------- MLK dates */

$mlk = get_page_by_path( 'mlk' );
if ( $mlk ) {
	$c = $mlk->post_content;
	$n = 0;

	$n += (int) taum_swap( $c, 'MLK Day line names both days',
		'<strong>MLK Day 2027 falls on Monday, January 18.</strong>',
		'<strong>MLK Day 2027 falls on Monday, January 18.</strong> The worship celebration is the day before, on Sunday, January 17, and the breakfast is on the holiday itself.' );

	$n += (int) taum_swap( $c, 'worship service dated',
		'<h3>MLK Day worship service</h3>
    <p>A service of remembrance and recommitment, with guest speakers and music. The committee is still working out the details, and it will most likely be at Bethel Baptist, as it has been in recent years.</p>',
		'<h3>MLK weekend worship celebration</h3>
    <p><strong>Sunday, January 17</strong>, the day before the holiday. A service of remembrance and recommitment, with guest speakers and music. The committee is still working out the details, and it will most likely be at Bethel Baptist, as it has been in recent years.</p>' );

	$n += (int) taum_swap( $c, 'breakfast dated',
		'<p><strong>8:30 am at Franklin Terrace, Troy.</strong> Neighbors, congregations and community partners at one table before the service.</p>',
		'<p><strong>Monday, January 18, 8:30 am at Franklin Terrace, Troy.</strong> Neighbors, congregations and community partners at one table on the holiday itself.</p>' );

	if ( $n ) {
		$r = wp_update_post( wp_slash( array( 'ID' => $mlk->ID, 'post_content' => $c ) ), true );
		if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
		WP_CLI::log( '  MLK page saved' );
	}
}

/* ------------------------------------------- Contact: map and building use */

$contact = get_page_by_path( 'contact' );
if ( $contact ) {
	$c = $contact->post_content;
	$n = 0;

	$addr = taum_opt( 'address1' ) . ', ' . taum_opt( 'address2' );
	$q    = rawurlencode( $addr );

	$map = '<h2 style="margin-top:44px;">Find us</h2>
  <div class="grid2">
   <div class="card">
    <h3>392 Second Street, Troy</h3>
    <p>We are on Second Street in the Osgood neighborhood, a few minutes from downtown Troy. Look for the bird mural, you cannot miss it.</p>
    <p style="margin-top:12px;color:var(--indigo-soft);font-size:.93rem;"><strong>Office hours:</strong> [taum hours]</p>
    <p style="margin-top:14px;">
     <a class="btn small" href="https://www.google.com/maps/dir/?api=1&amp;destination=' . $q . '" rel="noopener">Get directions</a>
    </p>
   </div>
   <?php // Not maps.google.com/...&output=embed: that 301s with X-Frame-Options SAMEORIGIN and the browser blocks the frame on the redirect. ?>
   <div class="map-embed">
    <iframe title="Map showing TAUM at ' . esc_attr( $addr ) . '" src="https://www.google.com/maps/embed?origin=mfe&amp;pb=!1m2!2m1!1s' . $q . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
   </div>
  </div>

  ';

	$n += (int) taum_swap( $c, 'directions map added',
		'<h2 style="margin-top:44px;">Send us a message</h2>',
		$map . '<h2 style="margin-top:44px;">Send us a message</h2>' );

	$building = '<div class="callout warm" style="margin-top:30px;">
   <h3>Want to use the building?</h3>
   <p>The community room, the kitchen and the smaller meeting rooms are available to the neighborhood. Fifty dollars an hour for businesses and private hire, a donation for nonprofits and community groups.</p>
   <p style="margin-top:14px;"><a class="btn small" href="/community-center/">About the space and how to book it</a></p>
  </div>

  ';

	$n += (int) taum_swap( $c, 'building use card sits above "Need a specific program?"',
		'<div class="callout" style="margin-top:30px;">
   <h3>Need a specific program?</h3>',
		$building . '<div class="callout" style="margin-top:30px;">
   <h3>Need a specific program?</h3>' );

	if ( $n ) {
		$r = wp_update_post( wp_slash( array( 'ID' => $contact->ID, 'post_content' => $c ) ), true );
		if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
		WP_CLI::log( '  contact page saved' );
	}
}

WP_CLI::success( 'Applied.' );
