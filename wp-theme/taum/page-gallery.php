<?php
/**
 * Gallery. The page's own photo gallery, then an archive of flyers from
 * events that have already happened. Nothing to maintain: a flyer leaves the
 * homepage and appears here the day after its event.
 */
get_header();
while ( have_posts() ) : the_post();
	taum_page_hero();
	?>
	<section>
	 <div class="wrap page-body">
	  <?php the_content(); ?>
	 </div>
	</section>

	<?php $flyers = taum_past_event_flyers(); if ( $flyers ) : ?>
	<section class="section-tint" id="flyers">
	 <div class="wrap">
	  <h2><?php esc_html_e( 'Event flyers', 'taum' ); ?></h2>
	  <p style="max-width:56ch;color:var(--indigo-soft);margin-top:10px;"><?php esc_html_e( 'Gatherings that have already happened. Every one of these filled a room.', 'taum' ); ?></p>
	  <div class="flyer-archive">
	   <?php foreach ( $flyers as $ev ) :
		   $d = get_post_meta( $ev->ID, 'taum_event_date', true ); ?>
	   <figure>
	    <?php echo get_the_post_thumbnail( $ev->ID, 'medium_large', array( 'alt' => sprintf( esc_attr__( 'Flyer for %s', 'taum' ), get_the_title( $ev ) ) ) ); ?>
	    <figcaption>
	     <?php echo esc_html( get_the_title( $ev ) ); ?>
	     <?php if ( $d ) : ?><span><?php echo esc_html( date_i18n( 'F j, Y', strtotime( $d ) ) ); ?></span><?php endif; ?>
	    </figcaption>
	   </figure>
	   <?php endforeach; ?>
	  </div>
	 </div>
	</section>
	<?php endif; ?>
	<?php
endwhile;
get_footer();
