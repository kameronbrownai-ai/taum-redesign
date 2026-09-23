<?php
/**
 * TAUM theme bootstrap.
 *
 * Everything is split into inc/ so each concern is easy to find:
 *   setup.php          theme supports, menus, assets, editor styles
 *   cpt.php            Event and Partner post types and their meta
 *   meta-boxes.php     the small edit screens for hero, event, partner fields
 *   customizer.php     the "TAUM Info" panel (phone, meal times, links, stats)
 *   template-tags.php  helpers used by the templates
 *   shortcodes.php     [taum key] so page copy can reference site-wide facts
 *   seo.php            meta description, schema.org, llms.txt, robots
 *   help.php           "How to update the site" page and dashboard box for editors
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TAUM_VERSION', '1.0.3' );
define( 'TAUM_DIR', get_template_directory() );
define( 'TAUM_URI', get_template_directory_uri() );

foreach ( array( 'setup', 'cpt', 'meta-boxes', 'customizer', 'template-tags', 'shortcodes', 'seo', 'help' ) as $taum_inc ) {
	require_once TAUM_DIR . '/inc/' . $taum_inc . '.php';
}
