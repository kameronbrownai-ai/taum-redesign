<?php
/**
 * MLK page: upcoming events, past recipients, and the real application PDF.
 *
 * The "Download the application" link pointed at /mlk-scholarship/ on the old
 * taum.org, which is a page, not a file. It now points at the actual PDF Abby
 * has on taum.org. That file is the 2026 form with a deadline of 4 May 2026,
 * which has passed, so the page says so rather than letting someone mail an
 * expired application.
 *
 * Facts taken from that PDF, which is TAUM's own: the scholarship was
 * established in 1997, renamed in 2015 for Ronald J. Dukes, former chairman of
 * the MLK Committee, and non-residents of Rensselaer County must include a
 * reference letter, which the page had left out of the checklist.
 *
 * The MLK Day events come from Abby's 13 October review, where she named the
 * worship service and the breakfast and said the speaker was not settled yet.
 * Times, places and guests are therefore marked unconfirmed rather than
 * guessed at, in the same way the autumn events are handled on the news page.
 *
 * Past recipients: only the 2026 cohort is known. Abby is gathering earlier
 * years. The section is built so adding a year is one more list item.
 *
 *   wp eval-file ../import/mlk-events-recipients.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$pg = get_page_by_path( 'mlk' );
if ( ! $pg ) { WP_CLI::error( 'MLK page not found.' ); }

$content = $pg->post_content;
$changed = 0;

/** Swap $old for $new unless $new is already there. */
$patch = function ( $label, $old, $new ) use ( &$content, &$changed ) {
	// A deletion passes an empty $new, and strpos finds "" at position 0 in
	// anything, so the already-applied check has to skip that case or it
	// reports every deletion as done without doing it.
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

/* ------------------------------------------- Upcoming events, above the fold */

$events = <<<'HTML'
<h2 style="margin-top:46px;">Coming up on MLK Day</h2>
<p style="max-width:720px;color:var(--indigo-soft);">Every year the MLK Committee marks the holiday with worship and a meal, open to the whole county. <strong>MLK Day 2027 falls on Monday, January 18.</strong></p>
<div class="grid2">
   <div class="card">
    <h3>MLK Day worship service</h3>
    <p>A service of remembrance and recommitment, with guest speakers and music.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);"><em>Time, location and guests to be confirmed. Call [taum phone] or watch <a href="/news/">News and events</a>.</em></p>
   </div>
   <div class="card">
    <h3>MLK Day breakfast</h3>
    <p>Neighbors, congregations and community partners at one table before the service.</p>
    <p style="margin-top:10px;color:var(--indigo-soft);"><em>Time, location and ticketing to be confirmed. Call [taum phone] or watch <a href="/news/">News and events</a>.</em></p>
   </div>
</div>
<div class="prose" style="margin-top:22px;">
<p>The committee also organizes observances, conversations and community work through the rest of the year. To take part, host something, or be told when the MLK Day details are set, write to Rev. Abby Norton-Levering at [taum email_director].</p>
</div>

HTML;

$patch(
	'upcoming MLK Day events section added',
	'<h2 id="scholarship" style="margin-top:46px;">The Ronald J. Dukes Memorial MLK Scholarship</h2>',
	$events . '<h2 id="scholarship" style="margin-top:46px;">The Ronald J. Dukes Memorial MLK Scholarship</h2>
<p style="max-width:720px;color:var(--indigo-soft);">Established in 1997 to carry forward Dr. King\'s life and the nonviolent civil rights movement he taught. Renamed in 2015 for Ronald J. Dukes, who chaired the MLK Committee.</p>'
);

/* The old callout repeated what the events section now says better. */
$patch(
	'duplicate "More than the scholarship" callout removed',
	'<div class="callout" style="margin-top:30px;"><h3>More than the scholarship</h3><p>The MLK Committee\'s work across the county reaches well past this one award. If you would like to take part, host something, or hear what is planned, get in touch with Rev. Abby Norton-Levering at [taum email_director].</p></div>',
	''
);

/* ------------------------------------------------------ The application file */

$patch(
	'download link points at the real PDF',
	'<a href="https://taum.org/mlk-scholarship/">2026 MLK Scholarship Application (PDF)</a>',
	'<a href="https://taum.org/wp-content/uploads/2026/03/MLK-2026-Scholarship-Application-Form.pdf">2026 MLK Scholarship Application (PDF)</a>'
);

/* The PDF asks for a reference letter the page never mentioned. */
$patch(
	'non-resident reference letter added to the checklist',
	'<li><strong>Gather your materials</strong>, your essay, transcripts, and a recommendation from a school official or community leader</li>',
	'<li><strong>Gather your materials</strong>, your essay, your most recent transcripts, and a recommendation from a school official or community leader. If you live outside Rensselaer County, add a reference letter from your school or congregation</li>'
);

/* Say plainly that the form on offer is last cycle's. */
$patch(
	'notice that the 2027 form is not out yet',
	'<li><strong>Mail it postmarked by May 4</strong> to: MLK Scholarship Committee, 392 Second St., Troy, NY 12180</li>
  </ol>',
	'<li><strong>Mail it postmarked by the deadline</strong> to: MLK Scholarship Committee, 392 Second St., Troy, NY 12180</li>
  </ol>
  <div class="callout" style="margin-top:26px;">
   <h3>Applying for 2027</h3>
   <p>The form above is the 2026 application, and its deadline of May 4 has passed. The 2027 form and deadline are set in the new year. Write to [taum email_director] and we will send it to you as soon as it is ready.</p>
  </div>'
);

/* ------------------------------------------------------------- Recipients */

$recipients = <<<'HTML'
<h2 style="margin-top:46px;">Past recipients</h2>
  <p style="max-width:720px;color:var(--indigo-soft);">Every one of these students was already doing the work in their own neighborhood before we ever met them.</p>
  <div class="callout warm" style="margin-top:22px;">
   <h3>2026</h3>
   <p><strong>Chance Davis</strong> · <strong>Karalina Hastings</strong> · <strong>Ethan McMahon</strong> · <strong>Emma Waugh</strong></p>
  </div>
  <div class="prose" style="margin-top:18px;">
   <p>The scholarship has been awarded every year since 1997, and we are putting the earlier years back together from the committee's records. If you received this scholarship, or you know someone who did, we would be glad to hear from you: [taum email_director].</p>
  </div>
HTML;

$patch(
	'past recipients section replaces the 2026 congratulations box',
	'<div class="callout warm" style="margin-top:44px;">
   <h3>Congratulations to our 2026 scholars</h3>
   <p>Four students received the Ronald J. Dukes Memorial MLK Scholarship this year: <strong>Chance Davis</strong>, <strong>Karalina Hastings</strong>, <strong>Ethan McMahon</strong>, and <strong>Emma Waugh</strong>.</p>
  </div>',
	$recipients
);

if ( $changed ) {
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $content ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::success( "MLK page updated ($changed change" . ( 1 === $changed ? '' : 's' ) . ")." );
} else {
	WP_CLI::success( 'MLK page already up to date.' );
}
