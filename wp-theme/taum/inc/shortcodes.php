<?php
/**
 * [taum key] prints a site fact from the TAUM Info panel, so page copy like
 * "Meals are served [taum meal_monday]" stays correct when the time changes.
 *
 * [taum tel ext="204"] prints a tap-to-call link with the extension.
 * [taum year] prints the current year.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Excerpts carry shortcodes too: they become the hero intro on every page and
 * the tile descriptions on the homepage. Without this, a [taum phone] in an
 * excerpt prints literally. Runs after wp_trim_excerpt.
 */
add_filter( 'get_the_excerpt', 'do_shortcode', 11 );

add_shortcode( 'taum', function ( $atts ) {
	$atts = shortcode_atts( array( 0 => '', 'ext' => '', 'label' => '' ), $atts );
	$key  = $atts[0];

	if ( 'year' === $key ) {
		return wp_date( 'Y' );
	}
	if ( 'tel' === $key ) {
		$label = $atts['label'] ? $atts['label'] : taum_opt( 'phone' ) . ( $atts['ext'] ? ' ext. ' . $atts['ext'] : '' );
		return '<a href="' . esc_attr( taum_tel( $atts['ext'] ) ) . '">' . esc_html( $label ) . '</a>';
	}
	// A "_raw" suffix returns the bare value, for use inside an attribute such as
	// a mailto: href, where the linked version would nest a tag inside a tag.
	if ( substr( $key, -4 ) === '_raw' ) {
		return esc_attr( taum_opt( substr( $key, 0, -4 ) ) );
	}
	if ( 'email_office' === $key || 'email_director' === $key ) {
		$e = taum_opt( $key );
		return '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
	}
	return esc_html( taum_opt( $key ) );
} );

/**
 * [taum_volunteer_form] embeds the Little Green Light volunteer sign-up.
 *
 * Abby sent the embed code on 13 October. It lives here rather than in the
 * page body for two reasons: WordPress strips script tags from content saved
 * by anyone without unfiltered_html, which is every editor account on this
 * site, and keeping it in the theme means a stray keystroke in the editor
 * cannot break the form.
 *
 * The script URL is the form URL with .js on the end, which is how LGL builds
 * them, so the "Volunteer form" field in TAUM Info drives both the embed and
 * the fallback link and there is no second copy to keep in step.
 *
 * It loads only on pages that use the shortcode, not site-wide.
 */
add_shortcode( 'taum_volunteer_form', function () {
	$url = taum_opt( 'url_volunteer' );
	if ( ! $url ) {
		return '';
	}

	ob_start();
	?>
	<div class="lgl-form" id="volunteer-form">
	 <script type="text/javascript" src="<?php echo esc_url( $url . '.js' ); ?>"></script>
	 <noscript>
	  <p><?php
		  printf(
			  /* translators: %s: link to the volunteer form */
			  esc_html__( 'The sign-up form needs JavaScript. %s, or call the office and we will take your details over the phone.', 'taum' ),
			  '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Open the form in a new page', 'taum' ) . '</a>'
		  );
		  ?></p>
	 </noscript>
	</div>
	<?php
	return ob_get_clean();
} );
