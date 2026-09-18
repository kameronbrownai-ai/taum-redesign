<?php
/**
 * Theme supports, menus, and assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'taum', TAUM_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	// Pages use the excerpt as the hero lede.
	add_post_type_support( 'page', 'excerpt' );

	register_nav_menus( array(
		'primary' => __( 'Main navigation', 'taum' ),
	) );

	// Hero photos are cropped 4:3 on inner pages; keep a generous source.
	set_post_thumbnail_size( 1600, 1200, false );
	add_image_size( 'taum-hero', 1600, 1200, false );
	add_image_size( 'taum-card', 1100, 733, true );
} );

/**
 * Front-end assets. The stylesheet carries the whole design; the version
 * string is the theme version so browsers refetch on every release.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'taum-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,640;9..144,700&family=Nunito+Sans:wght@400;600;700;800;900&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'taum', TAUM_URI . '/assets/css/styles.css', array( 'taum-fonts' ), TAUM_VERSION );
	wp_enqueue_style( 'taum-wp', TAUM_URI . '/assets/css/wp.css', array( 'taum' ), TAUM_VERSION );

	wp_enqueue_script( 'taum-concierge', TAUM_URI . '/assets/js/concierge.js', array(), TAUM_VERSION, array( 'strategy' => 'defer' ) );

	if ( is_front_page() ) {
		wp_enqueue_script( 'taum-home', TAUM_URI . '/assets/js/home.js', array(), TAUM_VERSION, array( 'strategy' => 'defer' ) );
	}
} );

/**
 * The block editor gets the design stylesheet too, so Abby sees cards and
 * callouts styled while she edits rather than a wall of unstyled text.
 */
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_style( 'taum-editor-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Nunito+Sans:wght@400;700;800&display=swap', array(), null );
} );

/**
 * Keep the front end lean: no emoji script, no embed script, no jQuery
 * migrate, no global styles we do not use.
 */
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
} );

add_filter( 'body_class', function ( $classes ) {
	if ( is_page() ) {
		$classes[] = 'page-' . get_post_field( 'post_name', get_queried_object_id() );
	}
	return $classes;
} );

/**
 * Let editors see the Customizer "TAUM Info" panel, because that is where
 * meal times and phone numbers live and Abby must be able to change them.
 */
add_action( 'admin_menu', function () {
	if ( current_user_can( 'edit_pages' ) && ! current_user_can( 'edit_theme_options' ) ) {
		add_menu_page(
			__( 'Site Info', 'taum' ),
			__( 'Site Info', 'taum' ),
			'edit_pages',
			'customize.php?autofocus[panel]=taum_info',
			'',
			'dashicons-info',
			3
		);
	}
} );

add_filter( 'user_has_cap', function ( $allcaps, $caps, $args ) {
	// Editors may open the Customizer so they can reach TAUM Info. WordPress
	// maps 'customize' onto 'edit_theme_options', so grant that, but only for
	// this one check: a direct 'edit_theme_options' check (menus, theme
	// editor) still fails for them.
	if ( isset( $args[0] ) && 'customize' === $args[0] && ! empty( $allcaps['edit_pages'] ) ) {
		$allcaps['edit_theme_options'] = true;
	}
	return $allcaps;
}, 10, 3 );
