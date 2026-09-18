<?php
/**
 * Small edit-screen panels for the fields that templates need.
 *
 * Plain meta boxes rather than a block-editor plugin: they work in every
 * editor mode, need no build step, and give Abby labeled fields with help
 * text instead of a JSON blob.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'taum_hero', __( 'Page hero', 'taum' ), 'taum_mb_hero', 'page', 'side', 'high' );
	add_meta_box( 'taum_event', __( 'When and where', 'taum' ), 'taum_mb_event', 'taum_event', 'normal', 'high' );
	add_meta_box( 'taum_partner', __( 'Partner details', 'taum' ), 'taum_mb_partner', 'taum_partner', 'normal', 'high' );
} );

function taum_mb_field( $id, $label, $value, $type = 'text', $help = '', $options = array() ) {
	echo '<p style="margin:10px 0 4px;"><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $label ) . '</strong></label></p>';
	if ( 'select' === $type ) {
		echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" style="width:100%;">';
		foreach ( $options as $k => $v ) {
			echo '<option value="' . esc_attr( $k ) . '"' . selected( $value, $k, false ) . '>' . esc_html( $v ) . '</option>';
		}
		echo '</select>';
	} elseif ( 'checkbox' === $type ) {
		echo '<label><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="1"' . checked( $value, '1', false ) . '> ' . esc_html( $help ) . '</label>';
		$help = '';
	} else {
		echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $value ) . '" style="width:100%;">';
	}
	if ( $help ) {
		echo '<p class="description" style="margin-top:4px;">' . esc_html( $help ) . '</p>';
	}
}

function taum_mb_hero( $post ) {
	wp_nonce_field( 'taum_mb', 'taum_mb_nonce' );
	taum_mb_field( 'taum_kicker', __( 'Kicker (small label above the title)', 'taum' ), get_post_meta( $post->ID, 'taum_kicker', true ), 'text', __( 'Leave blank to use the page title.', 'taum' ) );
	taum_mb_field( 'taum_hero_style', __( 'Hero background', 'taum' ), get_post_meta( $post->ID, 'taum_hero_style', true ), 'select', __( 'The excerpt is the intro paragraph. The featured image is the hero photo.', 'taum' ), array(
		''             => __( 'Aqua (default)', 'taum' ),
		'ph-aqua-pale' => __( 'Pale aqua', 'taum' ),
		'ph-peach'     => __( 'Peach', 'taum' ),
		'ph-olive'     => __( 'Olive', 'taum' ),
		'ph-cream'     => __( 'Cream', 'taum' ),
	) );
}

function taum_mb_event( $post ) {
	wp_nonce_field( 'taum_mb', 'taum_mb_nonce' );
	$m = function ( $k ) use ( $post ) { return get_post_meta( $post->ID, $k, true ); };
	echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0 24px;">';
	echo '<div>';
	taum_mb_field( 'taum_event_recurring', __( 'Repeats every week', 'taum' ), $m( 'taum_event_recurring' ), 'checkbox', __( 'Tick for weekly things like the Monday meal. Leave off for one-time events.', 'taum' ) );
	taum_mb_field( 'taum_event_day_label', __( 'Day (weekly events only)', 'taum' ), $m( 'taum_event_day_label' ), 'text', __( 'Short, e.g. Mon, Thu, or 24/7.', 'taum' ) );
	taum_mb_field( 'taum_event_date', __( 'Date (one-time events)', 'taum' ), $m( 'taum_event_date' ), 'date', __( 'Past events drop off the page automatically.', 'taum' ) );
	echo '</div><div>';
	taum_mb_field( 'taum_event_time', __( 'Time', 'taum' ), $m( 'taum_event_time' ), 'text', __( 'Free text, e.g. 5:45 pm or Noon.', 'taum' ) );
	taum_mb_field( 'taum_event_place', __( 'Place', 'taum' ), $m( 'taum_event_place' ), 'text', __( 'e.g. 392 Second Street', 'taum' ) );
	taum_mb_field( 'taum_event_url', __( 'Sign-up or details link', 'taum' ), $m( 'taum_event_url' ), 'url', __( 'A form, a ticket page, or a contact page.', 'taum' ) );
	taum_mb_field( 'taum_event_url_label', __( 'Button text', 'taum' ), $m( 'taum_event_url_label' ), 'text', __( 'e.g. Register, Light a flame, Get tickets.', 'taum' ) );
	echo '</div></div>';
	echo '<p class="description" style="margin-top:12px;">' . esc_html__( 'Use the main editor above for a sentence or two about the event. The excerpt is not used.', 'taum' ) . '</p>';
}

function taum_mb_partner( $post ) {
	wp_nonce_field( 'taum_mb', 'taum_mb_nonce' );
	taum_mb_field( 'taum_partner_url', __( 'Website (optional)', 'taum' ), get_post_meta( $post->ID, 'taum_partner_url', true ), 'url', __( 'Pick a Tier on the right: Program partners, or Sponsors & funders.', 'taum' ) );
}

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['taum_mb_nonce'] ) || ! wp_verify_nonce( $_POST['taum_mb_nonce'], 'taum_mb' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$text = array( 'taum_kicker', 'taum_event_day_label', 'taum_event_date', 'taum_event_time', 'taum_event_place', 'taum_event_url_label' );
	foreach ( $text as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
	foreach ( array( 'taum_event_url', 'taum_partner_url' ) as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, esc_url_raw( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
	if ( isset( $_POST['taum_hero_style'] ) ) {
		update_post_meta( $post_id, 'taum_hero_style', sanitize_key( $_POST['taum_hero_style'] ) );
	}
	if ( 'taum_event' === get_post_type( $post_id ) ) {
		update_post_meta( $post_id, 'taum_event_recurring', empty( $_POST['taum_event_recurring'] ) ? '' : 'weekly' );
	}
} );
