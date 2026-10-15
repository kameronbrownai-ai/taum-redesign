<?php
/**
 * Build out the Campus Chaplaincy page.
 *
 * Source: "Sage Grant Proposal 2027.docx", Abby's August 2026 application to
 * Albany Presbytery, shared 13 October with the note that it is more than we
 * needed to know. Everything on this page comes from Darren's annual report
 * inside that application. Nothing is invented.
 *
 * Deliberately left out of the public page: board member names and emails,
 * the funding breakdown and dollar figures, the reference list, the note about
 * revising the mission statement, and Darren's personal email and mobile. The
 * page keeps his Sage address and office line, which are already published by
 * the college. The funding reality is described in words because it is a real
 * and fair ask, but without the numbers until Abby says they can be public.
 *
 * The page has not been touched since the 18 September import, so the body is
 * replaced wholesale rather than patched. Re-running is safe: the first marker
 * check bails out if the new content is already in place.
 *
 *   wp eval-file ../import/chaplaincy.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$pg = get_page_by_path( 'chaplaincy' );
if ( ! $pg ) { WP_CLI::error( 'Chaplaincy page not found.' ); }

$content = <<<'HTML'
<!-- wp:html -->
<div class="prose">
<p>TAUM has kept a chaplain on the Russell Sage College campus since the ministry began. Darren Gundrum is the only chaplain at Sage, and the Spirituality Center he oversees is open to every student, every member of staff, and every tradition, including none at all. Chaplaincy here is about care, never conversion.</p>
<p>Students come from Catholic, Buddhist, Muslim, Protestant and secular backgrounds, and a good number arrive with no interest in religion whatsoever. That is welcome too. The work is pastoral counseling and theology, and it is also service projects, wellness support, meditation, yoga, and a safe room to sit in when the week has been long.</p>
</div>
<!-- /wp:html -->

<!-- wp:html -->
<h2 style="margin-top: 46px;">Every week</h2>
<div class="grid2">
<div class="card">
<h3>Open Discussion Table</h3>
<p><strong>Tuesdays at noon, Buchman.</strong> Come and say what is on your mind. No agenda, no sign-up. Different staff and faculty join the table each week.</p>
<p>It is also when you can write a gratitude slip for the gratitude tree painted on the wall of the Spirituality Center.</p>
</div>
<div class="card">
<h3>Mindfulness Mondays</h3>
<p><strong>At the Spirituality Center.</strong> A chance to start the week a little more centered and a little less wound up.</p>
</div>
<div class="card">
<h3>Office hours</h3>
<p>Darren is on campus ten hours a week, with hours set around student schedules rather than against them. Students drop in, and so do staff who need someone to talk to.</p>
</div>
</div>
<!-- /wp:html -->

<!-- wp:html -->
<h2 style="margin-top: 46px;">Through the year</h2>
<div class="grid2">
<div class="card">
<h3>De-stress bags at finals</h3>
<p>Handed out every finals week, and at other hard points in the semester, with tips and cards for getting through it.</p>
</div>
<div class="card">
<h3>Ashes to Go</h3>
<p>Offered on Ash Wednesday, to anyone who wants them.</p>
</div>
<div class="card">
<h3>Random Acts of Kindness Day</h3>
<p>Suggestion slips written for college life, plus kindness buttons and plushies.</p>
</div>
<div class="card">
<h3>Meditation classes</h3>
<p>Offered whenever someone asks for them.</p>
</div>
<div class="card">
<h3>The Sage food pantry</h3>
<p>We supply food to Gator Claws-it, the student-run pantry at Sage, and students come to TAUM to help put it together.</p>
</div>
<div class="card">
<h3>Campus partnerships</h3>
<p>Darren works alongside Sage's wellness and academic teams, joins the weekly Campus Coffee Hour, and builds programs with faculty across departments.</p>
</div>
</div>
<!-- /wp:html -->

<!-- wp:html -->
<div class="callout" style="margin-top: 46px;">
<h3>There is no playbook any more</h3>
<p>Very few students arrive at Sage looking for an established campus ministry to join. So Darren builds relationships one at a time, finds out what each student actually needs, and walks alongside them. Students who are curious about difference, and students who are wary of faith entirely, both get the same welcome. Between 150 and 200 students, faculty and congregation members took part last year.</p>
</div>
<!-- /wp:html -->

<!-- wp:html -->
<div class="callout warm" style="margin-top: 34px;">
<h3>How congregations can help</h3>
<p>Campus Ministry is the one program TAUM runs that cannot receive public funding. It exists because denominations, congregations and individuals choose to pay for it. There are four straightforward ways to help:</p>
<ul style="padding-left: 20px; margin-top: 10px; color: var(--indigo-soft);">
<li>Put together care packages for students</li>
<li>Donate food to the Sage pantry</li>
<li>Run a toiletries drive, because food is not the only thing students run short of</li>
<li>Help assemble the de-stress bags near the end of a semester</li>
</ul>
<p style="margin-top: 14px;"><a class="btn small" href="/donate/">Support Campus Ministry</a> <a class="btn ghost small" href="/contact/">Get in touch</a></p>
</div>
<!-- /wp:html -->

<!-- wp:html -->
<div class="callout" style="max-width: 560px; margin-top: 34px;">
<h3>Talk with the chaplain</h3>
<p><strong>Darren Gundrum</strong>, Chaplain and Spiritual Advisor<br>518-244-4522 · <a href="mailto:gundrd@sage.edu">gundrd@sage.edu</a></p>
<p style="margin-top: 10px;">Darren also speaks to congregations and groups about the ministry, in person or over Zoom. Ask him directly.</p>
</div>
<!-- /wp:html -->
HTML;

if ( false !== strpos( $pg->post_content, 'Open Discussion Table' ) ) {
	WP_CLI::log( '  body already rebuilt, leaving it alone' );
} else {
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $content ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::log( '  body rebuilt from the Sage proposal' );
}

/* The old intro said "de-stress meditation sessions" and "care packages during
   finals", which blurred two separate things. Darren runs meditation classes on
   request and hands out de-stress bags at finals. */
$excerpt = 'Darren Gundrum is the chaplain at Russell Sage College, and the Spirituality Center he oversees is open to every student and every tradition, including none at all. Office hours, a weekly discussion table, Mindfulness Mondays, meditation classes, and de-stress bags when finals come around. Care, never conversion.';

$r = wp_update_post( wp_slash( array(
	'ID'           => $pg->ID,
	'post_excerpt' => $excerpt,
) ), true );
if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
WP_CLI::log( '  intro updated' );

update_post_meta( $pg->ID, 'taum_kicker', 'Campus chaplaincy · Russell Sage College' );

WP_CLI::success( 'Chaplaincy page built out.' );
