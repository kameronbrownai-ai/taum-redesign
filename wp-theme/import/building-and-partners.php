<?php
/**
 * Community Center refocused on the building, plus a Partners page.
 *
 * From the 13 October review. The Community Center page was describing
 * programs that happen in the building rather than the building itself, so
 * someone looking for a room had to read past the food programme and the
 * Damien Center to find out whether they could book one. Those cards go, the
 * rooms get their own section with what each is good for, and the pricing
 * Abby gave is stated plainly: fifty dollars an hour for businesses, a
 * donation asked of nonprofits.
 *
 * What is NOT written here: capacities, room dimensions, equipment lists, and
 * the use policy itself. Abby has a draft policy she has not sent yet, and
 * none of the rest was in her notes. Inventing a room capacity would put a
 * number in a stranger's head before they call. The page says what is true
 * and tells people to ask.
 *
 * Community Compass moves off this page and onto Partners, where it already
 * existed as a partner record.
 *
 *   wp eval-file ../import/building-and-partners.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

/* ====================================================== Community Center */

$pg = get_page_by_path( 'community-center' );
if ( ! $pg ) { WP_CLI::error( 'Community Center page not found.' ); }

$body = <<<'HTML'
<p class="hero-actions"><a class="btn" href="/contact/">Ask about a room</a>
    <a class="btn ghost" href="/news/">See what's on</a></p>

<section>
 <div class="wrap">
  <h2>The rooms</h2>
  <p style="max-width:58ch;color:var(--indigo-soft);margin-top:10px;">Three kinds of space, all on one floor at 392 Second Street. Tell us roughly how many people and what you are planning, and we will tell you which one fits.</p>
  <div class="grid2">
   <div class="card">
    <h3>The community room</h3>
    <p>Our largest gathering space. Tables and chairs are included and we set them up the way you need: rows for a talk, circles for a discussion, long tables for a meal.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);">Good for meetings, classes, workshops, celebrations, memorials.</p>
   </div>
   <div class="card">
    <h3>The kitchen</h3>
    <p>The working kitchen Monday dinner and Thursday lunch come out of. Available to groups cooking for the neighborhood, on its own or alongside the community room.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);">Good for community meals, cooking classes, food prep for an event.</p>
   </div>
   <div class="card">
    <h3>Smaller meeting rooms</h3>
    <p>Rooms for a handful of people rather than a crowd, where a conversation stays private.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);">Good for a committee, a support group, tutoring, an interview, a quiet hour.</p>
   </div>
  </div>
 </div>
</section>

<section class="section-tint">
 <div class="wrap">
  <h2>What it costs</h2>
  <div class="grid2" style="margin-top:26px;">
   <div class="card">
    <h3>Businesses and private hire</h3>
    <p style="font-family:var(--serif);font-size:1.9rem;font-weight:700;color:var(--coral-dark);margin:6px 0 8px;">$50 an hour</p>
    <p>For commercial use and private events.</p>
   </div>
   <div class="card">
    <h3>Nonprofits and community groups</h3>
    <p style="font-family:var(--serif);font-size:1.9rem;font-weight:700;color:var(--teal-deep);margin:6px 0 8px;">A donation</p>
    <p>We ask for a donation rather than a rate. Give what the work can carry. If money is tight, say so, and we will work it out.</p>
   </div>
  </div>
  <div class="callout" style="margin-top:28px;">
   <h3>Who can use the space</h3>
   <p>Faith groups and secular groups. Block associations and book clubs. Twelve-step meetings, tenant meetings, craft nights, funerals, baby showers, budget hearings. We were founded by congregations, but you do not have to believe anything in particular to walk through the door and use this place.</p>
   <p style="margin-top:12px;">What we do ask is that the work lines up with ours: that it treats people in this neighborhood with dignity and does not set one group of neighbors against another. Our full use policy is being finalized and we will send you a copy when you book. If you are not sure whether your group fits, call and ask. The answer is usually yes.</p>
  </div>
 </div>
</section>

<section>
 <div class="wrap">
  <div class="media-row">
   <div>
    <h2>How to book it</h2>
    <ol class="steps">
     <li><strong>Call or write</strong> and tell us what you are planning, roughly how many people, and the dates you have in mind.</li>
     <li><strong>We will check the calendar</strong> and tell you which room fits and what is free.</li>
     <li><strong>We confirm in writing</strong>, with the cost or the donation and a copy of the use policy.</li>
    </ol>
   </div>
   <div class="media-photo"><img src="https://mediumslateblue-dolphin-566869.hostingersite.com/wp-content/uploads/2026/10/free-food-fridge-blessing-1024x768.jpg" alt="Neighbors and clergy gathered outside the Free Food Fridge on Second Street." loading="lazy"></div>
  </div>
 </div>
