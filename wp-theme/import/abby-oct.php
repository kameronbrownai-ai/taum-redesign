<?php
/**
 * Content changes from Abby's 1 Oct 2026 emails.
 *   wp eval-file ../import/abby-oct.php
 * Idempotent: events match by title, the page edits are string replacements
 * that no-op once applied.
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0]->ID;
$VOL   = 'https://secure.lglforms.com/form_engine/s/tyo0i-3Yd8ZON3oFUVTWuQ';
$OLD   = 'https://forms.gle/BWSMYFMgri5MybR97';

/* ── 1. Volunteer link: route page copy through the Customizer so the next
      change is one field in Site Info, not a hunt through pages. ────────── */
foreach ( get_posts( array( 'post_type' => array( 'page', 'post' ), 'posts_per_page' => -1, 's' => 'forms.gle' ) ) as $p ) {
	$c = str_replace( $OLD, '[taum url_volunteer_raw]', $p->post_content );
	if ( $c !== $p->post_content ) {
		wp_update_post( wp_slash( array( 'ID' => $p->ID, 'post_content' => $c ) ) );
		WP_CLI::log( '  volunteer link  ' . $p->post_name );
	}
}

/* ── 2. Autumn events from Abby's flyers ──────────────────────────────────
   Locations are deliberately blank: the flyers carry them and guessing an
   address someone drives to is not acceptable. Fill them in from the PDFs. */
$events = array(
	array(
		'title' => 'Community Garden Celebration',
		'date'  => '2026-10-13', 'time' => '5:00 to 6:00 pm', 'order' => 20,
		'body'  => 'An hour in the garden to mark the end of the growing season and thank everyone who planted, weeded, watered, and harvested this year. Produce from these beds went straight onto the Thursday table and into the Free Food Fridge all summer.',
		'url'   => '/get-involved/', 'label' => 'Help in the garden',
	),
	array(
		'title' => 'Regional Food Bank Distribution',
		'date'  => '2026-10-17', 'time' => '11:30 am to 12:30 pm', 'order' => 21,
		'body'  => 'A large food distribution in partnership with the Regional Food Bank. Come by between 11:30 and 12:30 and take what your household needs. No ID and no referral.' . "\n\n" . '<strong>Volunteers are needed from 9:30 am</strong> to unload, sort, and hand out. This is the shift where extra hands make the biggest difference.',
		'url'   => $VOL, 'label' => 'Volunteer for this',
	),
	array(
		'title' => 'TAUM Holiday Market',
		'date'  => '2026-11-14', 'time' => '1:00 to 5:00 pm', 'order' => 22,
		'body'  => 'A holiday market of local makers and small businesses. <strong>Vendors wanted.</strong> If you make something and want a table, get in touch and we will send you the details.',
		'url'   => '/contact/', 'label' => 'Ask about a vendor table',
	),
	array(
		'title' => 'Osgood Community Thanksgiving Feast',
		'date'  => '2026-11-23', 'time' => '5:45 to 7:00 pm', 'order' => 23,
		'body'  => 'A full Thanksgiving dinner, open to the whole neighborhood. Everyone is welcome, whatever brought you here and whoever you are coming with.',
		'url'   => '', 'label' => '',
	),
);

foreach ( $events as $e ) {
	$found = get_posts( array( 'post_type' => 'taum_event', 'title' => $e['title'], 'posts_per_page' => 1, 'post_status' => 'any' ) );
	$data = array(
		'post_type' => 'taum_event', 'post_status' => 'publish',
		'post_title' => $e['title'], 'post_content' => $e['body'],
		'post_author' => $admin, 'menu_order' => $e['order'], 'comment_status' => 'closed',
	);
	if ( $found ) { $data['ID'] = $found[0]->ID; $id = wp_update_post( wp_slash( $data ) ); }
	else { $id = wp_insert_post( wp_slash( $data ) ); }
	update_post_meta( $id, 'taum_event_recurring', '' );
	update_post_meta( $id, 'taum_event_day_label', '' );
	update_post_meta( $id, 'taum_event_date', $e['date'] );
	update_post_meta( $id, 'taum_event_time', $e['time'] );
	update_post_meta( $id, 'taum_event_place', '' );
	update_post_meta( $id, 'taum_event_url', $e['url'] );
	update_post_meta( $id, 'taum_event_url_label', $e['label'] );
	WP_CLI::log( sprintf( '  event  %-38s %s  #%d', $e['title'], $e['date'], $id ) );
}

