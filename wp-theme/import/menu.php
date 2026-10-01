<?php
/**
 * Rebuild the main menu with dropdowns.
 *   wp eval-file ../import/menu.php
 * Rebuilds from scratch each run so order and nesting always match this file.
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$menu_name = 'Main';
$menu = wp_get_nav_menu_object( $menu_name );
$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );
foreach ( wp_get_nav_menu_items( $menu_id ) ?: array() as $item ) {
	wp_delete_post( $item->ID, true );
}

function taum_mi( $menu_id, $label, $target, $pos, $parent = 0 ) {
	$args = array(
		'menu-item-title'  => $label,
		'menu-item-status' => 'publish',
		'menu-item-position' => $pos,
		'menu-item-parent-id' => $parent,
	);
	if ( 0 === strpos( $target, 'http' ) || 0 === strpos( $target, '/' ) || 0 === strpos( $target, '#' ) ) {
		$args['menu-item-type'] = 'custom';
		$args['menu-item-url']  = ( 0 === strpos( $target, 'http' ) ) ? $target : home_url( $target );
	} else {
		$pg = get_page_by_path( $target );
		if ( ! $pg ) { WP_CLI::warning( "missing page: $target" ); return 0; }
		$args['menu-item-type']      = 'post_type';
		$args['menu-item-object']    = 'page';
		$args['menu-item-object-id'] = $pg->ID;
	}
	return wp_update_nav_menu_item( $menu_id, 0, $args );
}

$pos = 1;

// Home, with the homepage's own sections under it. The front page is long and
// these are the places people are actually trying to reach.
$home = taum_mi( $menu_id, 'Home', '/', $pos++ );
taum_mi( $menu_id, 'Next up',            '/#next-up',  $pos++, $home );
taum_mi( $menu_id, "What's growing",     '/#now',      $pos++, $home );
taum_mi( $menu_id, 'Our 40th year',      '/#campaign', $pos++, $home );
taum_mi( $menu_id, 'Impact',             '/#impact',   $pos++, $home );
taum_mi( $menu_id, 'What we do',         '/#programs', $pos++, $home );
taum_mi( $menu_id, 'Use the building',   '/#involved', $pos++, $home );

// Flat items
taum_mi( $menu_id, 'Food', 'food', $pos++ );
taum_mi( $menu_id, 'Furniture', 'furniture', $pos++ );

// Programs, with every program page under it
$programs = taum_mi( $menu_id, 'Programs', 'programs', $pos++ );
foreach ( array(
	'Food'              => 'food',
	'Furniture'         => 'furniture',
	'Tech for Teens'    => 'tech-for-teens',
	'Community Center'  => 'community-center',
	'Damien Center'     => 'damien-center',
	'Campus Chaplaincy' => 'chaplaincy',
	'MLK Scholarship'   => 'mlk-scholarship',
) as $label => $slug ) {
	taum_mi( $menu_id, $label, $slug, $pos++, $programs );
}

// Events & News
$events = taum_mi( $menu_id, 'Events', 'news', $pos++ );
taum_mi( $menu_id, 'Upcoming events', '/news/#events', $pos++, $events );
taum_mi( $menu_id, 'News',            '/news/#news',   $pos++, $events );

// Get involved
$inv = taum_mi( $menu_id, 'Get involved', 'get-involved', $pos++ );
taum_mi( $menu_id, 'Volunteer', taum_opt( 'url_volunteer' ), $pos++, $inv );
taum_mi( $menu_id, 'Use the building', 'community-center', $pos++, $inv );
taum_mi( $menu_id, 'Donate', 'donate', $pos++, $inv );

// About
$about = taum_mi( $menu_id, 'About', 'about', $pos++ );
taum_mi( $menu_id, 'Our story & staff', 'about', $pos++, $about );
taum_mi( $menu_id, 'Partners & supporters', '/about/#partners', $pos++, $about );

// Gallery stays top level; the flyer archive hangs off it.
$gal = taum_mi( $menu_id, 'Gallery', 'gallery', $pos++ );
taum_mi( $menu_id, 'Photos', 'gallery', $pos++, $gal );
taum_mi( $menu_id, 'Event flyers', '/gallery/#flyers', $pos++, $gal );

taum_mi( $menu_id, 'FAQ', 'faq', $pos++ );
taum_mi( $menu_id, 'Contact', 'contact', $pos++ );

$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

$count = count( wp_get_nav_menu_items( $menu_id ) ?: array() );
WP_CLI::success( "Menu rebuilt: $count items, dropdowns on Programs, Events & News, Get involved, About." );
