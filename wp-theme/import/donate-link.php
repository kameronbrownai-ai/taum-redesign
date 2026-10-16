<?php
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }
$pg = get_page_by_path( 'donate' );
if ( ! $pg ) { WP_CLI::error( 'Donate page not found.' ); }
$c = $pg->post_content;
$old = '<a class="btn" href="https://taum.org/donate/">Give online now</a>';
$new = '<a class="btn" href="[taum url_donate_raw]">Give online now</a>';
if ( false !== strpos( $c, $new ) ) {
	WP_CLI::log( '  already reading from TAUM Info' );
} elseif ( false === strpos( $c, $old ) ) {
	WP_CLI::warning( '  could not find the Give online now button' );
} else {
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => str_replace( $old, $new, $c ) ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( '  Give online now reads the Online giving page from TAUM Info' );
}
/* Any other hardcoded taum.org/donate link in page content goes the same way. */
foreach ( get_posts( array( 'post_type' => 'page', 'numberposts' => -1, 'post_status' => 'publish' ) ) as $p ) {
	if ( false === strpos( $p->post_content, 'https://taum.org/donate/' ) ) { continue; }
	$c2 = str_replace( 'https://taum.org/donate/', '[taum url_donate_raw]', $p->post_content );
	wp_update_post( wp_slash( array( 'ID' => $p->ID, 'post_content' => $c2 ) ) );
	WP_CLI::log( '  ' . $p->post_name . ': old taum.org donate link now reads from TAUM Info' );
}
WP_CLI::success( 'Donate links point at PayPal.' );
