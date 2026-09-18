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
  <span><a href="<?php echo esc_url( taum_opt( 'facebook' ) ); ?>">Facebook</a> · <a href="<?php echo esc_url( taum_opt( 'instagram' ) ); ?>">Instagram</a> · <?php esc_html_e( 'Founded 1986', 'taum' ); ?></span>
 </div>
</div>

<header class="site">
 <div class="wrap nav">
  <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'TAUM home', 'taum' ); ?>">
   <?php echo taum_seal_svg(); ?>
   <span class="word"><span class="big">TAUM</span><span class="small"><?php bloginfo( 'name' ); ?></span></span>
  </a>
  <nav aria-label="<?php esc_attr_e( 'Main', 'taum' ); ?>">
   <?php
   wp_nav_menu( array(
	   'theme_location' => 'primary',
	   'container'      => false,
	   'items_wrap'     => '<ul>%3$s</ul>',
	   'depth'          => 1,
	   'fallback_cb'    => 'taum_nav_fallback',
   ) );
   ?>
  </nav>
  <a class="btn small" href="<?php echo esc_url( home_url( '/donate/' ) ); ?>"><?php esc_html_e( 'Donate', 'taum' ); ?></a>
 </div>
</header>

<main id="main">
