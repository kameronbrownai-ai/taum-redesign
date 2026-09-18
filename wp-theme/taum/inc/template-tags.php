<?php
/**
 * Helpers used by templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * tel: href for the main line, with an optional extension using the pause
 * syntax so tapping actually dials the extension.
 */
function taum_tel( $ext = '' ) {
	$href = 'tel:+1' . preg_replace( '/\D/', '', taum_opt( 'phone_raw' ) );
	return $ext ? $href . ',,' . $ext : $href;
}

/**
 * The bird seal used in the header. Inline SVG so it needs no request.
 */
function taum_seal_svg() {
	return '<svg class="seal-sm" viewBox="0 0 48 48" aria-hidden="true">'
		. '<circle cx="24" cy="24" r="21.5" fill="none" stroke="#20264C" stroke-width="2.5"/>'
		. '<path d="M9 27c5-9 14-13 23-11-4 1-6 3-8 5 5-2 10-1 13 3-4-1-9 0-11 3 3 0 6 2 7 4-5-3-12-3-16 0-3 2-5 2-8-4z" fill="#20264C"/>'
		. '<path d="M27 17c1.5-1.5 4-2 6-1.5l-2 1.5c1.5 0 3 .8 4 2-2-.5-4-.5-6 0z" fill="#7C8C3F"/>'
		. '</svg>';
}

/**
 * Inner-page hero. Title from the page, lede from the excerpt, photo from
 * the featured image, background from the "Page hero" box.
 */
function taum_page_hero( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$kicker  = get_post_meta( $post_id, 'taum_kicker', true );
	$style   = get_post_meta( $post_id, 'taum_hero_style', true );
	$lede    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
	$photo   = has_post_thumbnail( $post_id );
	?>
	<div class="page-hero <?php echo esc_attr( $style ); ?>">
	 <div class="wrap <?php echo $photo ? 'hero-split-2' : ''; ?>">
	  <div>
	   <p class="kicker"><?php echo esc_html( $kicker ? $kicker : get_the_title( $post_id ) ); ?></p>
	   <h1><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
	   <?php if ( $lede ) : ?><p class="lede"><?php echo wp_kses_post( $lede ); ?></p><?php endif; ?>
	  </div>
	  <?php if ( $photo ) : ?>
	  <div class="page-hero-photo"><?php echo get_the_post_thumbnail( $post_id, 'taum-hero' ); ?></div>
	  <?php endif; ?>
	 </div>
	</div>
	<?php
}

/**
 * Events. Weekly ones first (they are the meals), then upcoming one-time
 * events by date. Past one-time events are excluded so the page never shows
 * something that already happened.
 */
