<?php
/**
 * Audit fixes.
 *   wp eval-file ../import/fix-audit.php
 *
 * 1. Nested anchors. The importer wrapped [taum email_*] inside the mailto link
 *    it was replacing. The shortcode already outputs a whole <a>, so the result
 *    was <a><a>…</a></a>, which browsers split into a working link plus an empty
 *    one. Six pages, and an empty link is announced to screen readers as a link
 *    with no name.
 * 2. Heading order. Several pages open with card <h3>s before the first <h2>,
 *    so the outline jumps h1 to h3.
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$fixed = 0;
foreach ( get_posts( array( 'post_type' => array( 'page', 'post' ), 'posts_per_page' => -1, 'post_status' => 'any' ) ) as $p ) {
	$c = $p->post_content;
	$e = $p->post_excerpt;
	// Unwrap: <a href="mailto:…">[taum email_x]</a>  ->  [taum email_x]
	$re = '#<a\s+href="mailto:[^"]*"\s*>\s*(\[taum\s+email_[a-z]+\s*\])\s*</a>#i';
	$c2 = preg_replace( $re, '$1', $c );
	$e2 = preg_replace( $re, '$1', $e );
	if ( $c2 !== $c || $e2 !== $e ) {
		wp_update_post( wp_slash( array( 'ID' => $p->ID, 'post_content' => $c2, 'post_excerpt' => $e2 ) ) );
		WP_CLI::log( sprintf( '  unwrapped  #%-4d %s', $p->ID, $p->post_name ) );
		$fixed++;
	}
}
WP_CLI::log( "  $fixed post(s) fixed" );
WP_CLI::success( 'Nested anchors removed.' );
