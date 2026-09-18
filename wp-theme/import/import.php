<?php
/**
 * Content importer. Run on the server with:
 *   wp eval-file import/import.php
 *
 * Idempotent: pages are matched by slug, everything else by title, so it can
 * be re-run safely. Images are copied into the Media Library once and reused.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Run via wp eval-file.' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$base     = __DIR__;
$manifest = json_decode( file_get_contents( $base . '/manifest.json' ), true );
$theme    = get_template_directory();
$theme_uri= get_template_directory_uri();
$admin    = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0]->ID;

/** Copy an image from the theme into the Media Library, once. */
function taum_import_media( $rel, $alt = '' ) {
	static $media_cache = array();
	$theme = get_template_directory();
	$rel = ltrim( str_replace( '{{THEME}}/assets/', '', $rel ), '/' );
	if ( 0 !== strpos( $rel, 'images/' ) && 'mural-photo.jpg' !== $rel ) {
		$rel = 'images/' . $rel;
	}
	if ( 'mural-photo.jpg' === $rel ) {
		$rel = 'images/mural-photo.jpg';
	}
	if ( isset( $media_cache[ $rel ] ) ) {
		return $media_cache[ $rel ];
	}
	$file = $theme . '/assets/' . $rel;
	$name = basename( $file );
	// Reuse an existing attachment with the same filename.
	$existing = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1, 'meta_query' => array( array( 'key' => '_taum_source', 'value' => $name ) ) ) );
	if ( $existing ) {
		return $media_cache[ $rel ] = $existing[0]->ID;
	}
	$tmp = wp_tempnam( $name );
	copy( $file, $tmp );
	$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "media: $name: " . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_taum_source', $name );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $media_cache[ $rel ] = $id;
}

/** Find a post by title within a type. */
function taum_find_by_title( $title, $type ) {
	$q = get_posts( array( 'post_type' => $type, 'title' => $title, 'posts_per_page' => 1, 'post_status' => 'any' ) );
	return $q ? $q[0]->ID : 0;
}

// ── Pages ──────────────────────────────────────────────────────────
$page_ids = array();
foreach ( $manifest['pages'] as $p ) {
	$content = '';
	if ( empty( $p['empty_body'] ) ) {
		$content = file_get_contents( $base . '/pages/' . $p['slug'] . '.html' );
		$content = str_replace( '{{THEME}}', $theme_uri, $content );
	}
	if ( ! empty( $p['gallery'] ) ) {
		$ids = array();
		$figs = '';
		foreach ( $p['gallery'] as $g ) {
			$id = taum_import_media( $g['src'], $g['alt'] );
			if ( ! $id ) { continue; }
			$ids[] = $id;
			$url = wp_get_attachment_image_url( $id, 'large' );
			$figs .= '<!-- wp:image {"id":' . $id . ',"sizeSlug":"large","linkDestination":"none"} -->'
				. '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $g['alt'] ) . '" class="wp-image-' . $id . '"/>'
				. '<figcaption class="wp-element-caption">' . esc_html( $g['caption'] ) . '</figcaption></figure>'
				. '<!-- /wp:image -->';
		}
		$gallery = '<!-- wp:gallery {"columns":3,"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-3 is-cropped">' . $figs . '</figure><!-- /wp:gallery -->';
		$content = str_replace( '{{GALLERY}}', $gallery, $content );
	}

	$existing = get_page_by_path( $p['slug'] );
	$data = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $p['title'],
		'post_name'    => $p['slug'],
		'post_content' => $content,
		'post_excerpt' => $p['excerpt'],
		'post_author'  => $admin,
		'comment_status' => 'closed',
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$id = wp_update_post( wp_slash( $data ) );
	} else {
		$id = wp_insert_post( wp_slash( $data ) );
	}
	if ( is_wp_error( $id ) || ! $id ) {
		WP_CLI::warning( 'page ' . $p['slug'] . ' failed' );
		continue;
	}
	update_post_meta( $id, 'taum_kicker', $p['kicker'] );
	update_post_meta( $id, 'taum_hero_style', $p['hero_style'] );
	if ( ! empty( $p['photo'] ) ) {
		$m = taum_import_media( $p['photo'], $p['photo_alt'] );
		if ( $m ) { set_post_thumbnail( $id, $m ); }
	}
	$page_ids[ $p['slug'] ] = $id;
	if ( ! empty( $p['front'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $id );
	}
	WP_CLI::log( sprintf( '  page  %-18s #%d', $p['slug'], $id ) );
}

