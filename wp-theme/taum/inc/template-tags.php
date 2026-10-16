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
	  <?php
	  /**
	   * A time with no place reads as complete and sends someone to the wrong
	   * building. Say so plainly and give them a number instead.
	   */
	  if ( ! $rec && ! $place ) : ?>
	  <p class="when" style="color:var(--coral-dark);"><?php
		  printf(
			  /* translators: %s: phone link */
			  esc_html__( 'Location to be confirmed, call %s', 'taum' ),
			  '<a href="' . esc_attr( taum_tel() ) . '">' . esc_html( taum_opt( 'phone' ) ) . '</a>'
		  );
	  ?></p>
	  <?php endif; ?>
	  <?php echo wpautop( wp_kses_post( $post->post_content ) ); ?>
	  <?php if ( $url ) : ?>
	  <p class="actions"><a class="btn small" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $lbl ? $lbl : __( 'Details', 'taum' ) ); ?></a></p>
	  <?php endif; ?>
	 </div>
	</div>
	<?php
}

/**
 * The event for the homepage spotlight.
 *
 * The slot exists to show a flyer, so the soonest upcoming event that HAS one
 * wins. Only if nothing upcoming has a flyer does it fall back to the soonest
 * event of any kind, rather than leaving the slot empty.
 */
function taum_next_event() {
	$upcoming = get_posts( array(
		'post_type'      => 'taum_event',
		'posts_per_page' => 20,
		'post_status'    => 'publish',
		'meta_key'       => 'taum_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array( 'key' => 'taum_event_date', 'value' => wp_date( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
			array(
				'relation' => 'OR',
				array( 'key' => 'taum_event_recurring', 'compare' => 'NOT EXISTS' ),
				array( 'key' => 'taum_event_recurring', 'value' => '' ),
			),
		),
	) );
	if ( ! $upcoming ) {
		return null;
	}
	foreach ( $upcoming as $ev ) {
		if ( has_post_thumbnail( $ev ) ) {
			return $ev;
		}
	}
	return $upcoming[0];
}

/**
 * Flyers from events that have already happened. The gallery shows these, so
 * a flyer moves from the homepage to the archive on its own the day after.
 */
