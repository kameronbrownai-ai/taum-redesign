<?php
/**
 * Programs. The hub page: what TAUM runs, and who to call about each one.
 *
 * The cards are built from the program pages themselves, not from copy typed
 * into this page. The photo is that page's featured image, the headline is its
 * title, the description is its excerpt, and the contact comes from its own
 * meta. So when Abby edits the Furniture page, this page follows, and the
 * stale descriptions that had collected here cannot come back.
 *
 * The page body still renders underneath, for anything that does not fit the
 * card pattern, such as the CROP Hunger Walk.
 */
get_header();
while ( have_posts() ) : the_post();
	taum_page_hero();
	$programs = taum_programs();
	?>

	<?php if ( $programs ) : ?>
	<section>
	 <div class="wrap">
	  <div class="pcards">
	   <?php foreach ( $programs as $slug => $p ) :
		   // Not $page. Templates run in global scope, and get_the_content()
		   // reads the global $page as a page number and subtracts from it.
		   $prog    = $p['page'];
		   $contact = taum_program_contact( $prog->ID );
		   $desc    = has_excerpt( $prog ) ? wp_trim_words( get_the_excerpt( $prog ), 26, '…' ) : '';
		   ?>
	   <article class="pcard" style="--pc-accent: <?php echo esc_attr( $p['accent'] ); ?>;">
	    <a class="pcard-media" href="<?php echo esc_url( $p['url'] ); ?>" tabindex="-1" aria-hidden="true">
	     <?php
		 if ( has_post_thumbnail( $prog ) ) {
			 echo get_the_post_thumbnail( $prog, 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) );
		 }
		 ?>
	     <span class="pcard-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $p['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg></span>
	    </a>

	    <div class="pcard-body">
	     <h3><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['label'] ); ?></a></h3>
	     <p class="pcard-line"><?php echo esc_html( get_the_title( $prog ) ); ?></p>
	     <?php if ( $desc ) : ?><p class="pcard-desc"><?php echo esc_html( $desc ); ?></p><?php endif; ?>

	     <?php if ( $contact ) : ?>
	     <div class="pcard-who">
	      <p class="lbl"><?php esc_html_e( 'Who to ask', 'taum' ); ?></p>
	      <?php if ( $contact['name'] ) : ?><p class="nm"><?php echo esc_html( $contact['name'] ); ?></p><?php endif; ?>
	      <?php if ( $contact['lines'] ) : ?>
	      <p><?php echo implode( '<br>', $contact['lines'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	      <?php endif; ?>
	     </div>
	     <?php endif; ?>

	     <a class="pcard-more" href="<?php echo esc_url( $p['url'] ); ?>">
	      <?php echo esc_html( $p['cta'] ); ?><span aria-hidden="true"> &rarr;</span>
	      <span class="screen-reader-text"><?php echo esc_html( ': ' . $p['label'] ); ?></span>
	     </a>
	    </div>
	   </article>
	   <?php endforeach; ?>
	  </div>
	 </div>
	</section>
	<?php endif; ?>

	<?php if ( trim( get_the_content() ) ) : ?>
	<section>
	 <div class="wrap page-body">
	  <?php the_content(); ?>
	 </div>
	</section>
	<?php endif; ?>

	<section class="section-tint">
	 <div class="wrap">
	  <div class="media-row">
	   <div>
	    <h2><?php esc_html_e( 'Not sure which door to knock on?', 'taum' ); ?></h2>
	    <p><?php
			printf(
				/* translators: 1: phone link, 2: office hours, 3: contact page link */
				esc_html__( 'That is what the front desk is for. Call %1$s during office hours (%2$s) and we will put you through to the right program and the right person. Or %3$s and we will come back to you.', 'taum' ),
				'<strong><a href="' . esc_attr( taum_tel() ) . '">' . esc_html( taum_opt( 'phone' ) ) . '</a></strong>',
				esc_html( taum_opt( 'hours' ) ),
				'<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'send us a message', 'taum' ) . '</a>'
			);
			?></p>
	    <p style="margin-top:16px;">
	     <a class="btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact us', 'taum' ); ?></a>
	     <a class="btn ghost" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'Read the FAQ', 'taum' ); ?></a>
	    </p>
	   </div>
	   <div class="media-photo">
	    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/10/garden-build-group-1024x771.jpg' ) ); ?>" alt="<?php esc_attr_e( 'The volunteer crew gathered in the finished community garden beside the brick wall.', 'taum' ); ?>" loading="lazy" width="1024" height="771">
	   </div>
	  </div>
	 </div>
	</section>
	<?php
endwhile;
get_footer();
