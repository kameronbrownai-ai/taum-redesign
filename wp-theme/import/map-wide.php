<?php
if ( ! defined( 'ABSPATH' ) ) { die( 'Run via wp eval-file.' ); }
$pg = get_page_by_path( 'contact' );
if ( ! $pg ) { WP_CLI::error( 'Contact page not found.' ); }
$addr = taum_opt( 'address1' ) . ', ' . taum_opt( 'address2' );
$q    = rawurlencode( $addr );
$c    = $pg->post_content;

$start = strpos( $c, '<h2 style="margin-top:44px;">Find us</h2>' );
$end   = strpos( $c, '<h2 style="margin-top:44px;">Send us a message</h2>' );
if ( false === $start || false === $end ) { WP_CLI::error( 'Could not locate the Find us block.' ); }

/* The map was sharing a two-column row with the address card, which left it
   about a third of the page. It runs the full width now, with the address and
   the directions button above it. */
$new = '<h2 style="margin-top:44px;">Find us</h2>
  <div class="find-us">
   <div>
    <p><strong>392 Second Street, Troy, NY 12180.</strong> We are in the Osgood neighborhood, a few minutes from downtown Troy. Look for the bird mural, you cannot miss it.</p>
    <p style="margin-top:8px;color:var(--indigo-soft);"><strong>Office hours:</strong> [taum hours]</p>
   </div>
   <a class="btn" href="https://www.google.com/maps/dir/?api=1&amp;destination=' . $q . '" rel="noopener">Get directions</a>
  </div>
  <?php // Not maps.google.com/...&output=embed: that 301s with X-Frame-Options SAMEORIGIN and the browser blocks the frame on the redirect. ?>
  <div class="map-embed">
   <iframe title="Map showing TAUM at ' . esc_attr( $addr ) . '" src="https://www.google.com/maps/embed?origin=mfe&amp;pb=!1m2!2m1!1s' . $q . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
  </div>

  ';

$c = substr( $c, 0, $start ) . $new . substr( $c, $end );
$r = wp_update_post( wp_slash( array( 'ID' => $pg->ID, 'post_content' => $c ) ), true );
if ( is_wp_error( $r ) ) { WP_CLI::error( $r->get_error_message() ); }
WP_CLI::success( 'Map is full width now.' );
