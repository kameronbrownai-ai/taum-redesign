<?php
/**
 * Abby's photographs replace the stock imagery, and the gallery is rebuilt
 * from them. Stock pictures of strangers were always a placeholder; these are
 * the actual building, garden, crew and neighbours.
 *   wp eval-file ../import/real-photos.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$dir = dirname( __DIR__ ) . '/import/real-photos';

/** slug => alt text. Written to describe what is in frame, for screen readers. */
$alts = array(
	'free-food-fridge-in-use'      => 'A neighbour stocking the Free Food Fridge outside TAUM while a child looks into the lower shelf.',
	'free-food-fridge-blessing'    => 'A group of neighbours and clergy gathered outside the Free Food Fridge on Second Street.',
	'regional-food-bank-delivery'  => 'A Regional Food Bank "Meals in Motion" truck parked outside TAUM on Second Street.',
	'taum-mural-second-street'     => 'The bird mural on the TAUM building, birds carrying branches across a bright blue wall.',
	'taum-welcome-sign'            => 'A hand-drawn welcome sign reading Troy Area United Ministries, 392 2nd Street, Troy NY.',
	'carrying-a-food-box'          => 'A volunteer in a red coat carrying a large box of food.',
	'community-meal-tables'        => 'Neighbours of all ages eating together at long tables in the TAUM community room.',
	'grocery-bags-decorated'       => 'Rows of paper grocery bags decorated with hand-drawn hearts, ready to go out.',
	'kitchen-volunteer'            => 'A volunteer in an apron working in the TAUM kitchen.',
	'building-maintenance-ladder'  => 'A volunteer on a ladder with a paint bucket, working on the brick exterior of the building.',
	'neighbors-sharing-a-meal'     => 'Neighbours sitting together over plates of food at a community meal.',
	'volunteer-crew-with-shovels'  => 'Five volunteers with shovels and a wheelbarrow, smiling at the camera on a work day.',
	'garden-build-group'           => 'The volunteer crew gathered in the finished community garden beside the brick wall.',
	'unloading-the-food-truck'     => 'Volunteers unloading crates from a delivery truck onto a hand cart on the street.',
	'sorting-food-donations'       => 'Volunteers sorting boxes of donated food in the community room.',
	'grocery-distribution-tables'  => 'Tables laid out with crates of milk, bread and produce ready for the grocery distribution.',
	'packing-grocery-bags'         => 'Volunteers filling paper grocery bags at long tables.',
	'crop-walk-group'              => 'A large group of walkers with handmade signs gathered for the CROP Hunger Walk.',
	'free-food-fridge-signs'       => 'The Free Food Fridge outside TAUM, marked Free Food and Comida Gratis and decorated with painted handprints.',
	'stocking-the-fridge'          => 'A volunteer restocking the Free Food Fridge with fresh produce and prepared meals.',
	'preserves-from-the-garden'    => 'Jars of sauce and preserves made from the TAUM garden harvest.',
	'garden-beds-in-season'        => 'The TAUM community garden in full growth, raised beds thick with vegetables.',
	'teen-tech-center-working'     => 'Teenagers working together around a table at the Teen Tech Center.',
	'veggie-mobile-visit'          => 'The Veggie Mobile truck parked on Second Street beside the TAUM mural.',
	'community-room-teens'         => 'Teenagers spread out across the community room, some at tables and some on couches.',
	'teen-tech-classroom'          => 'A class in progress in the community room, participants at laptops facing a projected screen.',
	'sharing-the-harvest'          => 'Neighbours handing round squash and vegetables from the garden.',
	'cabbage-from-the-garden'      => 'A gardener holding up a large cabbage leaf grown in the TAUM garden.',
	'furniture-program-crew'       => 'The volunteer crew lined up in front of the TAUM Furniture Program truck.',
	'building-a-raised-bed'        => 'Two volunteers assembling a metal raised garden bed on the lawn.',
	'new-raised-beds'              => 'Newly built raised beds filled with soil in the garden behind the building.',
	'teens-in-the-community-room'  => 'Teenagers at tables in the community room during a Teen Tech Center session.',
	'harvesting-eggplant'          => 'A gardener reaching into the leaves to pick a ripe purple eggplant.',
	'pepper-from-the-garden'       => 'A gardener holding out a green pepper just picked from the bed.',
	'cherry-tomatoes-in-hand'      => 'A handful of ripening cherry tomatoes held up in the garden.',
	'raised-bed-greens'            => 'A raised bed of chard and greens growing behind the TAUM building.',
);

