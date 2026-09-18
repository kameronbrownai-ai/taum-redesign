<?php
/**
 * Search and AI-search plumbing: meta description, Open Graph, schema.org,
 * llms.txt, and robots.txt. Kept here so it survives content edits.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta description and OG tags. The excerpt doubles as the description on
 * pages; posts fall back to a trimmed body.
 */
add_action( 'wp_head', function () {
	$desc = '';
	if ( is_front_page() ) {
		$desc = 'TAUM is a neighborhood center at ' . taum_opt( 'address1' ) . ' in Troy, NY offering free community meals, groceries, a Free Food Fridge, free furniture, tech job training for teens, and space for neighbors to gather. Open to everyone.';
	} elseif ( is_singular() ) {
		$desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ), 30, '…' );
	}
	$desc = trim( wp_strip_all_tags( $desc ) );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_front_page() ? 'website' : 'article' ) . '">' . "\n";
	if ( is_singular() && has_post_thumbnail() ) {
		echo '<meta property="og:image" content="' . esc_url( get_the_post_thumbnail_url( null, 'large' ) ) . '">' . "\n";
	} elseif ( is_front_page() ) {
		echo '<meta property="og:image" content="' . esc_url( TAUM_URI . '/assets/images/mural-photo.jpg' ) . '">' . "\n";
	}
}, 1 );

/**
 * schema.org. Organization on the homepage, FAQPage on the FAQ page (built
 * from the accordion so it never drifts from the visible answers), and
 * Event schedules on the news page for the weekly meals.
 */
add_action( 'wp_head', function () {
	$graph = array();

	if ( is_front_page() ) {
		$graph[] = array(
			'@type'         => 'NonprofitOrganization',
			'name'          => 'Troy Area United Ministries',
			'alternateName' => 'TAUM',
			'url'           => home_url( '/' ),
			'foundingDate'  => '1986',
			'description'   => wp_strip_all_tags( taum_opt( 'hero_lede' ) ),
			'address'       => taum_schema_address(),
			'telephone'     => '+1-' . preg_replace( '/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', taum_opt( 'phone_raw' ) ),
			'email'         => taum_opt( 'email_office' ),
			'areaServed'    => 'Rensselaer County, NY',
			'sameAs'        => array_filter( array( taum_opt( 'facebook' ), taum_opt( 'instagram' ) ) ),
			'knowsAbout'    => array( 'free food Troy NY', 'food pantry', 'free furniture', 'community fridge', 'youth job training', 'MLK scholarship' ),
		);
	}

	if ( is_page( 'faq' ) ) {
		$content = get_post_field( 'post_content', get_the_ID() );
		$content = do_shortcode( $content );
		if ( preg_match_all( '#<summary>(.*?)</summary>\s*<div class="a">(.*?)</div>#s', $content, $m, PREG_SET_ORDER ) ) {
			$qs = array();
			foreach ( $m as $pair ) {
				$qs[] = array(
					'@type'          => 'Question',
					'name'           => trim( wp_strip_all_tags( $pair[1] ) ),
					'acceptedAnswer' => array( '@type' => 'Answer', 'text' => trim( wp_strip_all_tags( $pair[2] ) ) ),
				);
			}
			if ( $qs ) {
				$graph[] = array( '@type' => 'FAQPage', 'mainEntity' => $qs );
			}
		}
	}

	if ( is_page( 'news' ) ) {
		$days = array( 'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday' );
		foreach ( taum_get_events( 'weekly' ) as $ev ) {
			$day = get_post_meta( $ev->ID, 'taum_event_day_label', true );
			if ( ! isset( $days[ $day ] ) ) {
				continue;
			}
			$graph[] = array(
				'@type'               => 'Event',
				'name'                => get_the_title( $ev ) . ' at TAUM',
				'description'         => wp_strip_all_tags( $ev->post_content ),
				'eventSchedule'       => array( '@type' => 'Schedule', 'byDay' => 'https://schema.org/' . $days[ $day ], 'repeatFrequency' => 'P1W' ),
				'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
				'isAccessibleForFree' => true,
				'location'            => array( '@type' => 'Place', 'name' => 'Troy Area United Ministries', 'address' => taum_schema_address() ),
				'organizer'           => array( '@type' => 'NonprofitOrganization', 'name' => 'Troy Area United Ministries', 'url' => home_url( '/' ) ),
			);
		}
	}

	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}, 5 );

function taum_schema_address() {
	$parts = array_map( 'trim', explode( ',', taum_opt( 'address2' ) ) );
	$state_zip = isset( $parts[1] ) ? preg_split( '/\s+/', trim( $parts[1] ) ) : array( 'NY', '' );
	return array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => taum_opt( 'address1' ),
		'addressLocality' => $parts[0],
		'addressRegion'   => $state_zip[0],
		'postalCode'      => isset( $state_zip[1] ) ? $state_zip[1] : '',
		'addressCountry'  => 'US',
	);
}

/**
 * /llms.txt for AI crawlers. Served from the theme so it deploys with it.
 */
add_action( 'init', function () {
	add_rewrite_rule( '^llms\.txt$', 'index.php?taum_llms=1', 'top' );
} );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'taum_llms';
	return $vars;
} );
// Do not force a trailing slash onto /llms.txt.
add_filter( 'redirect_canonical', function ( $redirect, $requested ) {
	return false !== strpos( $requested, '/llms.txt' ) ? false : $redirect;
}, 10, 2 );
add_action( 'template_redirect', function () {
	if ( get_query_var( 'taum_llms' ) ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		$file = TAUM_DIR . '/llms.txt';
		echo file_exists( $file ) ? str_replace( 'https://taum.org', untrailingslashit( home_url() ), file_get_contents( $file ) ) : '';
		exit;
	}
} );

/**
 * robots.txt: explicitly welcome the AI crawlers.
 */
add_filter( 'robots_txt', function ( $output ) {
	foreach ( array( 'GPTBot', 'ClaudeBot', 'Claude-Web', 'PerplexityBot', 'Google-Extended' ) as $bot ) {
		$output .= "\nUser-agent: $bot\nAllow: /\n";
	}
	return $output;
}, 20 );
