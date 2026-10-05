<?php
/**
 * Hardening.
 *
 * This is a small nonprofit site with two accounts and no e-commerce, so the
 * realistic threats are automated: credential stuffing against wp-login,
 * XML-RPC amplification, and bots harvesting usernames to feed both. Each
 * measure below closes one of those without costing Abby anything.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * XML-RPC. Nothing here uses it: no Jetpack, no mobile app publishing, no
 * pingbacks worth having. Left on, system.multicall lets an attacker try
 * hundreds of passwords in a single request, and pingback.ping turns the site
 * into a reflector for attacks on others.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', function ( $methods ) {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
} );
// xmlrpc_enabled only covers methods that authenticate. system.listMethods and
// system.multicall answer regardless, so refuse the request outright.
add_action( 'init', function () {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		status_header( 403 );
		exit( 'XML-RPC is disabled on this site.' );
	}
}, 0 );
add_filter( 'wp_headers', function ( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
} );

/**
 * Username harvesting. /wp-json/wp/v2/users listed both accounts by name and
 * slug, and ?author=1 redirected to the admin's slug. Knowing a username is
 * half of a brute-force attempt, so neither is worth handing out.
 */
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );
// parse_request, not template_redirect: WordPress's own canonical redirect
// turns ?author=1 into /author/<slug>/ before template_redirect ever fires,
// which is exactly the slug we are trying not to publish.
add_action( 'parse_request', function ( $wp ) {
	if ( ! is_user_logged_in() && ( isset( $_GET['author'] ) || isset( $wp->query_vars['author'] ) || isset( $wp->query_vars['author_name'] ) ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
// Author archives have no purpose on this site and only expose the same names.
add_action( 'template_redirect', function () {
	if ( is_author() && ! is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );

/**
 * Login errors. WordPress says "unknown username" versus "incorrect password",
 * which confirms to a bot when it has found a real account.
 */
add_filter( 'login_errors', function () {
	return __( 'Those details did not work. Please try again.', 'taum' );
} );

/**
 * Version strings in feeds and on the login page. Minor, but they tell a
 * scanner exactly which exploits to try.
 */
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Security headers, set from PHP so they apply wherever the site is hosted
 * rather than depending on a control panel's .htaccess surviving a migration.
 */
add_action( 'send_headers', function () {
	if ( is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()' );
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}
} );
