<?php
/**
 * Default page: hero from title / excerpt / featured image, then the page
 * body. The body keeps its layout classes (card, grid2, callout) so Abby
 * can edit text without the design falling apart.
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
	<?php
endwhile;
get_footer();
