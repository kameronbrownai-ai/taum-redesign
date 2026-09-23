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