function taum_get_events( $which = 'all' ) {
	$args = array(
		'post_type'      => 'taum_event',
		'posts_per_page' => 50,
		'post_status'    => 'publish',
		'meta_query'     => array( 'relation' => 'AND' ),
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	);
	if ( 'weekly' === $which ) {
		$args['meta_query'][] = array( 'key' => 'taum_event_recurring', 'value' => 'weekly' );
	} elseif ( 'upcoming' === $which ) {
		$args['meta_query'][] = array(
			'relation' => 'OR',
			array( 'key' => 'taum_event_recurring', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'taum_event_recurring', 'value' => '' ),
		);
		$args['meta_query'][] = array(
			'relation' => 'OR',
			array( 'key' => 'taum_event_date', 'value' => wp_date( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
			array( 'key' => 'taum_event_date', 'value' => '', 'compare' => '=' ),
			array( 'key' => 'taum_event_date', 'compare' => 'NOT EXISTS' ),
		);
		$args['meta_key'] = 'taum_event_date';
		$args['orderby']  = 'meta_value';
	}
	return get_posts( $args );
}

function taum_event_card( $post ) {
	$id   = $post->ID;
	$m    = function ( $k ) use ( $id ) { return get_post_meta( $id, $k, true ); };
	$rec  = (bool) $m( 'taum_event_recurring' );
	$date = $m( 'taum_event_date' );
	$url  = $m( 'taum_event_url' );
	$lbl  = $m( 'taum_event_url_label' );
	$time = $m( 'taum_event_time' );
	$place= $m( 'taum_event_place' );
	$when = trim( $time . ( $place ? ' · ' . $place : '' ) );
	?>
	<div class="evt <?php echo $rec ? 'recurring' : ''; ?>">
	 <div class="date">
	  <?php if ( $rec ) : ?>
	   <span class="mo"><?php echo '24/7' === $m( 'taum_event_day_label' ) ? 'Open' : 'Every'; ?></span><span class="dy"><?php echo esc_html( $m( 'taum_event_day_label' ) ); ?></span>
	  <?php elseif ( $date ) : $ts = strtotime( $date ); ?>
	   <span class="mo"><?php echo esc_html( date_i18n( 'M', $ts ) ); ?></span><span class="dy"><?php echo esc_html( date_i18n( 'j', $ts ) ); ?></span><span class="yr"><?php echo esc_html( date_i18n( 'Y', $ts ) ); ?></span>
	  <?php else : ?>
	   <span class="mo">Date</span><span class="dy">TBA</span>
	  <?php endif; ?>
	 </div>
	 <div>
	  <h3><?php echo esc_html( get_the_title( $post ) ); ?></h3>
	  <?php if ( $when ) : ?><p class="when"><?php echo esc_html( $when ); ?></p><?php endif; ?>
	  <?php echo wpautop( wp_kses_post( $post->post_content ) ); ?>
	  <?php if ( $url ) : ?>
	  <p class="actions"><a class="btn small" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $lbl ? $lbl : __( 'Details', 'taum' ) ); ?></a></p>
	  <?php endif; ?>
	 </div>
	</div>
	<?php
}

/**
 * Partners grouped by tier, for the About page.
 */
function taum_partners_by_tier() {
	$out   = array();
	$terms = get_terms( array( 'taxonomy' => 'taum_tier', 'hide_empty' => false, 'orderby' => 'term_id' ) );
	if ( is_wp_error( $terms ) ) {
		return $out;
	}
	foreach ( $terms as $t ) {
		$out[ $t->name ] = get_posts( array(
			'post_type'      => 'taum_partner',
			'posts_per_page' => 100,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'tax_query'      => array( array( 'taxonomy' => 'taum_tier', 'field' => 'term_id', 'terms' => $t->term_id ) ),
		) );
	}
	return $out;
}

/**
 * Homepage "Happening now": sticky posts first, then the latest, two total.
 */
function taum_home_posts() {
	$sticky = get_option( 'sticky_posts' );
	$posts  = array();
	if ( $sticky ) {
		$posts = get_posts( array( 'post__in' => $sticky, 'posts_per_page' => 2, 'ignore_sticky_posts' => 1 ) );
	}
	if ( count( $posts ) < 2 ) {
		$more = get_posts( array( 'posts_per_page' => 2 - count( $posts ), 'post__not_in' => wp_list_pluck( $posts, 'ID' ), 'ignore_sticky_posts' => 1 ) );
		$posts = array_merge( $posts, $more );
	}
	return $posts;
}

/**
 * Until a menu is assigned, list the main pages in the launch order.
 */
function taum_nav_fallback() {
	$slugs = array( 'food' => 'Food', 'furniture' => 'Furniture', 'programs' => 'Programs', 'news' => 'News', 'get-involved' => 'Get involved', 'about' => 'About', 'gallery' => 'Gallery', 'faq' => 'FAQ', 'contact' => 'Contact' );
	echo '<ul>';
	foreach ( $slugs as $slug => $label ) {
		echo '<li><a href="' . esc_url( home_url( '/' . $slug . '/' ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Turn *word* into <em>word</em> for the hero headline.
 */
function taum_em( $text ) {
	return preg_replace( '/\*([^*]+)\*/', '<em>$1</em>', esc_html( $text ) );
}
