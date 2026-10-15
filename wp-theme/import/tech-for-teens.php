<?php
/**
 * Tech for Teens: five weeks, and the notify-me box goes.
 *
 * Abby called the program five weeks rather than six in the 13 October
 * review, as the safer number to publish.
 *
 * The notify-me box promised to email people the moment applications opened,
 * but its button went to the general newsletter form, so anyone who used it
 * was signed up for the monthly newsletter and would never have been told
 * about applications at all. Rather than build a notification list nobody
 * asked for, the box goes and the fact it carried, that applications open
 * each spring, moves down to the line that already sends people to the
 * county program, which is where applications actually happen.
 *
 * That county link was hardcoded here while the same URL sits in TAUM Info,
 * so it now reads from there.
 *
 *   wp eval-file ../import/tech-for-teens.php
 */
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }

$pg = get_page_by_path( 'tech-for-teens' );
if ( ! $pg ) { WP_CLI::error( 'Tech for Teens page not found.' ); }

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
	'five weeks, not six',
	'<div class="v">Six weeks each summer, July to August, weekday mornings</div>',
	'<div class="v">Five weeks each summer, July to August, weekday mornings</div>'
);

$patch(
	'notify-me box removed',
	'<div class="callout">
   <h3>Applications open each spring</h3>
   <p>Sign up below and we\'ll email you the moment applications open. No need to keep checking back.</p>
   <a class="btn" style="margin-top:14px;" href="https://secure.lglforms.com/form_engine/s/zmQHJr8EUVLsa5i9JZzIrw">Notify me when applications open</a>
  </div>

  ',
	''
);

$patch(
	'applications line carries the timing and reads the county link from TAUM Info',
	'<p><strong>Employment applications</strong> go through the <a href="https://www.rensco.com/406/Youth">Rensselaer County Summer Youth Employment Program</a>.</p>',
	'<p><strong>Applications open each spring.</strong> They go through the <a href="[taum url_syep_raw]">Rensselaer County Summer Youth Employment Program</a>, not through us, so watch their page when the time comes. Call [taum phone] x202 if you are not sure where to start.</p>'
);

/* The questions line below now repeats the phone number the line above gives. */
$patch(
	'duplicate questions line removed',
	'<p><strong>Questions?</strong> Call [taum phone] x202.</p>
   ',
	''
);

if ( $changed ) {
	$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => trim( $content ) ) ), true );
	if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
	WP_CLI::success( "Tech for Teens updated ($changed change" . ( 1 === $changed ? '' : 's' ) . ")." );
} else {
	WP_CLI::success( 'Tech for Teens already up to date.' );
}
