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
	if ( 'email_office' === $key || 'email_director' === $key ) {
		$e = taum_opt( $key );
		return '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
	}
	return esc_html( taum_opt( $key ) );
} );
