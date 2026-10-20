<?php
/**
 * Abby, 20 October, two corrections that had to be made everywhere at once.
 *
 * Barbara has no extension at the Damien Center any more. x206 reaches
 * nobody. People should email bhealey@taum.org, or call the main number and
 * leave a message, and Abby says email is the better option in most cases.
 *
 * Furniture splits by direction, which we had half-done: people who want to
 * RECEIVE furniture call the main number with no extension, and people who
 * want to GIVE furniture call the furniture program on x204. Abby noticed it
 * had not propagated, "including on the front page", and she was right: the
 * homepage quickbar still sent people wanting furniture to x204.
 *
 * Everything the two changes touch, in one pass, so there is no second round
 * of her finding the one page we missed.
 *
 *   wp eval-file ../import/oct20-contacts.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$changes = 0;

function taum_patch_page( $slug, $pairs ) {
	global $changes;
	$pg = get_page_by_path( $slug );
	if ( ! $pg ) { WP_CLI::warning( "  $slug: page not found" ); return; }
	$c = $pg->post_content;
	$n = 0;
	foreach ( $pairs as $label => $pair ) {
		list( $old, $new ) = $pair;
		if ( '' !== $new && false !== strpos( $c, $new ) ) {
			WP_CLI::log( "  $slug / $label: already applied" );
			continue;
		}
		if ( false === strpos( $c, $old ) ) {
			WP_CLI::warning( "  $slug / $label: text not found, check by hand" );
			continue;
		}
		$c = str_replace( $old, $new, $c );
		$n++;
		WP_CLI::log( "  $slug / $label" );
	}
	if ( $n ) {
		$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $c ) ), true );
		if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
		$changes += $n;
	}
}

/* ------------------------------------------------------------- Barbara */

$barbara_line = '<p><strong>Questions?</strong> Barbara Healey, Damien Center Coordinator, [taum phone] x206</p>';
$barbara_new  = '<p><strong>Questions?</strong> Barbara Healey, Damien Center Coordinator, <a href="mailto:bhealey@taum.org">bhealey@taum.org</a>. She has no direct extension, so email reaches her fastest, or call [taum phone] and leave her a message.</p>';

/* The two pages label her line differently, "Questions?" on Food and
   "Contact:" on the Damien Center, so each gets its own pair. */
taum_patch_page( 'food', array( 'Barbara by email' => array( $barbara_line, $barbara_new ) ) );
taum_patch_page( 'damien-center', array( 'Barbara by email' => array(
	str_replace( '<strong>Questions?</strong>', '<strong>Contact:</strong>', $barbara_line ),
	str_replace( '<strong>Questions?</strong>', '<strong>Contact:</strong>', $barbara_new ),
) ) );

taum_patch_page( 'about', array(
	'Barbara bio drops the dead extension' => array(
		'Barbara sets the table, literally. She coordinates meals, community, and care at the Damien Center, where everyone gets a warm welcome. x206',
		'Barbara sets the table, literally. She coordinates meals, community, and care at the Damien Center, where everyone gets a warm welcome. bhealey@taum.org',
	),
	'Tyara bio is about donations, since requests go to the front desk' => array(
		'Tyara runs the trucks, the warehouse, and the logistics that turn a donated dresser into a family\'s fresh start. Reach her about furniture donations and referrals. x204',
		'Tyara runs the trucks, the warehouse, and the logistics that turn a donated dresser into a family\'s fresh start. Reach her about furniture donations and pickups. x204',
	),
) );

/* ----------------------------------------------------------- Furniture */

taum_patch_page( 'faq', array(
	'asking for furniture goes to the main number' => array(
		'<div class="a"><p>Call Tyara Burnett at <strong>[taum phone] x204</strong>. If you\'re working with a caseworker or shelter, they can refer you directly.',
		'<div class="a"><p>Call our main number, <strong>[taum phone]</strong>, with no extension. If you\'re working with a caseworker or shelter, they can refer you directly.',
	),
	'offering furniture keeps the program extension' => array(
		'<div class="a"><p>Call <strong>[taum phone] x204</strong> to arrange a pickup from your home.',
		'<div class="a"><p>Call the furniture program on <strong>[taum phone] x204</strong> to arrange a pickup from your home.',
	),
	'what we cannot accept matches the furniture page' => array(
		'We can\'t accept sleeper sofas or furniture that needs repair.',
		'We can\'t accept sleeper sofas, china cabinets, heavily floral print upholstery, or furniture that needs repair.',
	),
) );

/* ------------------------------------------------------------- Contact */

taum_patch_page( 'contact', array(
	'the table splits furniture by direction' => array(
		'<tr><td>Furniture, giving or receiving</td><td>Tyara Burnett, Program Director</td><td>x204</td></tr>',
		'<tr><td>Furniture, if you need some</td><td>The front desk</td><td>[taum phone], no extension</td></tr>' . "\n" .
		'   <tr><td>Furniture, if you have some to give</td><td>Tyara Burnett, Program Director</td><td>x204</td></tr>',
	),
	'Barbara by email in the table' => array(
		'<tr><td>Meals &amp; the Damien Center</td><td>Barbara Healey, Coordinator</td><td>x206</td></tr>',
		'<tr><td>Meals &amp; the Damien Center</td><td>Barbara Healey, Coordinator</td><td><a href="mailto:bhealey@taum.org">bhealey@taum.org</a></td></tr>',
	),
) );

/* Abby's words were "call our main number", so the furniture page says that
   rather than naming whoever happens to answer it. Lynn's name was fine until
   Barbara's extension went dead and showed how a route tied to one person
   ages. The office email stays, because that one is a real inbox. */
taum_patch_page( 'furniture', array(
	'requesting furniture points at the main number, not a person' => array(
		'<p style="margin-top: 12px;"><strong>Call Lynn Malatesta:<br>[taum tel]</strong><br>[taum email_office]</p>',
		'<p style="margin-top: 12px;"><strong>Call our main number:<br>[taum tel]</strong><br>No extension needed. You can also write to [taum email_office].</p>',
	),
) );

/* ------------------------------------------- The Programs page contacts */

$meta = array(
	// slug => name, ext, phone, email
	'food'          => array( 'Barbara Healey, Damien Center Coordinator', '', '', 'bhealey@taum.org' ),
	'damien-center' => array( 'Barbara Healey, Damien Center Coordinator', '', '', 'bhealey@taum.org' ),
	'furniture'     => array( 'The front desk, for furniture requests', '', '', '' ),
);
foreach ( $meta as $slug => $m ) {
	$pg = get_page_by_path( $slug );
	if ( ! $pg ) { continue; }
	list( $name, $ext, $phone, $email ) = $m;
	update_post_meta( $pg->ID, 'taum_contact_name', $name );
	update_post_meta( $pg->ID, 'taum_contact_ext', $ext );
	update_post_meta( $pg->ID, 'taum_contact_phone', $phone );
	update_post_meta( $pg->ID, 'taum_contact_email', $email );
	WP_CLI::log( "  $slug: Programs card contact set to $name" );
	$changes++;
}

WP_CLI::success( "Done ($changes changes)." );
