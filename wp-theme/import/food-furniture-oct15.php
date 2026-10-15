<?php
/**
 * Abby's 13 October changes to the Food and Furniture pages.
 *
 * Food: the "keep the fridge full" box pointed at the donate page. Abby sent
 * the SignUpGenius link for filling the fridge and asked for it near the bottom
 * of the food page, so the button goes there instead.
 *
 * Furniture: referrals are needed for most requests, which the page said only
 * in passing, so it now says it plainly in its own notice. Lyn Malatesta takes
 * furniture requests and Tyara Burnett takes donations, so the two contacts
 * swap into the right cards, and the "Request a pickup" button goes away:
 * it pointed at the contact form, which is not how a pickup actually gets
 * arranged. Heavily floral upholstery joins the list of what we cannot place.
 *
 * Targeted string replacements, not a rewrite: Abby edits this site, and a
 * wholesale import would overwrite whatever she has touched since. Each
 * replacement checks for its own result first, so re-running changes nothing.
 *
 *   wp eval-file ../import/food-furniture-oct15.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

/** Replace $old with $new in a page, unless $done is already present. */
function taum_patch( $slug, $pairs ) {
	$pg = get_page_by_path( $slug );
	if ( ! $pg ) { WP_CLI::warning( "$slug: page not found" ); return; }

	$content = $pg->post_content;
	$changed = 0;

	foreach ( $pairs as $label => $pair ) {
		list( $old, $new ) = $pair;
		if ( false !== strpos( $content, $new ) ) {
			WP_CLI::log( "  $slug: $label already applied" );
			continue;
		}
		if ( false === strpos( $content, $old ) ) {
			WP_CLI::warning( "  $slug: $label, could not find the text to replace. Someone has edited this section; apply it by hand." );
			continue;
		}
		$content = str_replace( $old, $new, $content );
		$changed++;
		WP_CLI::log( "  $slug: $label" );
	}

	if ( ! $changed ) { return; }

	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $content ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( "  $slug: saved ($changed change" . ( 1 === $changed ? '' : 's' ) . ")" );
}

/* ------------------------------------------------------------------ Food */

taum_patch( 'food', array(
	'fill the fridge sign-up replaces the donate button' => array(
		'<p>Drop off unexpired, labeled food anytime, or make a gift and we\'ll do the shopping.</p>
   <a class="btn small" style="margin-top:12px;" href="/donate/">Donate to the food program</a>',
		'<p>Drop off unexpired, labeled food anytime, or take a slot on the sign-up and fill the fridge yourself.</p>
   <a class="btn small" style="margin-top:12px;" href="https://www.signupgenius.com/go/10C0B4DAFAA2EA0FECE9-64549506-grab#/" rel="noopener">Sign up to fill the fridge</a>',
	),
) );

/* ------------------------------------------------------------- Furniture */

taum_patch( 'furniture', array(

	/* Lyn takes furniture requests. */
	'Lyn Malatesta is the contact for furniture requests' => array(
		'<p>We work through referrals from local agencies, shelters, and congregations. If you\'re working with a caseworker, ask them to contact us. If you\'re not, call us directly and we\'ll help you figure out the next step. Furniture and delivery are always free.</p>
<p style="margin-top: 12px;"><strong>Call Tyara Burnett, Program Director:<br>[taum phone] x204</strong></p>',
		'<p>Most furniture requests come to us through a referral. If you are working with a caseworker at a local agency, shelter, or congregation, ask them to get in touch with us. If you are not working with anyone yet, call and we will help you figure out the next step. Furniture and delivery are always free.</p>
<p style="margin-top: 12px;"><strong>Call Lyn Malatesta:<br>[taum tel]</strong><br>[taum email_office]</p>',
	),

	/* Tyara takes donations, and the pickup button goes. */
	'Tyara Burnett is the contact for donations, pickup button removed' => array(
		'<p>We come to you. Call or request a pickup below and we\'ll schedule a pickup from your home.</p>
<p><a class="btn" style="margin-top: 16px;" href="/contact/">Request a pickup</a></p>',
		'<p>We come to you. Give Tyara a call and we will schedule a pickup from your home.</p>
<p style="margin-top: 12px;"><strong>Call Tyara Burnett, Program Director:<br>[taum tel ext="204"]</strong></p>',
	),

	/* Say the referral rule plainly, where nobody can miss it. */
	'referral notice added above the accept and cannot-accept lists' => array(
		'<!-- wp:html -->
<div class="yes-no">',
		'<!-- wp:html -->
<div class="callout" style="margin-top: 34px;">
<h3>A referral comes first</h3>
<p>Most furniture requests need a referral from a caseworker at an agency, shelter, or congregation we work with. If you do not have a caseworker, call us anyway. We will not turn you away at the phone, and we will help you find the right door.</p>
</div>
<!-- /wp:html -->

<!-- wp:html -->
<div class="yes-no">',
	),

	/* Floral upholstery does not get placed. */
	'heavily floral upholstery added to what we cannot accept' => array(
		'<li>Sleeper sofas</li>
<li>China cabinets</li>
<li>Damaged furniture needing repair</li>',
		'<li>Sleeper sofas</li>
<li>China cabinets</li>
<li>Heavily floral print upholstery</li>
<li>Damaged furniture needing repair</li>',
	),
) );

WP_CLI::success( 'Food and furniture updated.' );
