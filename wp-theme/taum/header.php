<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'taum' ); ?></a>

<div class="utility-bar">
 <div class="wrap">
  <span><?php echo esc_html( taum_opt( 'address1' ) . ', ' . taum_opt( 'address2' ) . ' · ' . taum_opt( 'phone' ) ); ?></span>
  <span><a href="<?php echo esc_url( taum_opt( 'facebook' ) ); ?>">Facebook</a><?php if ( taum_opt( 'instagram' ) ) : ?> · <a href="<?php echo esc_url( taum_opt( 'instagram' ) ); ?>">Instagram</a><?php endif; ?> · <?php esc_html_e( 'Founded 1986', 'taum' ); ?></span>
 </div>
</div>

<header class="site">
 <div class="wrap nav">
  <?php
  /**
   * The logo is the client's own artwork, kept in the theme rather than the
   * media library because the transparent and reversed versions are a matched
   * pair cut from one opaque file, and uploading a replacement would only
   * restore the white box they were made to remove.
   */
  ?>
  <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
   <img class="brand-logo" src="<?php echo esc_url( TAUM_URI . '/assets/images/taum-logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="414" height="238">
  </a>
  <nav aria-label="<?php esc_attr_e( 'Main', 'taum' ); ?>">
   <?php
   wp_nav_menu( array(
	   'theme_location' => 'primary',
	   'container'      => false,
	   'items_wrap'     => '<ul>%3$s</ul>',
	   'depth'          => 2,
	   'fallback_cb'    => 'taum_nav_fallback',
   ) );
   ?>
  </nav>
  <a class="btn small" href="<?php echo esc_url( home_url( '/donate/' ) ); ?>"><?php esc_html_e( 'Donate', 'taum' ); ?></a>
 </div>
</header>

<main id="main">
