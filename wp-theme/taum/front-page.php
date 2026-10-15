<?php
/**
 * Homepage. Every editable piece comes from the TAUM Info panel, sticky
 * posts, or the program pages themselves. Nothing here is hardcoded copy.
 */
get_header();

$badge = explode( '|', taum_opt( 'campaign_badge_text' ) );

// The tiles come from taum_programs(), the same list the Programs page uses.
// They used to be a second copy here, and when the MLK page moved to /mlk/
// this tile silently disappeared while the heading still claimed six.
$tiles = taum_programs();
?>

<section class="hero-inst">
 <div class="wrap hero-inst-grid">
  <div class="panel reveal">
   <p class="kicker on-dark"><?php echo esc_html( taum_opt( 'hero_kicker' ) ); ?></p>
   <h1><?php echo taum_em( taum_opt( 'hero_title' ) ); ?></h1>
   <p class="lede"><?php echo wp_kses_post( taum_opt( 'hero_lede' ) ); ?></p>
   <div class="actions">
    <a class="btn on-dark" href="#food"><?php esc_html_e( 'Get help', 'taum' ); ?></a>
    <a class="btn ghost on-dark" href="<?php echo esc_url( home_url( '/get-involved/' ) ); ?>"><?php esc_html_e( 'Get involved', 'taum' ); ?></a>
   </div>
  </div>
  <div class="hero-photo">
   <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'taum-hero' ); else : ?>
   <img src="<?php echo esc_url( TAUM_URI . '/assets/images/mural-photo.jpg' ); ?>" alt="<?php esc_attr_e( 'The TAUM building on Second Street in Troy, with a bright mural of birds building a nest together.', 'taum' ); ?>" width="1600" height="1205">
   <?php endif; ?>
   <svg class="hero-seal" viewBox="0 0 150 150" aria-hidden="true">
    <defs><path id="sealArc" d="M 75,75 m -58,0 a 58,58 0 1,1 116,0 a 58,58 0 1,1 -116,0"/></defs>
    <circle cx="75" cy="75" r="70" fill="#FBF7EC" stroke="#20264C" stroke-width="2.5"/>
    <text style="font-family:'Nunito Sans',sans-serif;font-weight:900;font-size:11.5px;letter-spacing:2px;fill:#20264C;text-transform:uppercase;"><textPath href="#sealArc">Troy Area United Ministries · Est. 1986 ·</textPath></text>
    <path d="M50 84c8-15 23-21 37-18-6 2-10 5-13 8 8-3 16-1 21 5-7-2-14-1-18 4 5 0 9 3 11 6-8-4-19-4-26 0-5 3-8 3-12-5z" fill="#20264C" transform="translate(0,-4)"/>
   </svg>
  </div>
 </div>
</section>

<div class="quickbar" id="food">
 <div class="wrap">
  <a href="<?php echo esc_url( home_url( '/food/' ) ); ?>"><strong><?php esc_html_e( 'Need food?', 'taum' ); ?></strong><span><?php echo esc_html( sprintf( 'Meals %s & %s, plus a 24/7 Free Food Fridge', taum_opt( 'meal_monday_short' ), taum_opt( 'meal_thursday_short' ) ) ); ?></span></a>
  <a href="<?php echo esc_url( home_url( '/furniture/' ) ); ?>"><strong><?php esc_html_e( 'Need furniture?', 'taum' ); ?></strong><span><?php echo esc_html( sprintf( 'Free, delivered, for neighbors starting over, call %s x204', taum_opt( 'phone' ) ) ); ?></span></a>
  <a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>"><strong><?php esc_html_e( 'Want to give?', 'taum' ); ?></strong><span><?php esc_html_e( 'Money, furniture, food, or your time, every gift stays in Troy', 'taum' ); ?></span></a>
 </div>
</div>

<div class="angle-strip" aria-hidden="true"></div>

<?php
/**
 * Next event, with its flyer. The soonest dated event takes this slot by
 * itself; once the date passes, the flyer moves to the gallery archive.
 */
