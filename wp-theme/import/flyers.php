<?php
/**
 * Attach flyers to events and fill in locations the flyers confirmed.
 *   wp eval-file ../import/flyers.php
 * Idempotent: media is matched by filename, events by title.
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$dir   = dirname( __DIR__ ) . '/import/flyers';
$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0]->ID;

function taum_flyer_media( $file, $alt ) {
	$name = basename( $file );
	$have = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1,
		'meta_query' => array( array( 'key' => '_taum_source', 'value' => $name ) ) ) );
	if ( $have ) { return $have[0]->ID; }
	if ( ! file_exists( $file ) ) { WP_CLI::warning( "missing $name" ); return 0; }
	$tmp = wp_tempnam( $name );
	copy( $file, $tmp );
	$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) { WP_CLI::warning( "$name: " . $id->get_error_message() ); return 0; }
	update_post_meta( $id, '_taum_source', $name );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return $id;
}

/* ── Holiday Market: flyer, and the location it confirms ──────────────── */
$hm = get_posts( array( 'post_type' => 'taum_event', 'title' => 'TAUM Holiday Market', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( $hm ) {
	$id = taum_flyer_media( "$dir/flyer-holiday-market-2026.jpg", 'Flyer for the TAUM Holiday Market, Saturday November 14, 1 pm to 5 pm at 392 Second Street, Troy.' );
	if ( $id ) { set_post_thumbnail( $hm[0]->ID, $id ); }
	update_post_meta( $hm[0]->ID, 'taum_event_place', '392 2nd St., Troy (Osgood neighborhood)' );
	update_post_meta( $hm[0]->ID, 'taum_event_url_label', 'Apply to be a vendor' );
	wp_update_post( wp_slash( array( 'ID' => $hm[0]->ID, 'post_content' =>
		'A holiday market of local artists and makers, in our building. <strong>Vendors wanted.</strong> If you make something and want a table, apply below and we will send you the details.' ) ) );
	WP_CLI::log( '  Holiday Market: flyer attached, location set from the flyer' );
}

/* ── SOCKtober: a partner drive that has just ended. Recorded as a past
      event so it demonstrates the archive and keeps the record. ────────── */
$title = 'SOCKtober Sock Drive';
$found = get_posts( array( 'post_type' => 'taum_event', 'title' => $title, 'posts_per_page' => 1, 'post_status' => 'any' ) );
$data  = array(
	'post_type' => 'taum_event', 'post_status' => 'publish', 'post_title' => $title,
	'post_author' => $admin, 'menu_order' => 90, 'comment_status' => 'closed',
	'post_content' => 'A sock drive run by CEO and partners through the autumn, collecting new socks ahead of their free coats initiative. Drop-off was at CEO and partner sock box locations, 2328 5th Ave, Troy.',
);
if ( $found ) { $data['ID'] = $found[0]->ID; $eid = wp_update_post( wp_slash( $data ) ); }
else { $eid = wp_insert_post( wp_slash( $data ) ); }
update_post_meta( $eid, 'taum_event_recurring', '' );
update_post_meta( $eid, 'taum_event_date', '2026-10-01' );
update_post_meta( $eid, 'taum_event_time', 'August 1 to October 1' );
update_post_meta( $eid, 'taum_event_place', 'CEO sock box locations, 2328 5th Ave, Troy' );
update_post_meta( $eid, 'taum_event_url', '' );
update_post_meta( $eid, 'taum_event_url_label', '' );
$sid = taum_flyer_media( "$dir/flyer-socktober-sock-drive.jpg", 'Flyer for the SOCKtober sock drive, August 1 to October 1, collecting 1000 pairs of new socks.' );
if ( $sid ) { set_post_thumbnail( $eid, $sid ); }
WP_CLI::log( '  SOCKtober: recorded as a past event with its flyer' );

/* ── Standing flyers into the Media Library for Abby to place ─────────── */
foreach ( array(
	'flyer-fill-the-free-food-fridge.jpg' => 'Flyer inviting people to fill the TAUM Free Food Fridge at 392 Second Street, Troy.',
	'flyer-donate-furniture.jpg'          => 'Flyer about donating furniture to the TAUM Furniture Program.',
	'flyer-computer-rehab-donations.jpg'  => 'Flyer from GE Elfun Computer Rehab of Schenectady about donating used computer equipment.',
) as $f => $alt ) {
	$id = taum_flyer_media( "$dir/$f", $alt );
	WP_CLI::log( "  media   $f  #$id" );
}

WP_CLI::success( 'Flyers in. Next event spotlight will show the Holiday Market.' );
