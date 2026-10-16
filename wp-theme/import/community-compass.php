<?php
/**
 * Community Compass page.
 *
 * Abby, 15 October: "the page that is already on the Mt. Ida Preservation
 * site ... I think what they have is fine and Janet is fine with us
 * replicating it on our site."
 *
 * Source: https://mountidatroy.org/communitycompass/
 *
 * Written rather than copied. Two sites carrying the same paragraphs compete
 * with each other in search and both lose, and this reads better in TAUM's
 * voice anyway. Every fact is theirs: what Community Compass is, that it is
 * explicitly not a direct service provider, that the lists live as shared
 * documents so they can be corrected as things change, and the six
 * categories. Mount Ida and Community Compass are both credited and linked.
 *
 * The lists themselves are linked, never copied. Each document carries its
 * own notice that it may be copied and distributed only through August 2026
 * and that the reader should look for a newer version after that, so putting
 * pantry addresses and meal times into our pages would hand neighbors a
 * snapshot that nobody can keep current. A link always shows whatever
 * Community Compass has now.
 *
 *   wp eval-file ../import/community-compass.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$lists = array(
	'Food pantries'             => 'https://docs.google.com/document/d/1ek1yoJvgcLBe9xIe6OuZOkBKuTvyR6cIlDpL4kxz9k0/edit?usp=sharing',
	'Community meals'           => 'https://docs.google.com/document/d/1yJQ-8_Xa3A3JFrh2KQ-FAq2rnP6BRZ-xgvKcMqfyx3Q/edit?usp=sharing',
	'Veteran services'          => 'https://docs.google.com/document/d/1yg7zDhTUE9P0iLoVGq55_GlPxoniefoDLOIaT8vwToY/edit?usp=sharing',
	'English classes'           => 'https://docs.google.com/document/d/1ZatszR3eamZuuTOHYLkf_YMSaHWzQkcFXYW-socpL6c/edit?usp=sharing',
	'Mental health and addiction' => 'https://docs.google.com/document/d/1EpChK5ryVTp4R77L5T_b609LFhxP-xcmXqExdYmgRy0/edit?usp=sharing',
	'Senior care and activities' => 'https://docs.google.com/document/d/1GNU4wfCGqNouLAW6NnSe-nZBeDBYDi4nWoSCvln5CoM/edit?usp=sharing',
);

$cards = '';
foreach ( $lists as $label => $url ) {
	$cards .= '<div class="card"><h3><a href="' . esc_url( $url ) . '" rel="noopener">' . esc_html( $label ) . '</a></h3>'
		. '<p style="color:var(--indigo-soft);font-size:.92rem;">Opens the list Community Compass keeps, so you always see their latest version.</p></div>' . "\n   ";
}

$body = '<div class="prose">
<p>Community Compass is a community-led effort to help neighbors find reliable information about food, housing, health, legal, family and civic support across Troy and the Capital Region. It is not a service provider and it does not hand anything out. What it does is make the rest of the landscape easier to read, so you can find the right door the first time.</p>
<p>The lists below are gathered and maintained by Community Compass. We link to them rather than copy them, so what you open is always their current version rather than a snapshot of ours, and a correction they make shows up here the moment they make it.</p>
</div>

<h2 style="margin-top:44px;">Resource lists</h2>
<div class="grid2">
   ' . trim( $cards ) . '
</div>

<div class="callout" style="margin-top:38px;">
<h3>Found something out of date?</h3>
<p>These lists only work if people correct them. Community Compass asks to hear about additions, errors and changes at <a href="mailto:communitycompassrc@gmail.com">communitycompassrc@gmail.com</a>. If it is easier, tell us on [taum phone] and we will pass it along.</p>
</div>

<div class="prose" style="margin-top:34px;">
<p><strong>Who runs it.</strong> Community Compass is a collaborative effort. The <a href="https://mountidatroy.org/communitycompass/" rel="noopener">Mount Ida Preservation Association</a> carries it as part of their community support work, and TAUM shares it here because our neighbors ask us these questions every week.</p>
<p>For what TAUM itself provides, see <a href="/food/">free food</a>, <a href="/furniture/">free furniture</a>, and <a href="/programs/">everything else we run</a>.</p>
</div>';

$excerpt = 'Community Compass gathers lists of where to find food, veteran services, English classes, mental health and addiction support, and senior care across Troy and the Capital Region. We link to their lists here, rather than copy them, because our neighbors ask us these questions every week.';

$pg = get_page_by_path( 'community-compass' );
if ( ! $pg ) {
	$id = wp_insert_post( wp_slash( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'post_name'      => 'community-compass',
		'post_title'     => 'Finding the right door.',
		'post_excerpt'   => $excerpt,
		'post_content'   => $body,
		'comment_status' => 'closed',
	) ), true );
	if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
	$pg = get_post( $id );
	WP_CLI::log( '  page created' );
} else {
	$r = wp_update_post( wp_slash( array(
		'ID'           => $pg->ID,
		'post_content' => $body,
		'post_excerpt' => $excerpt,
	) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( '  page updated' );
}
update_post_meta( $pg->ID, 'taum_kicker', 'Community Compass' );

/* The partner record points at our page, which points on to theirs. */
foreach ( get_posts( array( 'post_type' => 'taum_partner', 'numberposts' => -1 ) ) as $p ) {
	if ( false !== stripos( get_the_title( $p ), 'Community Compass' ) ) {
		update_post_meta( $p->ID, 'taum_partner_url', get_permalink( $pg->ID ) );
		WP_CLI::log( '  partner entry points at the new page' );
	}
}

/* The food page is where someone looking for a pantry actually lands. */
$food = get_page_by_path( 'food' );
if ( $food ) {
	$marker = 'Community Compass keeps lists';
	if ( false === strpos( $food->post_content, $marker ) ) {
		$old = '<div class="callout" style="margin-top:34px;">
   <h3>When to come</h3>';
		$new = '<div class="callout" style="margin-top:34px;">
   <h3>If you need more than we have</h3>
   <p>' . $marker . ' of every food pantry and community meal in Troy and Lansingburgh, not just ours. <a href="/community-compass/">See the lists</a>.</p>
  </div>

  <div class="callout" style="margin-top:34px;">
   <h3>When to come</h3>';
		if ( false !== strpos( $food->post_content, $old ) ) {
			wp_update_post( wp_slash( array( 'ID' => $food->ID, 'post_content' => str_replace( $old, $new, $food->post_content ) ) ) );
			WP_CLI::log( '  food page links to the lists' );
		} else {
			WP_CLI::warning( '  could not place the link on the food page, add it by hand' );
		}
	} else {
		WP_CLI::log( '  food page already links to the lists' );
	}
}

WP_CLI::success( 'Community Compass page in place.' );
