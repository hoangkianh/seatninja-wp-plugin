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
        <a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( '?page=mt-snj-general' ) ) ?>">
            <?php esc_html_e( 'General Settings', 'mt-snj' ) ?></a>
        <a class="nav-tab" href="<?php echo esc_url( admin_url( '?page=mt-snj-about' ) ) ?>">
            <?php esc_html_e( 'About', 'mt-snj' ) ?></a>
      </div>
      <div class="container tab-content-wrapper">
        <form method="post" id="mt-snj-settings">
          <table class="form-table">
            <tr>
              <th><label for="api-key"><?php esc_html_e( 'Seat Ninja API Key', 'mt-snj' ) ?></label></th>
              <td><input class="widefat" id="api-key" type="text" value="<?php echo esc_attr($keys['api-key']) ?>"/></td>
            </tr>
            <tr>
              <th><label for="customer-token"><?php esc_html_e( 'Customer AuthToken', 'mt-snj' ) ?></label></th>
              <td><input class="widefat" id="customer-token" type="text" value="<?php echo esc_attr($keys['customer-token']) ?>"/></td>
            </tr>
            <tr>
              <th><label for="google-api-key"><?php esc_html_e( 'Google Time Zone API Key', 'mt-snj' ) ?></label></th>
              <td>
                <input class="widefat" id="google-api-key" type="text" value="<?php echo esc_attr($keys['google-api-key']) ?>"/>
                <p class="help"><?php
                  printf(__('Get Time Zone API Key <a href="%s" target="_blank">here</a>', 'mt-snj'), 'https://developers.google.com/maps/documentation/timezone/get-api-key')
                ?></p>
              </td>
            </tr>
            <tr>
              <th><label for="email-from"><?php esc_html_e( 'Send Confirmation Emails FROM', 'mt-snj' ) ?></label></th>
              <td><input class="widefat" id="email-from" type="text" value="<?php echo esc_attr($keys['email-from']) ?>"/></td>
            </tr>
            <tr>
              <th><label for="your-name"><?php esc_html_e( 'Your Name', 'mt-snj' ) ?></label></th>
              <td><input class="widefat" id="your-name" type="text" value="<?php echo esc_attr($keys['your-name']) ?>"/></td>
            </tr>
            <tr>
              <th><label for="email-to"><?php esc_html_e( 'Send Confirmation Emails TO', 'mt-snj' ) ?></label></th>
              <td><input class="widefat" id="email-to" type="text" value="<?php echo esc_attr($keys['email-to']) ?>"/></td>
            </tr>
          </table>
          <button type="submit" name="submit" id="submit-btn" class="button button-primary">Save Changes
          </button>
          <button type="button" id="clear-cache" class="button">Clear Cache</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
