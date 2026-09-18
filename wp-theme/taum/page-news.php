<?php
/**
 * News & events. Weekly events, then upcoming one-time events, then the
 * latest posts. Abby adds an Event or a Post and it appears here.
 */
get_header();
while ( have_posts() ) : the_post();
	taum_page_hero();

	$weekly   = taum_get_events( 'weekly' );
	$upcoming = taum_get_events( 'upcoming' );
	$posts    = get_posts( array( 'posts_per_page' => 6 ) );
	?>

	<?php if ( $weekly ) : ?>
	<section>
	 <div class="wrap">
	  <h2><?php esc_html_e( 'Every week', 'taum' ); ?></h2>
	  <p style="max-width:52ch;color:var(--indigo-soft);margin-top:10px;"><?php esc_html_e( 'No sign-up, no ID, no referral. Just come.', 'taum' ); ?></p>
	  <?php foreach ( $weekly as $ev ) { taum_event_card( $ev ); } ?>
	 </div>
	</section>
	<?php endif; ?>

	<section class="section-tint">
	 <div class="wrap">
	  <h2><?php esc_html_e( 'Coming up', 'taum' ); ?></h2>
	  <?php if ( $upcoming ) : ?>
	   <p style="max-width:52ch;color:var(--indigo-soft);margin-top:10px;"><?php esc_html_e( 'Our gatherings, and how to get a spot.', 'taum' ); ?></p>
	   <?php foreach ( $upcoming as $ev ) { taum_event_card( $ev ); } ?>
	  <?php else : ?>
	   <div class="emptybox"><p><strong><?php esc_html_e( 'Nothing scheduled right now.', 'taum' ); ?></strong> <?php esc_html_e( 'The weekly meals above never stop, and the newsletter is the first to hear about new events.', 'taum' ); ?></p></div>
	  <?php endif; ?>

	  <div class="callout" style="margin-top:34px;">
	   <h3><?php esc_html_e( 'Want to host something here?', 'taum' ); ?></h3>
	   <p><?php esc_html_e( 'Our community room, kitchen, and meeting spaces are open to neighborhood groups, classes, and celebrations. Tell us what you have in mind.', 'taum' ); ?></p>
	   <a class="btn small" style="margin-top:12px;" href="<?php echo esc_url( home_url( '/community-center/' ) ); ?>"><?php esc_html_e( 'About the Community Center', 'taum' ); ?></a>
	  </div>
	 </div>
	</section>

	<section>
	 <div class="wrap">
	  <h2><?php esc_html_e( 'From around the building', 'taum' ); ?></h2>
	  <?php if ( $posts ) : foreach ( $posts as $p ) : ?>
	  <div class="post">
	   <p class="stamp"><?php echo esc_html( get_the_date( 'F Y', $p ) ); ?></p>
	   <h3><a href="<?php echo esc_url( get_permalink( $p ) ); ?>" style="color:inherit;text-decoration:none;"><?php echo esc_html( get_the_title( $p ) ); ?></a></h3>
	   <p><?php echo esc_html( has_excerpt( $p ) ? get_the_excerpt( $p ) : wp_trim_words( wp_strip_all_tags( $p->post_content ), 45, '…' ) ); ?></p>
	   <p style="margin-top:10px;"><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php esc_html_e( 'Read more', 'taum' ); ?></a></p>
	  </div>
	  <?php endforeach; endif; ?>

	  <div class="emptybox">
	   <p><strong><?php esc_html_e( 'More news soon.', 'taum' ); ?></strong> <?php esc_html_e( 'For the latest between updates, follow us on', 'taum' ); ?> <a href="<?php echo esc_url( taum_opt( 'facebook' ) ); ?>">Facebook</a> <?php esc_html_e( 'and', 'taum' ); ?> <a href="<?php echo esc_url( taum_opt( 'instagram' ) ); ?>">Instagram</a>, <?php esc_html_e( 'or get the newsletter below.', 'taum' ); ?></p>
	  </div>
	 </div>
	</section>

	<section class="newsletter section-tint">
	 <div class="wrap">
	  <h2><?php esc_html_e( 'Never miss a meal, a walk, or a workday', 'taum' ); ?></h2>
	  <p style="max-width:36em;margin:14px auto 0;"><?php esc_html_e( "Get TAUM news and events about once a month. No clutter, no daily begging, just what's happening and how to be part of it.", 'taum' ); ?></p>
	  <form action="<?php echo esc_url( taum_opt( 'url_newsletter' ) ); ?>" method="get">
	   <input type="email" placeholder="you@example.com" aria-label="<?php esc_attr_e( 'Email address', 'taum' ); ?>" required>
	   <button class="btn" type="submit"><?php esc_html_e( 'Sign up', 'taum' ); ?></button>
	   <a class="btn ghost" href="<?php echo esc_url( home_url( '/get-involved/' ) ); ?>"><?php esc_html_e( 'Volunteer', 'taum' ); ?></a>
	  </form>
	 </div>
	</section>
	<?php
endwhile;
get_footer();