$next = taum_next_event();
if ( $next ) :
	$nid   = $next->ID;
	$ndate = get_post_meta( $nid, 'taum_event_date', true );
	$ntime = get_post_meta( $nid, 'taum_event_time', true );
	$nplace= get_post_meta( $nid, 'taum_event_place', true );
	$nurl  = get_post_meta( $nid, 'taum_event_url', true );
	$nlbl  = get_post_meta( $nid, 'taum_event_url_label', true );
	$nwhen = trim( ( $ndate ? date_i18n( 'l, F j', strtotime( $ndate ) ) : '' ) . ( $ntime ? ' · ' . $ntime : '' ) );
?>
<section class="next-up" id="next-up">
 <div class="wrap next-up-grid reveal<?php echo has_post_thumbnail( $nid ) ? '' : ' no-flyer'; ?>">
  <?php if ( has_post_thumbnail( $nid ) ) : ?>
  <a class="flyer" href="<?php echo esc_url( get_permalink( $nid ) ); ?>">
   <?php echo get_the_post_thumbnail( $nid, 'large', array( 'alt' => sprintf( esc_attr__( 'Flyer for %s', 'taum' ), get_the_title( $nid ) ) ) ); ?>
  </a>
  <?php endif; ?>
  <div>
   <p class="kicker"><?php esc_html_e( 'Next up', 'taum' ); ?></p>
   <h2><?php echo esc_html( get_the_title( $nid ) ); ?></h2>
   <?php if ( $nwhen ) : ?><p class="when-big"><?php echo esc_html( $nwhen ); ?></p><?php endif; ?>
   <?php if ( $nplace ) : ?><p class="where"><?php echo esc_html( $nplace ); ?></p><?php endif; ?>
   <div class="prose" style="margin-top:14px;"><?php echo wpautop( wp_kses_post( $next->post_content ) ); ?></div>
   <p class="actions" style="margin-top:20px;">
    <?php if ( $nurl ) : ?><a class="btn" href="<?php echo esc_url( $nurl ); ?>"><?php echo esc_html( $nlbl ? $nlbl : __( 'Details', 'taum' ) ); ?></a><?php endif; ?>
    <a class="btn ghost" href="<?php echo esc_url( home_url( '/news/#events' ) ); ?>"><?php esc_html_e( 'All events', 'taum' ); ?></a>
   </p>
  </div>
 </div>
</section>
<?php endif; ?>

<?php $now = taum_home_posts(); if ( $now ) : ?>
<section id="now">
 <div class="wrap">
  <p class="kicker reveal"><?php echo esc_html( taum_opt( 'now_kicker' ) ); ?></p>
  <h2 class="reveal"><?php echo esc_html( taum_opt( 'now_title' ) ); ?></h2>
  <div class="cards">
   <?php foreach ( $now as $p ) : ?>
   <div class="card campaign reveal <?php echo is_sticky( $p->ID ) ? '' : ''; ?>">
    <h3><?php echo esc_html( get_the_title( $p ) ); ?></h3>
    <p><?php echo esc_html( has_excerpt( $p ) ? get_the_excerpt( $p ) : wp_trim_words( wp_strip_all_tags( $p->post_content ), 40, '…' ) ); ?></p>
    <a class="btn small" href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php esc_html_e( 'Read more', 'taum' ); ?></a>
   </div>
   <?php endforeach; ?>
  </div>
 </div>
</section>
<?php endif; ?>

<?php if ( taum_opt( 'campaign_on' ) ) : ?>
<section class="section-warm" id="campaign">
 <div class="wrap flames">
  <div class="reveal">
   <p class="kicker"><?php echo esc_html( taum_opt( 'campaign_kicker' ) ); ?></p>
   <h2><?php echo esc_html( taum_opt( 'campaign_title' ) ); ?></h2>
   <p style="margin-top:18px;"><?php echo wp_kses_post( taum_opt( 'campaign_text' ) ); ?></p>
   <a class="btn" style="margin-top:26px;" href="<?php echo esc_url( taum_opt( 'campaign_url' ) ); ?>"><?php echo esc_html( taum_opt( 'campaign_btn' ) ); ?></a>
  </div>
  <div class="flame-badge reveal">
   <div class="big"><?php echo esc_html( taum_opt( 'campaign_badge' ) ); ?></div>
   <p><strong><?php echo esc_html( trim( $badge[0] ) ); ?></strong><?php if ( isset( $badge[1] ) ) : ?><br><?php echo esc_html( trim( $badge[1] ) ); endif; ?></p>
  </div>
 </div>
