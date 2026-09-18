<?php
/**
 * Custom post types: Event and Partner.
 *
 * News uses the built-in Post type. These two exist because Abby adds
 * events and sponsors regularly and they need structured fields, not prose.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {

	register_post_type( 'taum_event', array(
		'labels' => array(
			'name'               => __( 'Events', 'taum' ),
			'singular_name'      => __( 'Event', 'taum' ),
			'add_new'            => __( 'Add event', 'taum' ),
			'add_new_item'       => __( 'Add new event', 'taum' ),
			'edit_item'          => __( 'Edit event', 'taum' ),
			'new_item'           => __( 'New event', 'taum' ),
			'view_item'          => __( 'View event', 'taum' ),
			'search_items'       => __( 'Search events', 'taum' ),
			'not_found'          => __( 'No events yet.', 'taum' ),
			'not_found_in_trash' => __( 'No events in the trash.', 'taum' ),
			'menu_name'          => __( 'Events', 'taum' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'events', 'with_front' => false ),
		'menu_icon'    => 'dashicons-calendar-alt',
		'menu_position' => 5,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'show_in_rest' => true,
	) );

	register_post_type( 'taum_partner', array(
		'labels' => array(
			'name'               => __( 'Partners & Sponsors', 'taum' ),
			'singular_name'      => __( 'Partner', 'taum' ),
			'add_new'            => __( 'Add partner', 'taum' ),
			'add_new_item'       => __( 'Add new partner or sponsor', 'taum' ),
			'edit_item'          => __( 'Edit partner', 'taum' ),
			'new_item'           => __( 'New partner', 'taum' ),
			'search_items'       => __( 'Search partners', 'taum' ),
			'not_found'          => __( 'No partners yet.', 'taum' ),
			'not_found_in_trash' => __( 'No partners in the trash.', 'taum' ),
			'menu_name'          => __( 'Partners', 'taum' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'menu_icon'           => 'dashicons-groups',
		'menu_position'       => 6,
		'supports'            => array( 'title' ),
		'show_in_rest'        => true,
	) );

	// Partner tiers. Program partners vs sponsors, and sponsor levels.
	register_taxonomy( 'taum_tier', 'taum_partner', array(
		'labels' => array(
			'name'          => __( 'Tiers', 'taum' ),
			'singular_name' => __( 'Tier', 'taum' ),
			'menu_name'     => __( 'Tiers', 'taum' ),
		),
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );

	// Event meta, registered so it is available to the block editor and REST.
	$event_meta = array(
		'taum_event_date'      => 'string', // YYYY-MM-DD
		'taum_event_time'      => 'string', // free text, e.g. "5:45 pm"
		'taum_event_place'     => 'string',
		'taum_event_url'       => 'string', // registration / details link
		'taum_event_url_label' => 'string',
		'taum_event_recurring' => 'string', // "" or "weekly"
		'taum_event_day_label' => 'string', // for recurring, e.g. "Mon"
	);
	foreach ( $event_meta as $key => $type ) {
		register_post_meta( 'taum_event', $key, array(
			'type'          => $type,
			'single'        => true,
			'show_in_rest'  => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
		) );
	}

	register_post_meta( 'taum_partner', 'taum_partner_url', array(
		'type' => 'string', 'single' => true, 'show_in_rest' => true,
		'sanitize_callback' => 'esc_url_raw',
		'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
	) );

	// Page hero fields.
	register_post_meta( 'page', 'taum_kicker', array(
		'type' => 'string', 'single' => true, 'show_in_rest' => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
	register_post_meta( 'page', 'taum_hero_style', array(
		'type' => 'string', 'single' => true, 'show_in_rest' => true,
		'sanitize_callback' => 'sanitize_key',
		'auth_callback' => function () { return current_user_can( 'edit_pages' ); },
	) );
} );

/**
 * Seed the tier terms once so the dropdown is never empty for Abby.
 */
add_action( 'after_switch_theme', 'taum_seed_tiers' );
function taum_seed_tiers() {
	foreach ( array( 'Program partners', 'Sponsors & funders' ) as $name ) {
		if ( ! term_exists( $name, 'taum_tier' ) ) {
			wp_insert_term( $name, 'taum_tier' );
		}
	}
}

/**
 * Admin list columns so the Events screen is scannable.
 */
add_filter( 'manage_taum_event_posts_columns', function ( $cols ) {
	$new = array();
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['taum_when'] = __( 'When', 'taum' );
		}
	}
	unset( $new['date'] );
	return $new;
} );
add_action( 'manage_taum_event_posts_custom_column', function ( $col, $post_id ) {
	if ( 'taum_when' !== $col ) {
		return;
	}
	if ( get_post_meta( $post_id, 'taum_event_recurring', true ) ) {
		echo esc_html( sprintf( 'Every %s · %s', get_post_meta( $post_id, 'taum_event_day_label', true ), get_post_meta( $post_id, 'taum_event_time', true ) ) );
	} else {
		$d = get_post_meta( $post_id, 'taum_event_date', true );
		echo esc_html( trim( ( $d ? date_i18n( 'M j, Y', strtotime( $d ) ) : '' ) . ' ' . get_post_meta( $post_id, 'taum_event_time', true ) ) );
	}
}, 10, 2 );
