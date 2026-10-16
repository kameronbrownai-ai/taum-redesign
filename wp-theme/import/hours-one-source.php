<?php
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }
/* Any page that spells the office hours out in its own words goes stale the
   next time they change. Point them all at the one setting instead. */
$stale = array(
	'Monday to Friday, 9 am to 4 pm',
	'Monday to Friday, 9am to 4pm',
	'Monday to Friday 9 am to 4 pm',
);
foreach ( get_posts( array( 'post_type' => array( 'page', 'post' ), 'numberposts' => -1, 'post_status' => 'any' ) ) as $p ) {
	$c = $p->post_content;
	$e = $p->post_excerpt;
	foreach ( $stale as $s ) {
		$c = str_replace( $s, '[taum hours]', $c );
		$e = str_replace( $s, '[taum hours]', $e );
	}
	if ( $c !== $p->post_content || $e !== $p->post_excerpt ) {
		wp_update_post( wp_slash( array( 'ID' => $p->ID, 'post_content' => $c, 'post_excerpt' => $e ) ) );
		WP_CLI::log( '  ' . $p->post_name . ': hours now read from TAUM Info' );
	}
}
WP_CLI::success( 'Office hours have one source.' );
