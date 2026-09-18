<?php
/**
 * Fallback for archives, search, and anything without a dedicated template.
 */
get_header();
?>
<div class="page-hero ph-aqua-pale">
 <div class="wrap">
  <p class="kicker"><?php echo is_search() ? esc_html__( 'Search', 'taum' ) : esc_html__( 'News', 'taum' ); ?></p>
  <h1><?php echo is_search() ? esc_html( sprintf( __( 'Results for "%s"', 'taum' ), get_search_query() ) ) : esc_html( get_the_archive_title() ); ?></h1>
 </div>
</div>
<section>
 <div class="wrap">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <div class="post">
   <p class="stamp"><?php echo esc_html( get_the_date( 'F Y' ) ); ?></p>
   <h3><a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a></h3>
   <p><?php echo esc_html( has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 45, '…' ) ); ?></p>
  </div>
  <?php endwhile; the_posts_pagination(); else : ?>
  <div class="emptybox"><p><?php esc_html_e( 'Nothing here yet.', 'taum' ); ?></p></div>
  <?php endif; ?>
 </div>
</section>
<?php get_footer(); ?>
