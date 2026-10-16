<?php
/**
 * Abby's answers to the "Website things still needed" list, 15 October.
 *
 * Her words, applied:
 *
 *  - Vendor form:  the real LGL application, replacing the contact page the
 *                  Holiday Market button was falling back to.
 *  - Numbers:      2,000+ neighbors stands ("it includes recipients of
 *                  furniture, food, and participants in MLK and CROP walk").
 *                  Meals drop from 14,000 to 11,000+. Households get the plus
 *                  they always deserved. The teens figure was wrong by more
 *                  than half: 16 in Tech for Teens, not 40.
 *  - MLK:          breakfast is 8:30 am at Franklin Terrace, with Rashida
 *                  Taylor of the New York State Council of Churches speaking.
 *                  The worship celebration is not settled; it is "likely"
 *                  Bethel Baptist, so the page says likely rather than
 *                  promising a room that might change.
 *  - Scholars:     two more 2026 recipients she had left off, Jahnia Venson
 *                  and Desmond Pickens Harrison. Six, not four.
 *  - CROP Walk:    the national walk page for Troy, where people can give any
 *                  day of the year and where Abby is listed as local contact.
 *  - Donate:       the updated PayPal page.
 *
 *   wp eval-file ../import/abby-oct15-answers.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

/* ------------------------------------------------- Site-wide settings */

/* These live as theme mods, which is what taum_opt() reads and what the
   Customizer writes, so they show up in TAUM Info for Abby to edit later. */
$settings = array(
	'url_donate' => 'https://www.paypal.com/donate/?hosted_button_id=2SGN4SV4NRFRN',
	'stat1_n'    => '2000',
	'stat1_s'    => '+',
	'stat1_l'    => 'neighbors served every year',
	'stat2_n'    => '11000',
	'stat2_s'    => '+',
	'stat2_l'    => 'meals shared from our kitchen',
	'stat3_n'    => '300',
	'stat3_s'    => '+',
	'stat3_l'    => 'households furnished',
	'stat4_n'    => '16',
	'stat4_s'    => '',
	'stat4_l'    => 'teens in Tech for Teens each summer',
);

foreach ( $settings as $k => $v ) {
	$before = get_theme_mod( 'taum_' . $k, '(theme default)' );
	if ( $before !== $v ) {
		set_theme_mod( 'taum_' . $k, $v );
		WP_CLI::log( "  $k: $before -> " . ( '' === $v ? '(blank)' : $v ) );
	} else {
		WP_CLI::log( "  $k: already $v" );
	}
}

/* An earlier run of this script wrote these into an option named taum_opts,
   which nothing reads. Clear it rather than leave a decoy copy of the numbers
   sitting in the database for someone to find later and believe. */
if ( false !== get_option( 'taum_opts', false ) ) {
	delete_option( 'taum_opts' );
	WP_CLI::log( '  removed the stray taum_opts option' );
}

/* ------------------------------------------------------------- Events */

function taum_set_event_link( $match, $url, $label ) {
	foreach ( get_posts( array( 'post_type' => 'taum_event', 'numberposts' => -1, 'post_status' => 'any' ) ) as $ev ) {
		if ( false === stripos( get_the_title( $ev ), $match ) ) {
			continue;
		}
		update_post_meta( $ev->ID, 'taum_event_url', $url );
		update_post_meta( $ev->ID, 'taum_event_url_label', $label );
		WP_CLI::log( '  event "' . get_the_title( $ev ) . '" -> ' . $url );
		return true;
	}
	WP_CLI::warning( "  no event matching \"$match\"" );
	return false;
}

taum_set_event_link(
	'Holiday Market',
	'https://secure.lglforms.com/form_engine/s/9Gd8gLBR3jqLxO3hleUH-g',
	'Apply to be a vendor'
);

taum_set_event_link(
	'CROP',
	'https://events.crophungerwalk.org/chw/event/troyny',
	'Learn more and give'
);

/* ---------------------------------------------------------------- MLK */

$pg = get_page_by_path( 'mlk' );
if ( ! $pg ) { WP_CLI::error( 'MLK page not found.' ); }

$content = $pg->post_content;
$changed = 0;

$patch = function ( $label, $old, $new ) use ( &$content, &$changed ) {
	if ( '' !== $new && false !== strpos( $content, $new ) ) {
		WP_CLI::log( "  $label: already applied" );
		return;
	}
	if ( false === strpos( $content, $old ) ) {
		WP_CLI::warning( "  $label: could not find the text to replace, apply by hand" );
		return;
	}
	$content = str_replace( $old, $new, $content );
	$changed++;
	WP_CLI::log( "  $label" );
};

$patch(
	'breakfast has its time, place and speaker',
	'<h3>MLK Day breakfast</h3>
    <p>Neighbors, congregations and community partners at one table before the service.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);"><em>Time, location and ticketing to be confirmed. Call [taum phone] or watch <a href="/news/">News and events</a>.</em></p>',
	'<h3>MLK Day breakfast</h3>
    <p><strong>8:30 am at Franklin Terrace, Troy.</strong> Neighbors, congregations and community partners at one table before the service.</p>
    <p style="margin-top:10px;">Speaking this year: <strong>Rashida Taylor</strong>, Executive Director of the New York State Council of Churches.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);"><em>Ticketing to be confirmed. Call [taum phone] or watch <a href="/news/">News and events</a>.</em></p>'
);

$patch(
	'worship service says what is known and what is not',
	'<h3>MLK Day worship service</h3>
    <p>A service of remembrance and recommitment, with guest speakers and music.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);"><em>Time, location and guests to be confirmed. Call [taum phone] or watch <a href="/news/">News and events</a>.</em></p>',
	'<h3>MLK Day worship service</h3>
    <p>A service of remembrance and recommitment, with guest speakers and music. The committee is still working out the details, and it will most likely be at Bethel Baptist, as it has been in recent years.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);"><em>Time and location to be confirmed. Call [taum phone] or watch <a href="/news/">News and events</a>.</em></p>'
);

$patch(
	'all six 2026 scholars, not four',
	'<p><strong>Chance Davis</strong> · <strong>Karalina Hastings</strong> · <strong>Ethan McMahon</strong> · <strong>Emma Waugh</strong></p>',
	'<p><strong>Chance Davis</strong> · <strong>Karalina Hastings</strong> · <strong>Ethan McMahon</strong> · <strong>Desmond Pickens Harrison</strong> · <strong>Jahnia Venson</strong> · <strong>Emma Waugh</strong></p>'
);

if ( $changed ) {
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $content ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( "  MLK page saved ($changed change" . ( 1 === $changed ? '' : 's' ) . ')' );
}

WP_CLI::success( "Abby's answers applied." );
