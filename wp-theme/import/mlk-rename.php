<?php
/**
 * Broaden the MLK page beyond the scholarship.
 *
 * Abby: "it's not just the scholarships, it's all the MLK programming for the
 * county." The page is renamed and reframed to hold that, the scholarship
 * becomes a section within it rather than the whole thing, and the slug moves
 * to /mlk/ with the old address redirected. No programming is invented here:
 * her materials have not arrived, so the page says plainly that the committee
 * does more and gives people a way to ask.
 *
 *   wp eval-file ../import/mlk-rename.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$pg = get_page_by_path( 'mlk-scholarship' );
if ( ! $pg ) {
	$pg = get_page_by_path( 'mlk' );
	if ( ! $pg ) { WP_CLI::error( 'MLK page not found' ); }
}

$content = $pg->post_content;

// The scholarship keeps everything it had, under its own heading.
if ( false === strpos( $content, 'id="scholarship"' ) ) {
	$intro = '<div class="prose">'
		. '<p>Troy Area United Ministries has carried Dr. King\'s work in Rensselaer County for decades: the scholarship below, and the observances, conversations and community work the MLK Committee organises through the year.</p>'
		. '</div>'
		. '<div class="callout" style="margin-top:30px;">'
		. '<h3>More than the scholarship</h3>'
		. '<p>The MLK Committee\'s work across the county reaches well past this one award. If you would like to take part, host something, or hear what is planned, get in touch with Rev. Abby Norton-Levering at [taum email_director].</p>'
		. '</div>'
		. '<h2 id="scholarship" style="margin-top:46px;">The Ronald J. Dukes Memorial MLK Scholarship</h2>';
	$content = $intro . "\n" . $content;
}

$r = wp_update_post( wp_slash( array(
	'ID'           => $pg->ID,
	'post_title'   => "Carrying Dr. King's work in Rensselaer County.",
	'post_name'    => 'mlk',
	'post_excerpt' => "Troy Area United Ministries has honoured Dr. King's vision in this county since 1997, through the Ronald J. Dukes Memorial Scholarship and the work of the MLK Committee. The scholarship supports students ready to confront racism, poverty and injustice in their own neighbourhoods.",
	'post_content' => $content,
) ), true );
if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }

update_post_meta( $pg->ID, 'taum_kicker', 'MLK Programs' );
WP_CLI::log( '  page renamed, slug is now /mlk/' );

/* Old address keeps working: anyone holding /mlk-scholarship/ still lands right. */
$redirects = get_option( 'taum_redirects', array() );
$redirects['/mlk-scholarship/'] = '/mlk/';
update_option( 'taum_redirects', $redirects );
WP_CLI::log( '  /mlk-scholarship/ will redirect to /mlk/' );

flush_rewrite_rules( false );
WP_CLI::success( 'MLK page broadened.' );
