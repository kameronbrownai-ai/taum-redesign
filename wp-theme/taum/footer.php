</main>

<footer class="site" id="contact">
 <div class="wrap">
  <?php if ( is_front_page() ) : ?>
  <div class="cols">
   <div>
    <h3>TAUM</h3>
    <p style="font-size:.95rem;"><?php bloginfo( 'name' ); ?><br><?php echo esc_html( taum_opt( 'address1' ) ); ?><br><?php echo esc_html( taum_opt( 'address2' ) ); ?></p>
    <p style="font-size:.95rem;margin-top:10px;"><?php echo esc_html( taum_opt( 'phone' ) ); ?><br><a href="mailto:<?php echo esc_attr( taum_opt( 'email_office' ) ); ?>"><?php echo esc_html( taum_opt( 'email_office' ) ); ?></a></p>
   </div>
   <div>
    <h3><?php esc_html_e( 'Get help', 'taum' ); ?></h3>
    <ul>
     <li><a href="<?php echo esc_url( home_url( '/food/' ) ); ?>">Free food</a></li>
     <li><a href="<?php echo esc_url( home_url( '/furniture/' ) ); ?>">Free furniture</a></li>
     <li><a href="<?php echo esc_url( home_url( '/damien-center/' ) ); ?>">Damien Center</a></li>
     <li><a href="<?php echo esc_url( home_url( '/tech-for-teens/' ) ); ?>">Tech for Teens</a></li>
     <li><a href="<?php echo esc_url( home_url( '/mlk-scholarship/' ) ); ?>">MLK Scholarship</a></li>
    </ul>
   </div>
   <div>
    <h3><?php esc_html_e( 'Get involved', 'taum' ); ?></h3>
    <ul>
     <li><a href="<?php echo esc_url( home_url( '/get-involved/' ) ); ?>">Volunteer</a></li>
     <li><a href="<?php echo esc_url( home_url( '/community-center/' ) ); ?>">Use the building</a></li>
     <li><a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News &amp; events</a></li>
     <li><a href="<?php echo esc_url( taum_opt( 'url_newsletter' ) ); ?>">Newsletter</a></li>
     <li><a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>">Donate</a></li>
    </ul>
   </div>
   <div>
    <h3><?php esc_html_e( 'Follow along', 'taum' ); ?></h3>
    <ul>
     <li><a href="<?php echo esc_url( taum_opt( 'facebook' ) ); ?>">Facebook</a></li>
     <li><a href="<?php echo esc_url( taum_opt( 'instagram' ) ); ?>">Instagram @taum518</a></li>
     <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
    </ul>
   </div>
  </div>
  <div class="fine">
   <span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <?php esc_html_e( 'Founded by congregations, open to everyone.', 'taum' ); ?></span>
   <span><?php esc_html_e( '501(c)(3) nonprofit · Gifts are tax-deductible', 'taum' ); ?></span>
  </div>
  <?php else : ?>
  <div class="fine">
   <span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <?php echo esc_html( taum_opt( 'address1' ) . ', ' . taum_opt( 'address2' ) . ' · ' . taum_opt( 'phone' ) ); ?></span>
   <span><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> · <a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a> · <a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>">Donate</a></span>
  </div>
  <?php endif; ?>
 </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
