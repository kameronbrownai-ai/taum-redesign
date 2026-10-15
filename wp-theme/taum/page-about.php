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
	  <p style="max-width:58ch;color:var(--indigo-soft);margin-top:10px;"><?php esc_html_e( 'Nothing here happens alone. We work alongside other organizations, share this building with some of them, and keep the doors open on the support of funders, congregations and businesses.', 'taum' ); ?></p>
	  <?php // The full list lives on its own page now, with logos. Repeating it here only gave it two places to go stale. ?>
	  <p style="margin-top:18px;">
	   <a class="btn" href="<?php echo esc_url( home_url( '/partners/' ) ); ?>"><?php esc_html_e( 'See our partners', 'taum' ); ?></a>
	   <a class="btn ghost" style="margin-left:8px;" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Partner with us', 'taum' ); ?></a>
	  </p>
	 </div>
	</section>
	<?php
endwhile;
get_footer();
