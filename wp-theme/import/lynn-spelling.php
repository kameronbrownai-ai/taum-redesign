<?php
/**
 * One spelling for Lynn Malatesta.
 *
 * The site had her as "Lyn" on Furniture, Community Center and Programs, and
 * "Lynn" on Contact and About. Abby spells it Lynn in her own email, and the
 * original page copy did too, so Lynn it is. A staff member's name spelled two
 * ways across five pages is the kind of thing a client notices immediately.
 *
 *   wp eval-file ../import/lynn-spelling.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$fix = function ( $s ) {
	// Only the standalone name, so "Lynn" is never doubled into "Lynnn".
	return preg_replace( '/\bLyn\b(?!n)/', 'Lynn', $s );
};

foreach ( get_posts( array( 'post_type' => array( 'page', 'post', 'taum_event' ), 'numberposts' => -1, 'post_status' => 'any' ) ) as $p ) {
	$c = $fix( $p->post_content );
	$e = $fix( $p->post_excerpt );
	if ( $c !== $p->post_content || $e !== $p->post_excerpt ) {
		wp_update_post( wp_slash( array( 'ID' => $p->ID, 'post_content' => $c, 'post_excerpt' => $e ) ) );
		WP_CLI::log( '  ' . $p->post_type . ' "' . $p->post_name . '"' );
	}
	foreach ( array( 'taum_contact_name', 'taum_kicker' ) as $k ) {
		$v = get_post_meta( $p->ID, $k, true );
		if ( $v && $fix( $v ) !== $v ) {
			update_post_meta( $p->ID, $k, $fix( $v ) );
			WP_CLI::log( '  ' . $p->post_name . ' meta ' . $k . ' -> ' . $fix( $v ) );
		}
	}
}
WP_CLI::success( 'Spelling normalized to Lynn.' );