$ids = array();
foreach ( $alts as $slug => $alt ) {
	$file = "$slug.jpg";
	$have = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1,
		'meta_query' => array( array( 'key' => '_taum_source', 'value' => $file ) ) ) );
	if ( $have ) { $ids[ $slug ] = $have[0]->ID; continue; }
	$path = "$dir/$file";
	if ( ! file_exists( $path ) ) { WP_CLI::warning( "missing $file" ); continue; }
	$tmp = wp_tempnam( $file );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) { WP_CLI::warning( "$file: " . $id->get_error_message() ); continue; }
	update_post_meta( $id, '_taum_source', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	wp_update_post( array( 'ID' => $id, 'post_title' => ucfirst( str_replace( '-', ' ', $slug ) ) ) );
	$ids[ $slug ] = $id;
}
WP_CLI::log( '  uploaded/found: ' . count( $ids ) . ' photographs' );

/* ── Page heroes: the photo that best matches what the page is about ─── */
$heroes = array(
	'food'             => 'free-food-fridge-signs',
	'furniture'        => 'furniture-program-crew',
	'get-involved'     => 'volunteer-crew-with-shovels',
	'about'            => 'free-food-fridge-blessing',
	'donate'           => 'packing-grocery-bags',
	'damien-center'    => 'neighbors-sharing-a-meal',
	'community-center' => 'community-room-teens',
	'programs'         => 'taum-welcome-sign',
	'news'             => 'veggie-mobile-visit',
	'gallery'          => 'crop-walk-group',
	'contact'          => 'taum-mural-second-street',
);
foreach ( $heroes as $slug => $photo ) {
	$pg = get_page_by_path( $slug );
	if ( $pg && ! empty( $ids[ $photo ] ) ) {
		set_post_thumbnail( $pg->ID, $ids[ $photo ] );
		WP_CLI::log( sprintf( '  hero    %-18s %s', $slug, $photo ) );
	}
}

/* ── Inline stock images inside page content ─────────────────────────── */
$swaps = array(
	'images/meal-share.jpg'            => 'community-meal-tables',
	'images/garden-harvest.jpg'        => 'garden-beds-in-season',
	'images/serving-soup.jpg'          => 'kitchen-volunteer',
	'images/food-box.jpg'              => 'carrying-a-food-box',
	'images/group-grass.jpg'           => 'garden-build-group',
	'images/hands-unity.jpg'           => 'free-food-fridge-blessing',
	'images/volunteers-group.jpg'      => 'furniture-program-crew',
	'images/community-planting.jpg'    => 'building-a-raised-bed',
	'images/family-tomatoes.jpg'       => 'cherry-tomatoes-in-hand',
	'images/supportive-hands.jpg'      => 'neighbors-sharing-a-meal',
	'images/teens-laptops.jpg'         => 'teen-tech-center-working',
	'images/tech-for-teens.jpg'        => 'teen-tech-classroom',
	'images/food-donations.jpg'        => 'sorting-food-donations',
	'images/furniture-fresh-start.jpg' => 'furniture-program-crew',
	'images/friends-park.jpg'          => 'community-room-teens',
	'images/student-reading.jpg'       => 'teen-tech-center-working',
	'images/graduates.jpg'             => 'crop-walk-group',
	'images/campus-reflection.jpg'     => 'community-room-teens',
	'images/food-donations.jpg'        => 'grocery-distribution-tables',
);
$changed = 0;
foreach ( get_posts( array( 'post_type' => array( 'page', 'post' ), 'posts_per_page' => -1, 'post_status' => 'publish' ) ) as $p ) {
	$c = $p->post_content;
	foreach ( $swaps as $old => $slug ) {
		if ( empty( $ids[ $slug ] ) || false === strpos( $c, $old ) ) { continue; }
		$url = wp_get_attachment_image_url( $ids[ $slug ], 'large' );
		$alt = get_post_meta( $ids[ $slug ], '_wp_attachment_image_alt', true );
		// Replace the whole <img> so the alt text matches the new picture.
		$c = preg_replace(
			'#<img[^>]*src="[^"]*' . preg_quote( basename( $old ), '#' ) . '"[^>]*>#',
			'<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">',
			$c
		);
	}
	if ( $c !== $p->post_content ) {
		wp_update_post( wp_slash( array( 'ID' => $p->ID, 'post_content' => $c ) ) );
		WP_CLI::log( '  inline  ' . $p->post_name );
		$changed++;
	}
}
WP_CLI::log( "  $changed page(s) had inline images replaced" );

