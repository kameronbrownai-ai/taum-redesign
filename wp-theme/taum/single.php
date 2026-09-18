<?php
/**
 * A single news post, or a single event.
 */
get_header();
while ( have_posts() ) : the_post();
	$is_event = 'taum_event' === get_post_type();
	?>
	<div class="page-hero ph-aqua-pale">
	 <div class="wrap <?php echo has_post_thumbnail() ? 'hero-split-2' : ''; ?>">
	  <div>
	   <p class="kicker"><?php echo $is_event ? esc_html__( 'Event', 'taum' ) : esc_html( get_the_date( 'F j, Y' ) ); ?></p>
	   <h1><?php the_title(); ?></h1>
	   <?php if ( $is_event ) :
		   $rec = get_post_meta( get_the_ID(), 'taum_event_recurring', true );
		   $d   = get_post_meta( get_the_ID(), 'taum_event_date', true );
		   $t   = get_post_meta( get_the_ID(), 'taum_event_time', true );
		   $pl  = get_post_meta( get_the_ID(), 'taum_event_place', true );
		   $when = $rec ? 'Every ' . get_post_meta( get_the_ID(), 'taum_event_day_label', true ) : ( $d ? date_i18n( 'l, F j, Y', strtotime( $d ) ) : '' );
		   $when = trim( $when . ( $t ? ' · ' . $t : '' ) . ( $pl ? ' · ' . $pl : '' ) );
		   if ( $when ) : ?><p class="lede"><strong><?php echo esc_html( $when ); ?></strong></p><?php endif;
	   elseif ( has_excerpt() ) : ?><p class="lede"><?php echo wp_kses_post( get_the_excerpt() ); ?></p><?php endif; ?>
	  </div>
	  <?php if ( has_post_thumbnail() ) : ?><div class="page-hero-photo"><?php the_post_thumbnail( 'taum-hero' ); ?></div><?php endif; ?>
	 </div>
	</div>

	<section>
	 <div class="wrap page-body">
	  <div class="prose" style="max-width:68ch;">
	   <?php the_content(); ?>
	  </div>
	  <?php if ( $is_event && ( $u = get_post_meta( get_the_ID(), 'taum_event_url', true ) ) ) : ?>
	  <p style="margin-top:24px;"><a class="btn" href="<?php echo esc_url( $u ); ?>"><?php echo esc_html( get_post_meta( get_the_ID(), 'taum_event_url_label', true ) ?: __( 'Details', 'taum' ) ); ?></a></p>
	  <?php endif; ?>
	  <p style="margin-top:36px;"><a class="btn ghost" href="<?php echo esc_url( home_url( '/news/' ) ); ?>">← <?php esc_html_e( 'All news & events', 'taum' ); ?></a></p>
	 </div>
	</section>
	<?php
endwhile;
get_footer();
