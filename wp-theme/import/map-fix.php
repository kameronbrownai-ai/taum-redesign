<?php
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }
/* maps.google.com/maps?...&output=embed answers with a 301 that carries
   X-Frame-Options: SAMEORIGIN, and the browser blocks the frame on the
   redirect before it ever reaches the frameable page. Point straight at the
   destination, which sends no such header. */
$pg = get_page_by_path( 'contact' );
if ( ! $pg ) { WP_CLI::error( 'Contact page not found.' ); }
$addr = taum_opt( 'address1' ) . ', ' . taum_opt( 'address2' );
$old_src = 'https://maps.google.com/maps?q=' . rawurlencode( $addr ) . '&amp;output=embed';
$new_src = 'https://www.google.com/maps/embed?origin=mfe&amp;pb=!1m2!2m1!1s' . rawurlencode( $addr );
$c = $pg->post_content;
if ( false !== strpos( $c, 'maps/embed?origin=mfe' ) ) {
	WP_CLI::log( '  already using the direct embed' );
} elseif ( false === strpos( $c, $old_src ) ) {
	WP_CLI::warning( '  could not find the map iframe source' );
} else {
	$c = str_replace( $old_src, $new_src, $c );
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $c ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( '  map now loads from the frameable URL' );
}
WP_CLI::success( 'Map fixed.' );