/* ── 3. MLK page: 2026 scholars replace the 2025 line ─────────────────── */
$mlk = get_page_by_path( 'mlk-scholarship' );
if ( $mlk ) {
	$old_block = 'Congratulations to our 2025 scholars';
	if ( false !== strpos( $mlk->post_content, $old_block ) ) {
		$c = preg_replace(
			'#<div class="callout warm"[^>]*>\s*<h3>Congratulations to our 2025 scholars</h3>.*?</div>#s',
			'<div class="callout warm" style="margin-top:44px;">' . "\n"
			. '   <h3>Congratulations to our 2026 scholars</h3>' . "\n"
			. '   <p>Four students received the Ronald J. Dukes Memorial MLK Scholarship this year: <strong>Chance Davis</strong>, <strong>Karalina Hastings</strong>, <strong>Ethan McMahon</strong>, and <strong>Emma Waugh</strong>.</p>' . "\n"
			. '  </div>',
			$mlk->post_content
		);
		if ( $c !== $mlk->post_content ) {
			wp_update_post( wp_slash( array( 'ID' => $mlk->ID, 'post_content' => $c ) ) );
			WP_CLI::log( '  mlk     2025 line replaced with the four 2026 scholars' );
		} else {
			WP_CLI::warning( '  mlk: pattern did not match, left untouched' );
		}
	} else {
		WP_CLI::log( '  mlk     already updated' );
	}
}

/* ── 4. Summer recap post ─────────────────────────────────────────────── */
$title = 'Sixteen teens finished Tech for Teens this summer';
$found = get_posts( array( 'post_type' => 'post', 'title' => $title, 'posts_per_page' => 1, 'post_status' => 'any' ) );
$body  = '<p>Sixteen young people completed Tech for Teens this summer. Alongside the Office skills, resume writing, and web work the program has always covered, this year added <strong>3D printing</strong> and a unit on <strong>using AI ethically</strong>, which is a conversation most adults have not had yet either.</p>'
	. '<p>Every student who finished took their computer home with them. That is the part that lasts: a laptop on a kitchen table in Rensselaer County, belonging to a teenager who knows what to do with it.</p>'
	. '<p>The garden had a good season too, and vegetables from the beds behind the building went straight into the meals we serve and the Free Food Fridge out front.</p>'
	. '<p><a class="btn small" href="/tech-for-teens/">About Tech for Teens</a> <a class="btn small ghost" href="[taum url_newsletter_raw]">Get the newsletter</a></p>';

$data = array(
	'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $title,
	'post_content' => $body, 'post_author' => $admin, 'comment_status' => 'closed',
	'post_excerpt' => 'Sixteen young people finished the summer session, this year with 3D printing and a unit on using AI ethically. Every graduate took their computer home.',
	'post_date' => '2026-09-05 09:00:00',
);
if ( $found ) { $data['ID'] = $found[0]->ID; $id = wp_update_post( wp_slash( $data ) ); }
else { $id = wp_insert_post( wp_slash( $data ) ); }
// Carry the Tech for Teens photo until Abby's own summer photos arrive.
$img = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1, 'meta_query' => array( array( 'key' => '_taum_source', 'value' => 'teens-laptops.jpg' ) ) ) );
if ( $img ) { set_post_thumbnail( $id, $img[0]->ID ); }
// Make it one of the two homepage features, retiring the older of the current pair.
$sticky = get_option( 'sticky_posts', array() );
if ( ! in_array( $id, $sticky, true ) ) { array_unshift( $sticky, $id ); }
update_option( 'sticky_posts', array_slice( $sticky, 0, 2 ) );
WP_CLI::log( '  post    ' . $title . '  #' . $id . '  (featured on the homepage)' );

flush_rewrite_rules( false );
WP_CLI::success( "Abby's October content applied." );
