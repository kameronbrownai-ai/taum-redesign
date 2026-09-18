<?php
/**
 * The "TAUM Info" Customizer panel.
 *
 * One place for every fact that appears on more than one page: phone,
 * address, hours, meal times, emails, form links, impact numbers, and the
 * homepage hero and campaign. Templates read these with taum_opt() and page
 * copy can reference them with the [taum key] shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions. Key => [label, default, type, section].
 * Keep defaults equal to the launch content so a fresh install looks right.
 */
function taum_option_fields() {
	return array(
		// Contact
		'phone'         => array( 'Main phone', '(518) 274-5920', 'text', 'contact' ),
		'phone_raw'     => array( 'Main phone, digits only (for tap-to-call)', '5182745920', 'text', 'contact' ),
		'address1'      => array( 'Street address', '392 2nd Street', 'text', 'contact' ),
		'address2'      => array( 'City, state, ZIP', 'Troy, NY 12180', 'text', 'contact' ),
		'hours'         => array( 'Office hours', 'Monday to Friday, 9 am to 4 pm', 'text', 'contact' ),
		'email_office'  => array( 'Office email', 'lmalatesta@taum.org', 'email', 'contact' ),
		'email_director'=> array( 'Executive Director email', 'nortonlevering@taum.org', 'email', 'contact' ),
		'facebook'      => array( 'Facebook URL', 'https://www.facebook.com/TroyAreaUnitedMinistries/', 'url', 'contact' ),
		'instagram'     => array( 'Instagram URL', 'https://www.instagram.com/taum518/', 'url', 'contact' ),

		// Food
		'meal_monday'   => array( 'Monday meal', 'Monday evenings at 5:45', 'text', 'food' ),
		'meal_monday_short' => array( 'Monday meal, short', 'Monday 5:45 pm', 'text', 'food' ),
		'meal_thursday' => array( 'Thursday meal', 'Thursdays at noon', 'text', 'food' ),
		'meal_thursday_short' => array( 'Thursday meal, short', 'Thursday noon', 'text', 'food' ),
		'groceries'     => array( 'Groceries', 'Thursdays at noon', 'text', 'food' ),
		'fridge'        => array( 'Free Food Fridge hours', '24 hours a day, every day', 'text', 'food' ),

		// Links
		'url_donate'    => array( 'Online giving page', 'https://taum.org/donate/', 'url', 'links' ),
		'url_newsletter'=> array( 'Newsletter signup', 'https://secure.lglforms.com/form_engine/s/zmQHJr8EUVLsa5i9JZzIrw', 'url', 'links' ),
		'url_volunteer' => array( 'Volunteer form', 'https://forms.gle/BWSMYFMgri5MybR97', 'url', 'links' ),
		'url_garden'    => array( 'Garden campaign giving page', 'https://secure.lglforms.com/form_engine/s/Kr1X6bDWzYn2WsP_7ufiYw', 'url', 'links' ),
		'url_flames'    => array( 'Forty Flames form', 'https://docs.google.com/forms/d/e/1FAIpQLSc3lvfd-69c7KV95N8lchVQaoVPV_EZ4qFsUpPTWqsr_oxoLA/viewform', 'url', 'links' ),
		'url_syep'      => array( 'County Summer Youth Employment page', 'https://www.rensco.com/406/Youth', 'url', 'links' ),

		// Homepage
		'hero_kicker'   => array( 'Hero kicker', 'Troy Area United Ministries · Est. 1986', 'text', 'home' ),
		'hero_title'    => array( 'Hero headline (wrap a word in *asterisks* to color it)', 'A neighborhood center where *everyone* belongs.', 'text', 'home' ),
		'hero_lede'     => array( 'Hero paragraph (the mission)', "Rooted in Troy's diverse interfaith community and open to all, TAUM offers food and assistance for those facing health challenges, homelessness and hard times, furniture for families making a fresh start, tech job training and scholarships for teens, spiritual support for Sage College students, and space for neighbors to gather, organize and advocate for justice, healing and peace.", 'textarea', 'home' ),
		'now_kicker'    => array( '"Happening now" kicker', 'Happening now', 'text', 'home' ),
		'now_title'     => array( '"Happening now" heading', "What's growing at TAUM", 'text', 'home' ),

		// Campaign block
		'campaign_on'    => array( 'Show the campaign section', '1', 'checkbox', 'campaign' ),
		'campaign_kicker'=> array( 'Kicker', '2026 · Our 40th year', 'text', 'campaign' ),
		'campaign_title' => array( 'Heading', 'Forty years. Forty flames. One neighborhood.', 'text', 'campaign' ),
		'campaign_text'  => array( 'Paragraph', 'TAUM turns 40 this year, and we\'re celebrating all year long. We\'re looking for forty friends (people, families, congregations, businesses) to each "light a candle" by hosting something for the community: a dinner party, a movie night, a garden build, an ice cream social, a fresh coat of paint on a wall that needs it.', 'textarea', 'campaign' ),
		'campaign_btn'   => array( 'Button text', 'Light a flame', 'text', 'campaign' ),
		'campaign_url'   => array( 'Button link', 'https://docs.google.com/forms/d/e/1FAIpQLSc3lvfd-69c7KV95N8lchVQaoVPV_EZ4qFsUpPTWqsr_oxoLA/viewform', 'url', 'campaign' ),
		'campaign_badge' => array( 'Badge number', '40', 'text', 'campaign' ),
		'campaign_badge_text' => array( 'Badge caption', 'years of showing up|for the people of Troy', 'text', 'campaign' ),

		// Impact stats
		'stat1_n' => array( 'Stat 1 number', '2000', 'text', 'stats' ),
		'stat1_s' => array( 'Stat 1 suffix', '+', 'text', 'stats' ),
		'stat1_l' => array( 'Stat 1 label', 'neighbors served every year', 'text', 'stats' ),
		'stat2_n' => array( 'Stat 2 number', '14000', 'text', 'stats' ),
		'stat2_s' => array( 'Stat 2 suffix', '', 'text', 'stats' ),
		'stat2_l' => array( 'Stat 2 label', 'meals shared from our kitchen', 'text', 'stats' ),
		'stat3_n' => array( 'Stat 3 number', '300', 'text', 'stats' ),
		'stat3_s' => array( 'Stat 3 suffix', '', 'text', 'stats' ),
		'stat3_l' => array( 'Stat 3 label', 'households furnished', 'text', 'stats' ),
		'stat4_n' => array( 'Stat 4 number', '40', 'text', 'stats' ),
		'stat4_s' => array( 'Stat 4 suffix', '', 'text', 'stats' ),
		'stat4_l' => array( 'Stat 4 label', 'teens trained & employed each summer', 'text', 'stats' ),
	);
}

