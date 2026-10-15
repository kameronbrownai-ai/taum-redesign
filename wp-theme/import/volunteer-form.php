<?php
/**
 * Embed the volunteer sign-up instead of linking away to it.
 *
 * Abby sent the Little Green Light embed on 13 October, which is what she and
 * Kameron agreed to in the review: put the form on the page rather than send
 * people to a form on somebody else's site and hope they come back.
 *
 * The form goes on Get Involved under id="volunteer". Every other volunteer
 * link on the site then points at that anchor: the Volunteer item in the
 * header menu, which appears on all sixteen pages, the second button further
 * down Get Involved, the answer in the FAQ, and the sign-up link on any event
 * that pointed at the raw form.
 *
 *   wp eval-file ../import/volunteer-form.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$form   = 'https://secure.lglforms.com/form_engine/s/tyo0i-3Yd8ZON3oFUVTWuQ';
$anchor = home_url( '/get-involved/#volunteer' );

/* --------------------------------------------- The form itself, on the page */

$pg = get_page_by_path( 'get-involved' );
if ( ! $pg ) { WP_CLI::error( 'Get Involved page not found.' ); }

$content = $pg->post_content;

$embed = '<h2 id="volunteer" style="margin-top:44px;">Sign up to volunteer</h2>
  <p style="max-width:56ch;color:var(--indigo-soft);margin-top:10px;">Tell us what you are drawn to and how much time you have. There is no minimum, and we will come back to you about a shift that fits.</p>
  [taum_volunteer_form]';

if ( false !== strpos( $content, '[taum_volunteer_form]' ) ) {
	WP_CLI::log( '  form already embedded' );
} else {
	$old = '<p style="margin-top:24px;"><a class="btn" href="[taum url_volunteer_raw]">Fill out the volunteer form</a></p>';
	if ( false === strpos( $content, $old ) ) {
		WP_CLI::warning( '  could not find the volunteer button to replace, apply by hand' );
	} else {
		$content = str_replace( $old, $embed, $content );
		WP_CLI::log( '  form embedded where the button was' );
	}
}

/* The second button further down now scrolls up to the form on this page. */
$old2 = '<a class="btn small ghost" style="margin-top:12px;margin-left:8px;" href="[taum url_volunteer_raw]">Volunteer with us</a>';
$new2 = '<a class="btn small ghost" style="margin-top:12px;margin-left:8px;" href="#volunteer">Volunteer with us</a>';
if ( false !== strpos( $content, $old2 ) ) {
	$content = str_replace( $old2, $new2, $content );
	WP_CLI::log( '  second button points at the form above' );
}

if ( $content !== $pg->post_content ) {
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $content ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( '  Get Involved saved' );
}

/* ------------------------------------------------------------- The FAQ */

$faq = get_page_by_path( 'faq' );
if ( $faq ) {
	$c   = $faq->post_content;
	$old = '<a href="[taum url_volunteer_raw]">Fill out the volunteer form</a>';
	$new = '<a href="' . esc_url( $anchor ) . '">Fill out the volunteer form</a>';
	if ( false !== strpos( $c, $old ) ) {
		$r = wp_update_post( wp_slash( array( 'ID' => $faq->ID, 'post_content' => str_replace( $old, $new, $c ) ) ), true );
		if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
		WP_CLI::log( '  FAQ answer points at the form' );
	} else {
		WP_CLI::log( '  FAQ already updated' );
	}
}

/* ------------------------------------------------- Menu item and events */

global $wpdb;

$menu_items = $wpdb->get_col( $wpdb->prepare(
	"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_menu_item_url' AND meta_value LIKE %s",
	'%' . $wpdb->esc_like( 'tyo0i-3Yd8ZON3oFUVTWuQ' ) . '%'
) );
foreach ( $menu_items as $id ) {
	update_post_meta( $id, '_menu_item_url', $anchor );
	WP_CLI::log( '  menu item ' . $id . ' (' . get_the_title( $id ) . ') points at the form' );
}
if ( ! $menu_items ) {
	WP_CLI::log( '  menu already updated' );
}

$events = get_posts( array(
	'post_type'   => 'taum_event',
	'numberposts' => -1,
	'post_status' => 'any',
	'meta_query'  => array( array( 'key' => 'taum_event_url', 'value' => 'tyo0i-3Yd8ZON3oFUVTWuQ', 'compare' => 'LIKE' ) ),
) );
foreach ( $events as $ev ) {
	update_post_meta( $ev->ID, 'taum_event_url', $anchor );
	WP_CLI::log( '  event "' . get_the_title( $ev ) . '" points at the form' );
}
if ( ! $events ) {
	WP_CLI::log( '  events already updated' );
}

WP_CLI::success( 'Volunteer form embedded and links repointed.' );
