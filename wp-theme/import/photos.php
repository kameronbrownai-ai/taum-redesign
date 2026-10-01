<?php
/**
 * Teen Tech Center photos. TAUM holds signed releases for everyone shown.
 * The whiteboard in the presenting shot was blurred before upload because it
 * carried a personal email address.
 *   wp eval-file ../import/photos.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$dir = dirname( __DIR__ ) . '/import/photos';

$shots = array(
	'teen-tech-center-mailing-project.jpg' => 'Teens at the Teen Tech Center working together around a table, folding and boxing a mailing.',
	'teen-tech-center-classroom.jpg'       => 'Teens at the Teen Tech Center reading through materials at tables in the community room.',
	'teen-tech-center-presenting.jpg'      => 'Three teens presenting to the room at the Teen Tech Center while the rest of the group listens.',
);

$ids = array();
foreach ( $shots as $file => $alt ) {
	$path = "$dir/$file";
	$have = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1,
		'meta_query' => array( array( 'key' => '_taum_source', 'value' => $file ) ) ) );
	if ( $have ) { $ids[ $file ] = $have[0]->ID; WP_CLI::log( "  have    $file  #{$have[0]->ID}" ); continue; }
	if ( ! file_exists( $path ) ) { WP_CLI::warning( "missing $file" ); continue; }
	$tmp = wp_tempnam( $file );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) { WP_CLI::warning( "$file: " . $id->get_error_message() ); continue; }
	update_post_meta( $id, '_taum_source', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	$ids[ $file ] = $id;
	WP_CLI::log( "  upload  $file  #$id" );
}

/* The summer recap post and the Tech for Teens page both ran stock imagery.
   Real photographs of the actual programme are better on every axis. */
$post = get_posts( array( 'post_type' => 'post', 'title' => 'Sixteen teens finished Tech for Teens this summer', 'posts_per_page' => 1 ) );
if ( $post && ! empty( $ids['teen-tech-center-mailing-project.jpg'] ) ) {
	set_post_thumbnail( $post[0]->ID, $ids['teen-tech-center-mailing-project.jpg'] );
	WP_CLI::log( '  summer recap post now uses a real programme photo' );
}
$tft = get_page_by_path( 'tech-for-teens' );
if ( $tft && ! empty( $ids['teen-tech-center-presenting.jpg'] ) ) {
	set_post_thumbnail( $tft->ID, $ids['teen-tech-center-presenting.jpg'] );
	WP_CLI::log( '  Tech for Teens hero now uses a real programme photo' );
}

/* Add all three to the gallery block, newest first, if not already there. */
$gal = get_page_by_path( 'gallery' );
if ( $gal ) {
	$content = $gal->post_content;
	$add = '';
	$caps = array(
		'teen-tech-center-mailing-project.jpg' => 'Teen Tech Center: a mailing, start to finish',
		'teen-tech-center-presenting.jpg'      => 'Presenting to the room',
		'teen-tech-center-classroom.jpg'       => 'Heads down at the Teen Tech Center',
	);
	foreach ( $caps as $file => $cap ) {
		if ( empty( $ids[ $file ] ) ) { continue; }
		$id = $ids[ $file ];
		if ( false !== strpos( $content, 'wp-image-' . $id . '"' ) ) { continue; }
		$url = wp_get_attachment_image_url( $id, 'large' );
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$add .= '<!-- wp:image {"id":' . $id . ',"sizeSlug":"large","linkDestination":"none"} -->'
			. '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $id . '"/>'
			. '<figcaption class="wp-element-caption">' . esc_html( $cap ) . '</figcaption></figure>'
			. '<!-- /wp:image -->';
	}
	if ( $add ) {
		// Drop them in at the front of the existing gallery block.
		$needle = 'is-cropped">';
		$pos = strpos( $content, $needle );
		if ( false !== $pos ) {
			$content = substr_replace( $content, $needle . $add, $pos, strlen( $needle ) );
			wp_update_post( wp_slash( array( 'ID' => $gal->ID, 'post_content' => $content ) ) );
			WP_CLI::log( '  gallery: three photos added at the front' );
		} else {
			WP_CLI::warning( '  gallery block not found, photos are in the Library but not placed' );
		}
	} else {
		WP_CLI::log( '  gallery already has them' );
	}
}

WP_CLI::success( 'Teen Tech Center photos in.' );
