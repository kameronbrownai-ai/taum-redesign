</main>

<footer class="site" id="contact">
 <div class="wrap">
  <?php
  /**
   * The full footer renders on every page, not just the front one.
   * Someone who lands on /food/ from a search result should have the address,
   * the phone number and a way through to everything else without going home
   * first, and a one-line footer gave them none of that.
   */
  ?>
  <div class="cols">
   <div>
    <h3>TAUM</h3>
    <p style="font-size:.95rem;"><?php bloginfo( 'name' ); ?><br><?php echo esc_html( taum_opt( 'address1' ) ); ?><br><?php echo esc_html( taum_opt( 'address2' ) ); ?></p>
    <p style="font-size:.95rem;margin-top:10px;"><a href="<?php echo esc_attr( taum_tel() ); ?>"><?php echo esc_html( taum_opt( 'phone' ) ); ?></a><br><a href="mailto:<?php echo esc_attr( taum_opt( 'email_office' ) ); ?>"><?php echo esc_html( taum_opt( 'email_office' ) ); ?></a></p>
    <p style="font-size:.95rem;margin-top:10px;"><?php echo esc_html( taum_opt( 'hours' ) ); ?></p>
   </div>
   <div>
    <h3><?php esc_html_e( 'Get help', 'taum' ); ?></h3>
    <ul>
     <li><a href="<?php echo esc_url( home_url( '/food/' ) ); ?>">Free food</a></li>
     <li><a href="<?php echo esc_url( home_url( '/furniture/' ) ); ?>">Free furniture</a></li>
     <li><a href="<?php echo esc_url( home_url( '/damien-center/' ) ); ?>">Damien Center</a></li>
     <li><a href="<?php echo esc_url( home_url( '/tech-for-teens/' ) ); ?>">Tech for Teens</a></li>
     <li><a href="<?php echo esc_url( home_url( '/chaplaincy/' ) ); ?>">Campus chaplaincy</a></li>
     <li><a href="<?php echo esc_url( home_url( '/mlk/' ) ); ?>">MLK Programs</a></li>
     <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">Questions, answered</a></li>
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
    <h3><?php esc_html_e( 'More', 'taum' ); ?></h3>
    <ul>
     <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About TAUM</a></li>
     <li><a href="<?php echo esc_url( home_url( '/programs/' ) ); ?>">All programs</a></li>
     <li><a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>">Gallery</a></li>
     <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
     <li><a href="<?php echo esc_url( taum_opt( 'facebook' ) ); ?>">Facebook</a></li>
     <?php if ( taum_opt( 'instagram' ) ) : ?><li><a href="<?php echo esc_url( taum_opt( 'instagram' ) ); ?>">Instagram</a></li><?php endif; ?>
    </ul>
   </div>
  </div>
  <div class="fine">
   <span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <?php esc_html_e( 'Founded by congregations, open to everyone.', 'taum' ); ?></span>
   <span><?php esc_html_e( '501(c)(3) nonprofit · Gifts are tax-deductible', 'taum' ); ?></span>
  </div>
 </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
