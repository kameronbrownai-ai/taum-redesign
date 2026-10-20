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

/**
 * What the concierge knows, assembled from the site itself.
 *
 * The concierge used to carry a frozen copy of every answer, so it drifted
 * every time a page changed and somebody had to notice. Now it is handed the
 * program pages and the FAQ at load, and its answers follow whatever Abby
 * has written. Only the handful of things with no page behind them, a crisis
 * response, "do I have to be religious", greetings, stay written in the JS.
 */
function taum_concierge_data() {
	$pages = array();
	foreach ( taum_programs() as $slug => $p ) {
		$page    = $p['page'];
		$contact = taum_program_contact( $page->ID );

		$line = '';
		if ( $contact ) {
			$line = $contact['name'];
			if ( $contact['lines'] ) {
				// The contact helper returns anchors; the concierge wants words.
				$line .= ': ' . trim( wp_strip_all_tags( implode( ', ', $contact['lines'] ) ) );
			}
		}

		$pages[ $slug ] = array(
			'label'   => $p['label'],
			'title'   => get_the_title( $page ),
			'intro'   => has_excerpt( $page ) ? wp_strip_all_tags( get_the_excerpt( $page ) ) : '',
			'url'     => $p['url'],
			'cta'     => $p['cta'],
			'contact' => $line,
		);
	}

	// The FAQ accordion, the same pairs seo.php turns into FAQPage schema.
	$faq  = array();
	$page = get_page_by_path( 'faq' );
	if ( $page ) {
		$content = do_shortcode( $page->post_content );
		if ( preg_match_all( '#<summary>(.*?)</summary>\s*<div class="a">(.*?)</div>#s', $content, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $pair ) {
				$q = trim( wp_strip_all_tags( $pair[1] ) );
				$a = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $pair[2] ) ) );
				if ( $q && $a ) {
					$faq[] = array( 'q' => $q, 'a' => $a );
				}
			}
		}
	}

	/* The index is built from the words on each page, so a new page is
	   findable by what it says, not only by what it is called. */
	$raw = array();
	foreach ( taum_programs() as $slug => $p ) {
		$page = $p['page'];
		$raw[ $slug ] = array(
			'strong' => $p['label'] . ' ' . get_the_title( $page ) . ' ' . ( has_excerpt( $page ) ? get_the_excerpt( $page ) : '' ),
			'body'   => wp_strip_all_tags( do_shortcode( $page->post_content ) ),
		);
	}
	/* The resource page is not a programme but it is where several questions
	   should land, so it is indexed alongside them. */
	$compass = get_page_by_path( 'community-compass' );
	if ( $compass ) {
		$pages['community-compass'] = array(
			'label'   => 'Other services in Troy',
			'title'   => get_the_title( $compass ),
			'intro'   => has_excerpt( $compass ) ? wp_strip_all_tags( get_the_excerpt( $compass ) ) : '',
			'url'     => get_permalink( $compass ),
			'cta'     => 'See the lists',
			'contact' => '',
		);
		$raw['community-compass'] = array(
			'strong' => get_the_title( $compass ) . ' ' . ( has_excerpt( $compass ) ? get_the_excerpt( $compass ) : '' ),
			'body'   => wp_strip_all_tags( do_shortcode( $compass->post_content ) ),
		);
	}

	return array(
		'pages' => $pages,
		'faq'   => $faq,
		'index' => taum_concierge_index( $raw ),
	);
}

/**
 * Stem a word just enough that singular and plural stop being different
 * things. Not a real stemmer: "classes" and "class", "pantries" and "pantry",
 * "beds" and "bed". That one difference caused most of the misses.
 */
function taum_stem( $w ) {
	$w = strtolower( $w );
	if ( strlen( $w ) > 4 && substr( $w, -3 ) === 'ies' ) { return substr( $w, 0, -3 ) . 'y'; }
	if ( strlen( $w ) > 4 && substr( $w, -2 ) === 'es' && ! in_array( substr( $w, -3, 1 ), array( 'a','e','i','o','u' ), true ) ) { return substr( $w, 0, -2 ); }
	if ( strlen( $w ) > 3 && substr( $w, -1 ) === 's' && substr( $w, -2 ) !== 'ss' ) { return substr( $w, 0, -1 ); }
	return $w;
}

/**
 * Words too common to tell two pages apart. Dropping them keeps the index
 * about what a page is, rather than about English.
 */
function taum_stopwords() {
	return array_flip( explode( ' ',
		'the and that have with this from they been more will your you our for are was were his her she him них ' .
		'what when where which who whom whose why how all any both each few other some such only own same than too ' .
		'very can just should now also into over under about after before between through during above below ' .
		'taum troy street second does did done get gets got give gives given take takes make makes made ' .
		'need needs want wants come comes coming here there them then their these those every else ' .
		'people person neighbor neighbors community program programs free help helps helping ' .
		'call calls called phone email please thanks thank one two three ' .
		'year years day days time times new old out off up down back way ways thing things' ) );
}

/**
 * A small search index over the program pages, so a visitor's words can find
 * a page even when nobody wrote a keyword for it.
 *
 * Terms from the title and the intro count for more than terms from the body,
 * because that is where a page says what it is. Only the strongest terms per
 * page are sent, which keeps this to a few kilobytes.
 */
function taum_concierge_index( $pages_raw ) {
	$stop  = taum_stopwords();
	$index = array();

	foreach ( $pages_raw as $slug => $parts ) {
		$weights = array();
		foreach ( array( 'strong' => 3, 'body' => 1 ) as $where => $weight ) {
			$words = preg_split( '/[^a-z0-9]+/i', strtolower( $parts[ $where ] ), -1, PREG_SPLIT_NO_EMPTY );
			foreach ( $words as $w ) {
				if ( strlen( $w ) < 4 ) { continue; }
				$t = taum_stem( $w );
				if ( isset( $stop[ $t ] ) || strlen( $t ) < 4 ) { continue; }
				$weights[ $t ] = ( isset( $weights[ $t ] ) ? $weights[ $t ] : 0 ) + $weight;
			}
		}
		/* Keep the long tail. Sorting by frequency and taking the top forty
		   threw away "gratitude", "dukes" and "gator", which are mentioned
		   once each and are precisely the words that identify a page beyond
		   doubt. The whole index is a few kilobytes either way. */
		arsort( $weights );
		foreach ( array_slice( $weights, 0, 200, true ) as $term => $w ) {
			if ( ! isset( $index[ $term ] ) ) { $index[ $term ] = array(); }
			$index[ $term ][ $slug ] = $w;
		}
	}

	// A term on every page tells us nothing, so drop it.
	$limit = max( 2, (int) ceil( count( $pages_raw ) * 0.6 ) );
	foreach ( $index as $term => $hits ) {
		if ( count( $hits ) > $limit ) {
			unset( $index[ $term ] );
			continue;
		}
		/* How rare a word is matters more than how often it is repeated.
		   "Gratitude" appears once, on one page, and that one mention tells
		   us more than a dozen mentions of a word several pages share. A term
		   unique to one page is enough on its own; a shared one needs help. */
		$rarity = 1 === count( $hits ) ? 6 : ( 2 === count( $hits ) ? 2 : 1 );
		foreach ( $hits as $slug => $w ) {
			$index[ $term ][ $slug ] = $w * $rarity;
		}
	}
	return $index;
}
