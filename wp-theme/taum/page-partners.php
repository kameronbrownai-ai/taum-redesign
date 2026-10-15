<?php
/**
 * Partners. The organizations TAUM works with, shown as logos where we have
 * them and as names where we do not.
 *
 * Built from the Partners post type, so adding one is adding a post with a
 * featured image for the logo and a website in the "Partner details" box.
 * A tier with nobody in it says so plainly rather than rendering an empty
 * heading, because the sponsor list is still being gathered.
 */
get_header();
while ( have_posts() ) : the_post();
	taum_page_hero();
	$tiers = taum_partners_by_tier();
	?>

	<?php if ( trim( get_the_content() ) ) : ?>
	<section>
	 <div class="wrap page-body"><?php the_content(); ?></div>
	</section>
	<?php endif; ?>

	<?php foreach ( $tiers as $tier => $list ) : ?>
	<section<?php echo 'Sponsors &amp; funders' === $tier ? ' class="section-tint"' : ''; ?>>
	 <div class="wrap">
	  <h2><?php echo esc_html( html_entity_decode( $tier, ENT_QUOTES, 'UTF-8' ) ); ?></h2>

	  <?php if ( $list ) : ?>
	  <ul class="logo-grid">
	   <?php
	   foreach ( $list as $p ) :
		   $url  = get_post_meta( $p->ID, 'taum_partner_url', true );
		   $name = get_the_title( $p );
		   $logo = has_post_thumbnail( $p );
		   ?>
	   <li class="<?php echo $logo ? 'has-logo' : 'no-logo'; ?>">
	    <?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" rel="noopener"><?php endif; ?>
	     <?php
		 if ( $logo ) {
			 // The name is already below the logo, so the image itself is decorative.
			 echo get_the_post_thumbnail( $p, 'medium', array( 'alt' => '', 'loading' => 'lazy' ) );
		 }
		 ?>
	     <span class="nm"><?php echo esc_html( $name ); ?></span>
	    <?php if ( $url ) : ?></a><?php endif; ?>
	   </li>
	   <?php endforeach; ?>
	  </ul>

	  <?php else : ?>
	  <div class="emptybox" style="margin-top:20px;">
	   <p><strong><?php esc_html_e( 'This list is being gathered.', 'taum' ); ?></strong>
	   <?php esc_html_e( 'If your business, congregation, or foundation supports TAUM and you would like to be listed here, write to', 'taum' ); ?>
	   <a href="mailto:<?php echo esc_attr( taum_opt( 'email_office' ) ); ?>"><?php echo esc_html( taum_opt( 'email_office' ) ); ?></a>
	   <?php esc_html_e( 'and we will add you.', 'taum' ); ?></p>
	  </div>
	  <?php endif; ?>
	 </div>
	</section>
	<?php endforeach; ?>

	<section>
	 <div class="wrap">
	  <div class="callout">
	   <h3><?php esc_html_e( 'Want to partner with us?', 'taum' ); ?></h3>
	   <p><?php esc_html_e( 'Sponsoring a program, running a food drive, sending a work group, sharing our space, or something we have not thought of yet. We would like to hear from you.', 'taum' ); ?></p>
	   <p style="margin-top:14px;">
	    <a class="btn small" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Start a conversation', 'taum' ); ?></a>
	    <a class="btn small ghost" style="margin-left:8px;" href="<?php echo esc_url( home_url( '/community-center/' ) ); ?>"><?php esc_html_e( 'Use the building', 'taum' ); ?></a>
	   </p>
	  </div>
	 </div>
	</section>
	<?php
endwhile;
get_footer();