function taum_option_sections() {
	return array(
		'contact'  => 'Contact & hours',
		'food'     => 'Meal & grocery times',
		'links'    => 'Forms & links',
		'home'     => 'Homepage hero',
		'campaign' => 'Homepage campaign block',
		'stats'    => 'Impact numbers',
	);
}

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_panel( 'taum_info', array(
		'title'       => __( 'TAUM Info', 'taum' ),
		'description' => __( 'Facts that appear across the site. Change them here once.', 'taum' ),
		'priority'    => 1,
	) );

	foreach ( taum_option_sections() as $id => $label ) {
		$wp_customize->add_section( 'taum_' . $id, array(
			'title' => $label,
			'panel' => 'taum_info',
		) );
	}

	foreach ( taum_option_fields() as $key => $f ) {
		list( $label, $default, $type, $section ) = $f;
		$sanitize = 'sanitize_text_field';
		if ( 'url' === $type )      { $sanitize = 'esc_url_raw'; }
		if ( 'email' === $type )    { $sanitize = 'sanitize_email'; }
		if ( 'textarea' === $type ) { $sanitize = 'wp_kses_post'; }
		if ( 'checkbox' === $type ) { $sanitize = function ( $v ) { return $v ? '1' : ''; }; }

		$wp_customize->add_setting( 'taum_' . $key, array(
			'default'           => $default,
			'type'              => 'theme_mod',
			'capability'        => 'edit_pages',
			'sanitize_callback' => $sanitize,
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'taum_' . $key, array(
			'label'   => $label,
			'section' => 'taum_' . $section,
			'type'    => $type,
		) );
	}
} );

/**
 * Read a site fact. Falls back to the launch default so nothing renders empty.
 */
function taum_opt( $key ) {
	$fields = taum_option_fields();
	$default = isset( $fields[ $key ] ) ? $fields[ $key ][1] : '';
	$v = get_theme_mod( 'taum_' . $key, $default );
	return is_string( $v ) ? $v : $default;
}
