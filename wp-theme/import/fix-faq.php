<?php
/**
 * The FAQ still told furniture donors that pickup is free. Abby changed the
 * Furniture page on 21 Sept to say TAUM requests a donation toward pickup
 * costs, and her flyer says the same. She runs the programme, so her wording
 * is the one that is right; the FAQ is what has to move.
 *
 * This answer is also inside the FAQPage schema, so Google and AI assistants
 * were quoting "free pickup" back to people.
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$faq = get_page_by_path( 'faq' );
if ( ! $faq ) { WP_CLI::error( 'faq page not found' ); }

$old = 'Call <strong>[taum phone] x204</strong> to schedule a free pickup from your home.';
$new = 'Call <strong>[taum phone] x204</strong> to arrange a pickup from your home. We ask for a monetary donation toward the cost of the pickup, based on where you are and how much there is: it covers gas, insurance, and the crew. Your gift keeps furniture moving to the families who need it.';

$c = $faq->post_content;
if ( false === strpos( $c, 'free pickup' ) ) {
	WP_CLI::log( '  already updated' );
	return;
}
// Match whatever the exact surrounding markup is, replacing only the sentence.
$c2 = preg_replace(
	'#Call <strong>\[taum phone\] x204</strong> to schedule a free pickup from your home\.#',
	$new,
	$c
);
if ( $c2 === $c ) {
	// Fall back to a looser match if the phone shortcode is not there.
	$c2 = preg_replace( '#to schedule a free pickup from your home\.#', 'to arrange a pickup from your home. We ask for a monetary donation toward the cost of the pickup, based on where you are and how much there is: it covers gas, insurance, and the crew.', $c );
}
if ( $c2 === $c ) { WP_CLI::warning( '  pattern did not match, FAQ left untouched' ); return; }
wp_update_post( wp_slash( array( 'ID' => $faq->ID, 'post_content' => $c2 ) ) );
WP_CLI::success( 'FAQ now matches the Furniture page on pickup donations.' );