</section>
<?php endif; ?>

<section class="impact" id="impact">
 <div class="wrap">
  <h2 class="reveal"><?php esc_html_e( 'Every gift stays in the neighborhood', 'taum' ); ?></h2>
  <div class="grid">
   <?php for ( $i = 1; $i <= 4; $i++ ) : $n = preg_replace( '/\D/', '', taum_opt( "stat{$i}_n" ) ); $s = taum_opt( "stat{$i}_s" ); if ( '' === $n ) { continue; } ?>
   <div class="reveal"><div class="num" data-count="<?php echo esc_attr( $n ); ?>" data-suffix="<?php echo esc_attr( $s ); ?>"><?php echo esc_html( number_format_i18n( (int) $n ) . $s ); ?></div><div class="lbl"><?php echo esc_html( taum_opt( "stat{$i}_l" ) ); ?></div></div>
   <?php endfor; ?>
  </div>
 </div>
</section>

<section id="programs">
 <div class="wrap">
  <p class="kicker reveal"><?php esc_html_e( 'What we do', 'taum' ); ?></p>
  <h2 class="reveal"><?php
	  /* translators: %s: how many programs are published. */
	  printf( esc_html__( '%s ways we show up', 'taum' ), esc_html( ucfirst( taum_number_word( count( $tiles ) ) ) ) );
	  ?></h2>
  <div class="tile-grid reveal">
   <?php foreach ( $tiles as $slug => $t ) : ?>
   <a class="tile <?php echo esc_attr( $t['tone'] ); ?>" href="<?php echo esc_url( $t['url'] ); ?>">
    <svg class="ico2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $t['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg>
    <h3><?php echo esc_html( $t['label'] ); ?></h3>
    <p><?php echo esc_html( has_excerpt( $t['page'] ) ? wp_trim_words( get_the_excerpt( $t['page'] ), 14, '…' ) : '' ); ?></p>
    <span class="more"><?php echo esc_html( $t['cta'] ); ?> &rarr;</span>
   </a>
   <?php endforeach; ?>
  </div>
 </div>
</section>

<section class="banner has-photo" id="involved">
 <div class="wrap">
  <div class="banner-photo reveal"><img src="<?php echo esc_url( TAUM_URI . '/assets/images/group-grass.jpg' ); ?>" alt="<?php esc_attr_e( 'A diverse group of TAUM volunteers laughing together outdoors.', 'taum' ); ?>" width="1100" height="733"></div>
  <div class="reveal" style="flex:1;">
   <h2><?php esc_html_e( 'This building belongs to the neighborhood.', 'taum' ); ?></h2>
   <p><?php esc_html_e( "Our home at 392 Second Street is more than offices, it's a gathering place. Host a meeting, a class, a celebration, or a community project here.", 'taum' ); ?></p>
   <a class="btn" style="margin-top:18px;" href="<?php echo esc_url( home_url( '/community-center/' ) ); ?>"><?php esc_html_e( 'Use the building', 'taum' ); ?></a>
  </div>
 </div>
</section>

<section class="newsletter section-tint" id="donate">
 <div class="wrap">
  <h2 class="reveal"><?php esc_html_e( 'Stay close to the neighborhood', 'taum' ); ?></h2>
  <p style="max-width:36em;margin:14px auto 0;" class="reveal"><?php esc_html_e( 'Get TAUM news, events, and ways to help, about once a month, no clutter. Or make a gift today and keep the fridge full, the truck rolling, and the table set.', 'taum' ); ?></p>
  <form action="<?php echo esc_url( taum_opt( 'url_newsletter' ) ); ?>" method="get" class="reveal">
   <input type="email" placeholder="you@example.com" aria-label="<?php esc_attr_e( 'Email address', 'taum' ); ?>" required>
   <button class="btn" type="submit"><?php esc_html_e( 'Sign up', 'taum' ); ?></button>
   <a class="btn ghost" href="<?php echo esc_url( home_url( '/donate/' ) ); ?>"><?php esc_html_e( 'Donate now', 'taum' ); ?></a>
  </form>
 </div>
</section>

<?php get_footer(); ?>
