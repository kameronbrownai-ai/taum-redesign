<?php
/**
 * About page: the page body (story, milestones, staff, board, reports) then
 * the partners and sponsors section generated from the Partners post type.
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

	<section id="partners">
	 <div class="wrap">
	  <h2><?php esc_html_e( 'Partners & supporters', 'taum' ); ?></h2>
	  <p style="max-width:58ch;color:var(--indigo-soft);margin-top:10px;"><?php esc_html_e( 'Nothing here happens alone. These are the organizations we work alongside, and the people and businesses whose support keeps the doors open.', 'taum' ); ?></p>

	  <?php foreach ( taum_partners_by_tier() as $tier => $list ) : ?>
	  <div class="tier">
	   <h3><?php echo esc_html( $tier ); ?></h3>
	   <?php if ( $list ) : ?>
	   <ul class="partners">
	    <?php foreach ( $list as $p ) : $u = get_post_meta( $p->ID, 'taum_partner_url', true ); ?>
	    <li><?php if ( $u ) : ?><a href="<?php echo esc_url( $u ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a><?php else : echo esc_html( get_the_title( $p ) ); endif; ?></li>
	    <?php endforeach; ?>
	   </ul>
	   <?php else : ?>
	   <div class="emptybox"><p><strong><?php esc_html_e( 'This list is being updated.', 'taum' ); ?></strong> <?php esc_html_e( "If your business, congregation, or foundation supports TAUM and you'd like to be listed here, email", 'taum' ); ?> <a href="mailto:<?php echo esc_attr( taum_opt( 'email_office' ) ); ?>"><?php echo esc_html( taum_opt( 'email_office' ) ); ?></a> <?php esc_html_e( "and we'll add you.", 'taum' ); ?></p></div>
	   <?php endif; ?>
	  </div>
	  <?php endforeach; ?>

	  <div class="callout" style="margin-top:34px;">
	   <h3><?php esc_html_e( 'Want to partner with us?', 'taum' ); ?></h3>
	   <p><?php esc_html_e( "Whether that's sponsoring a program, running a food drive, sending a work group, or sharing our space, we'd like to hear from you.", 'taum' ); ?></p>
	   <a class="btn small" style="margin-top:12px;" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Start a conversation', 'taum' ); ?></a>
	  </div>
	 </div>
	</section>
	<?php
endwhile;
get_footer();
