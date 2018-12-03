<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$keys = MT_SeatNinja::get_snj_keys();
?>
<div class="wrap mt-snj-wrap">
  <div class="wrap-container">
    <div class="mt-snj-heading">
      <h1><?php esc_html_e( 'Seat Ninja for WPBakery Page Builder Settings', 'mt-snj' ) ?></h1>
      <h3>
          <?php esc_html_e( 'Congratulations! You are about to use a useful WordPress plugin
                that gives you the great ability to get a table booking through seatninja.com. 
                From where ever you are, and then show up when your table is ready and know exactly
                what you are going to order ahead of time, like a ninja!',
              'mt-snj' ) ?>
      </h3>
      <div class="mt-snj-logo">
      </div>
    </div>
    <div class="mt-snj-body">
      <div class="nav-tab-wrapper">
        <a class="nav-tab" href="<?php echo esc_url( admin_url( '?page=mt-snj-general' ) ) ?>">
            <?php esc_html_e( 'General Settings', 'mt-snj' ) ?></a>
        <a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( '?page=mt-snj-about' ) ) ?>">
            <?php esc_html_e( 'About', 'mt-snj' ) ?></a>
      </div>
      <div class="container tab-content-wrapper">
        <h1>Seat Ninja for WPBakery Page Builder</h1>
        <p>Made with Passion ❤️ from WPMortar</p>
      </div>
    </div>
  </div>
</div>
<?php
