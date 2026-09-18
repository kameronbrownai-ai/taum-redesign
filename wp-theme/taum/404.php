<?php get_header(); ?>
<div class="page-hero ph-peach">
 <div class="wrap">
  <p class="kicker"><?php esc_html_e( 'Page not found', 'taum' ); ?></p>
  <h1><?php esc_html_e( "That page isn't here.", 'taum' ); ?></h1>
  <p class="lede"><?php esc_html_e( 'The building is, though. Try one of these doors.', 'taum' ); ?></p>
  <div class="actions" style="margin-top:26px;">
   <a class="btn" href="<?php echo esc_url( home_url( '/food/' ) ); ?>"><?php esc_html_e( 'Get food', 'taum' ); ?></a>
   <a class="btn ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'taum' ); ?></a>
  </div>
 </div>
</div>
<?php get_footer(); ?>