function taum_past_event_flyers( $limit = 24 ) {
	$out = array();
	$past = get_posts( array(
		'post_type'      => 'taum_event',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'meta_key'       => 'taum_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
		'meta_query'     => array(
			array( 'key' => 'taum_event_date', 'value' => wp_date( 'Y-m-d' ), 'compare' => '<', 'type' => 'DATE' ),
		),
	) );
	foreach ( $past as $ev ) {
		if ( has_post_thumbnail( $ev ) ) {
			$out[] = $ev;
		}
	}
	return $out;
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

/**
 * The programs, in the order we present them, with the short name and icon
 * used wherever they are listed.
 *
 * One list, used by both the homepage tiles and the Programs page. They used
 * to carry their own copies, and when the MLK page moved from
 * /mlk-scholarship/ to /mlk/ the homepage tile vanished without a word,
 * because a missing page is skipped silently. Shared list, one place to fix.
 *
 * A program only appears if its page exists and is published.
 */
function taum_programs( $where = 'all' ) {
	$all = array(
		'food' => array(
			'label' => __( 'Food', 'taum' ),
			'cta'   => __( 'Get food', 'taum' ),
			'tone'  => 't-indigo',
			'accent'=> 'var(--teal-deep)',
			'icon'  => '<path d="M4 12h16a8 8 0 0 1-16 0z"/><path d="M9 8c0-2 2-2 2-4"/><path d="M14 8c0-2 2-2 2-4"/>',
		),
		'furniture' => array(
			'label' => __( 'Furniture', 'taum' ),
			'cta'   => __( 'Get or give furniture', 'taum' ),
			'tone'  => 't-coral',
			'accent'=> 'var(--coral-dark)',
			'icon'  => '<path d="M3 19v-9"/><path d="M3 14h18v5"/><path d="M21 14v-3a2 2 0 0 0-2-2h-9v5"/><circle cx="6.5" cy="11.5" r="1.5"/>',
		),
		'tech-for-teens' => array(
			'label' => __( 'Tech for Teens', 'taum' ),
			'cta'   => __( 'Learn more', 'taum' ),
			'tone'  => 't-teal',
			'accent'=> 'var(--magenta-deep)',
			'icon'  => '<rect x="5" y="5" width="14" height="10" rx="1.5"/><path d="M3 19h18"/>',
		),
		'damien-center' => array(
			'label' => __( 'The Damien Center', 'taum' ),
			'cta'   => __( 'Find support', 'taum' ),
			// Abby, 15 October: off the front page, but keep the page itself
			// and its place in Programs.
			'home'  => false,
			'tone'  => 't-magenta',
			'accent'=> 'var(--olive-deep)',
			'icon'  => '<path d="M12 20s-7-4.5-7-9.5A4 4 0 0 1 12 7.5 4 4 0 0 1 19 10.5C19 15.5 12 20 12 20z"/>',
		),
		'community-center' => array(
			'label' => __( 'Community Center', 'taum' ),
			'cta'   => __( 'Use the space', 'taum' ),
			'tone'  => 't-slate',
			'accent'=> 'var(--teal-deep)',
			'icon'  => '<path d="M3 11 12 4l9 7"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
		),
		'chaplaincy' => array(
			'label' => __( 'Campus Chaplaincy', 'taum' ),
			'cta'   => __( 'Meet the chaplain', 'taum' ),
			'tone'  => 't-olive',
			'accent'=> 'var(--coral-dark)',
			'icon'  => '<path d="M3 12c2.5-3 5-3 7.5 0"/><path d="M13.5 12c2.5-3 5-3 7.5 0"/><path d="M8 17c2-2.5 6-2.5 8 0"/>',
		),
		'mlk' => array(
			'label' => __( 'MLK Programs', 'taum' ),
			'cta'   => __( 'See the programs', 'taum' ),
			'tone'  => 't-teal',
			'accent'=> 'var(--magenta-deep)',
			'icon'  => '<path d="M12 5 2 10l10 5 10-5-10-5z"/><path d="M6 12.5V17c4 2 8 2 12 0v-4.5"/><path d="M22 10v5"/>',
		),
	);

	$out = array();
	foreach ( $all as $slug => $p ) {
		$page = get_page_by_path( $slug );
		if ( ! $page || 'publish' !== $page->post_status ) {
			continue;
		}
		// 'home' => false keeps a program off the homepage tiles while leaving
		// it everywhere else. Anything without the key is shown in both.
		if ( 'home' === $where && isset( $p['home'] ) && ! $p['home'] ) {
			continue;
		}
		$p['page'] = $page;
		$p['url']  = get_permalink( $page );
		$out[ $slug ] = $p;
	}
	return $out;
}

/**
 * "Who to ask" for a program page, assembled from its own meta so a changed
 * extension is changed in one place rather than on every page that prints it.
 * Returns an empty array when nobody has been named yet, so callers can leave
 * the block out rather than print an empty heading.
 */
function taum_program_contact( $post_id ) {
	$name  = get_post_meta( $post_id, 'taum_contact_name', true );
	$ext   = get_post_meta( $post_id, 'taum_contact_ext', true );
	$phone = get_post_meta( $post_id, 'taum_contact_phone', true );
	$email = get_post_meta( $post_id, 'taum_contact_email', true );

	if ( ! $name && ! $ext && ! $phone && ! $email ) {
		return array();
	}

	$lines = array();
	if ( $phone ) {
		$lines[] = '<a href="tel:+1' . preg_replace( '/\D/', '', $phone ) . '">' . esc_html( $phone ) . '</a>';
	} elseif ( $ext ) {
		$lines[] = '<a href="' . esc_attr( taum_tel( $ext ) ) . '">' . esc_html( taum_opt( 'phone' ) . ' ext. ' . $ext ) . '</a>';
	}
	if ( $email ) {
		$lines[] = '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
	}

	return array(
		'name'  => $name,
		'lines' => $lines,
	);
}

/**
 * Small numbers as words, so a heading reads "Seven ways we show up" rather
 * than "7 ways". Falls back to the digits past ten, which we will not reach.
 */
function taum_number_word( $n ) {
	$words = array( 'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten' );
	return isset( $words[ $n ] ) ? $words[ $n ] : (string) $n;
}

/**
 * The TAUM logo, as uploaded by the client.
 *
 * Read from the WordPress site icon, which is where it already has to live to
 * produce the favicon and the home-screen icon, so there is one copy and
 * replacing it in Settings replaces it everywhere the theme prints it.
 * Returns an empty string if nobody has set one, and callers skip the mark.
 */
function taum_logo_url( $size = 512 ) {
	return function_exists( 'get_site_icon_url' ) ? (string) get_site_icon_url( $size ) : '';
}