</section>

<section class="banner">
 <div class="wrap">
  <div>
   <h2>Need a room?</h2>
   <p>Tell us what you're planning, roughly how many people, and when. Lyn will walk you through what's available.</p>
  </div>
  <a class="btn" href="/contact/">Get in touch</a>
 </div>
</section>

<section>
 <div class="wrap">
  <div class="prose">
   <p><strong>Contact:</strong> Lyn Malatesta, Office Manager · [taum phone] · [taum email_office]</p>
   <p><strong>Office hours:</strong> [taum hours] · [taum address1], [taum address2]</p>
   <p>Organizations that work out of this building are listed on our <a href="/partners/">partners page</a>.</p>
  </div>
 </div>
</section>
HTML;

if ( false !== strpos( $pg->post_content, '<h2>The rooms</h2>' ) ) {
	WP_CLI::log( '  community centre already rebuilt' );
} else {
	$r = wp_update_post( wp_slash( array(
		'ID'           => $pg->ID,
		'post_content' => $body,
		'post_excerpt' => '392 Second Street has a community room, a working kitchen, and smaller meeting rooms, and they are available to the neighborhood. Fifty dollars an hour for businesses, a donation for nonprofits and community groups. Tell us what you are planning and we will tell you which room fits.',
	) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( '  community centre rebuilt around the building' );
}
update_post_meta( $pg->ID, 'taum_kicker', 'Use the building' );

/* ============================================================= Partners */

$partners = get_page_by_path( 'partners' );
if ( ! $partners ) {
	$id = wp_insert_post( wp_slash( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'post_name'      => 'partners',
		'post_title'     => 'Nothing here happens alone.',
		'post_excerpt'   => 'The organizations we work alongside, the ones that share our building, and the funders and businesses whose support keeps the doors open.',
		'post_content'   => '',
		'comment_status' => 'closed',
	) ), true );
	if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
	// No page_template is set: page-partners.php is picked by the slug through
	// the template hierarchy. Assigning it explicitly is rejected, because
	// WordPress only accepts templates that declare a Template Name.
	WP_CLI::log( '  partners page created' );
	$partners = get_post( $id );
} else {
	WP_CLI::log( '  partners page already exists' );
}

// Set outside the branch: an earlier run created the page and then failed
// before it got here, so the "already exists" path has to fix it up too.
update_post_meta( $partners->ID, 'taum_kicker', 'Partners & supporters' );

/* In the About dropdown, where Abby wanted it, not the top row.
   There was already a custom "Partners & supporters" item pointing at an
   anchor on the About page. That section is its own page now, so the
   existing item is repointed rather than left dangling beside a new one. */
$menu = wp_get_nav_menu_object( 'Main' );
if ( $menu ) {
	$items   = wp_get_nav_menu_items( $menu->term_id );
	$about   = null;
	$already = false;

	foreach ( $items as $it ) {
		if ( 'about' === basename( untrailingslashit( $it->url ) ) && 0 === (int) $it->menu_item_parent ) {
			$about = $it;
		}
	}

	foreach ( $items as $it ) {
		$points_here = ( 'page' === $it->object && (int) $it->object_id === (int) $partners->ID )
			|| false !== strpos( $it->url, '/partners' );

		if ( false !== strpos( $it->url, '/about/#partners' ) ) {
			// The old anchor item becomes the link to the new page.
			update_post_meta( $it->ID, '_menu_item_url', get_permalink( $partners->ID ) );
			$already = true;
			WP_CLI::log( '  About > Partners & supporters now opens the partners page' );
		} elseif ( $points_here ) {
			if ( $already ) {
				// A second entry for the same page: one dropdown, one way in.
				wp_delete_post( $it->ID, true );
				WP_CLI::log( '  removed a duplicate Partners item from the menu' );
			} else {
				$already = true;
			}
		}
	}

	if ( $about && ! $already ) {
		wp_update_nav_menu_item( $menu->term_id, 0, array(
			'menu-item-title'     => 'Partners & supporters',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $partners->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $about->ID,
		) );
		WP_CLI::log( '  partners added to the About menu' );
	}
}

WP_CLI::success( 'Building refocused, partners page in place.' );