/* ── Rebuild the gallery from these photographs ──────────────────────── */
$gallery_order = array(
	'taum-mural-second-street'    => 'Our home and mural on Second Street',
	'free-food-fridge-signs'      => 'The Free Food Fridge, open every hour',
	'community-meal-tables'       => "Everyone's welcome at the table",
	'crop-walk-group'             => 'The CROP Hunger Walk',
	'furniture-program-crew'      => 'The furniture crew',
	'garden-beds-in-season'       => 'The garden in full season',
	'free-food-fridge-in-use'     => 'Take what you need',
	'volunteer-crew-with-shovels' => 'Strong backs, big hearts',
	'grocery-distribution-tables' => 'Thursday groceries, ready to go',
	'harvesting-eggplant'         => 'Picked this morning',
	'teen-tech-center-working'    => 'Teen Tech Center in action',
	'garden-build-group'          => 'The day we built the garden',
	'kitchen-volunteer'           => 'Where Monday dinner comes from',
	'grocery-bags-decorated'      => 'Bags packed by hand',
	'veggie-mobile-visit'         => 'The Veggie Mobile comes to Second Street',
	'cabbage-from-the-garden'     => 'Grown out the back',
	'regional-food-bank-delivery' => 'Meals in motion',
	'community-room-teens'        => 'The community room on a weekday',
	'preserves-from-the-garden'   => 'Put up for the winter',
	'free-food-fridge-blessing'   => 'Blessing the fridge',
	'building-maintenance-ladder' => 'An old building needs handy people',
	'cherry-tomatoes-in-hand'     => 'Tomatoes from the beds',
	'sorting-food-donations'      => 'Sorting what comes in',
	'new-raised-beds'             => 'New beds, ready for spring',
	'teen-tech-classroom'         => 'Real skills, real paychecks',
	'pepper-from-the-garden'      => 'Straight from the bed',
	'unloading-the-food-truck'    => 'Delivery day',
	'taum-welcome-sign'           => 'Welcome to 392 Second Street',
);
$gal = get_page_by_path( 'gallery' );
if ( $gal ) {
	$figs = '';
	$n = 0;
	foreach ( $gallery_order as $slug => $cap ) {
		if ( empty( $ids[ $slug ] ) ) { continue; }
		$id  = $ids[ $slug ];
		$url = wp_get_attachment_image_url( $id, 'large' );
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$figs .= '<!-- wp:image {"id":' . $id . ',"sizeSlug":"large","linkDestination":"none"} -->'
			. '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $id . '"/>'
			. '<figcaption class="wp-element-caption">' . esc_html( $cap ) . '</figcaption></figure>'
			. '<!-- /wp:image -->';
		$n++;
	}
	$block = '<!-- wp:gallery {"columns":3,"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-3">' . $figs . '</figure><!-- /wp:gallery -->';
	$c = $gal->post_content;
	// Swap the existing gallery block wholesale.
	$c2 = preg_replace( '#<!-- wp:gallery.*<!-- /wp:gallery -->#s', $block, $c );
	if ( $c2 === $c ) { $c2 = $block . "\n\n" . $c; }
	wp_update_post( wp_slash( array( 'ID' => $gal->ID, 'post_content' => $c2 ) ) );
	WP_CLI::log( "  gallery rebuilt with $n photographs" );
}

WP_CLI::success( 'Real photographs are in.' );