// ── Events ─────────────────────────────────────────────────────────
foreach ( $manifest['events'] as $e ) {
	$id = taum_find_by_title( $e['title'], 'taum_event' );
	$data = array(
		'post_type'    => 'taum_event',
		'post_status'  => 'publish',
		'post_title'   => $e['title'],
		'post_content' => $e['content'],
		'post_author'  => $admin,
		'menu_order'   => isset( $e['order'] ) ? (int) $e['order'] : 10,
		'comment_status' => 'closed',
	);
	if ( $id ) { $data['ID'] = $id; $id = wp_update_post( wp_slash( $data ) ); } else { $id = wp_insert_post( wp_slash( $data ) ); }
	update_post_meta( $id, 'taum_event_recurring', isset( $e['recurring'] ) ? $e['recurring'] : '' );
	update_post_meta( $id, 'taum_event_day_label', isset( $e['day'] ) ? $e['day'] : '' );
	update_post_meta( $id, 'taum_event_date', isset( $e['date'] ) ? $e['date'] : '' );
	update_post_meta( $id, 'taum_event_time', isset( $e['time'] ) ? $e['time'] : '' );
	update_post_meta( $id, 'taum_event_place', isset( $e['place'] ) ? $e['place'] : '' );
	update_post_meta( $id, 'taum_event_url', isset( $e['url'] ) ? $e['url'] : '' );
	update_post_meta( $id, 'taum_event_url_label', isset( $e['url_label'] ) ? $e['url_label'] : '' );
	WP_CLI::log( sprintf( '  event %-30s #%d', $e['title'], $id ) );
}

// ── Partners ───────────────────────────────────────────────────────
taum_seed_tiers();
foreach ( $manifest['partners'] as $pt ) {
	$id = taum_find_by_title( $pt['title'], 'taum_partner' );
	$data = array( 'post_type' => 'taum_partner', 'post_status' => 'publish', 'post_title' => $pt['title'], 'post_author' => $admin );
	if ( $id ) { $data['ID'] = $id; $id = wp_update_post( wp_slash( $data ) ); } else { $id = wp_insert_post( wp_slash( $data ) ); }
	wp_set_object_terms( $id, $pt['tier'], 'taum_tier' );
	update_post_meta( $id, 'taum_partner_url', isset( $pt['url'] ) ? $pt['url'] : '' );
	WP_CLI::log( sprintf( '  partner %-40s #%d', $pt['title'], $id ) );
}

// ── Posts ──────────────────────────────────────────────────────────
// Remove the default "Hello world!" post if it is still there.
$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
if ( $hello ) { wp_delete_post( $hello->ID, true ); }
$sticky = array();
foreach ( $manifest['posts'] as $po ) {
	$id = taum_find_by_title( $po['title'], 'post' );
	$data = array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => $po['title'],
		'post_content' => $po['content'],
		'post_excerpt' => $po['excerpt'],
		'post_date'    => $po['date'],
		'post_author'  => $admin,
		'comment_status' => 'closed',
	);
	if ( $id ) { $data['ID'] = $id; $id = wp_update_post( wp_slash( $data ) ); } else { $id = wp_insert_post( wp_slash( $data ) ); }
	if ( ! empty( $po['photo'] ) ) {
		$m = taum_import_media( $po['photo'] );
		if ( $m ) { set_post_thumbnail( $id, $m ); }
	}
	if ( ! empty( $po['sticky'] ) ) { $sticky[] = $id; }
	WP_CLI::log( sprintf( '  post  %-40s #%d', $po['title'], $id ) );
}
update_option( 'sticky_posts', $sticky );

// ── Menu ───────────────────────────────────────────────────────────
$menu_name = 'Main';
$menu = wp_get_nav_menu_object( $menu_name );
$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );
// Rebuild items each run so order matches the manifest.
foreach ( wp_get_nav_menu_items( $menu_id ) ?: array() as $item ) {
	wp_delete_post( $item->ID, true );
}
$pos = 1;
foreach ( $manifest['menu'] as $mi ) {
	if ( empty( $page_ids[ $mi['slug'] ] ) ) { continue; }
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => $mi['label'],
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page_ids[ $mi['slug'] ],
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
		'menu-item-position'  => $pos++,
	) );
}
$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );
WP_CLI::log( "  menu  Main assigned to primary (" . ( $pos - 1 ) . " items)" );

// ── Sample page cleanup ────────────────────────────────────────────
$sample = get_page_by_path( 'sample-page' );
if ( $sample ) { wp_delete_post( $sample->ID, true ); }

flush_rewrite_rules( false );
WP_CLI::success( 'Import complete.' );
