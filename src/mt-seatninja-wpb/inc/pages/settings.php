<div class="wrap ultimate-icons-wrap">
    <div class="wrap-container">
        <div class="ultimate-icons-heading">
            <h1><?php esc_html_e( 'Seat Ninja for WPBakery Page Builder Settings', 'mt-snj' ) ?></h1>
            <h3>
                <?php esc_html_e( 'Congratulations! You are about to use a useful WordPress plugin
				that gives you the great ability to get a table booking through seatninja.com.
				From where ever you are, and then show up when your table is ready and know exactly
				what you are going to order ahead of time, like a ninja!',
                    'mt-snj' ) ?>
            </h3>
            <div class="mtui-logo">
            </div>
        </div>
        <div class="ultimate-icons-body">
            <div class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( '?page=mt-snj-general' ) ) ?>">
                    <?php esc_html_e( 'General Settings', 'mt-snj' ) ?></a>
                <a class="nav-tab" href="<?php echo esc_url( admin_url( '?page=mtui-about' ) ) ?>">
                    <?php esc_html_e( 'About', 'mt-snj' ) ?></a>
            </div>
            <div class="container tab-content-wrapper">
                <form method="post" id="mt-snj-settings">
                    <input type="hidden" name="icon-fonts" id="icon-fonts">
                    <table class="form-table">
                        <tr>
                            <th><label for="api-key"><?php esc_html_e( 'Seat Ninja API Key', 'mt-snj' ) ?></label></th>
                            <td><input class="widefat" id="api-key" type="text"/></td>
                        </tr>
                        <tr>
                            <th><label for="customer-token"><?php esc_html_e( 'Customer AuthToken',
                                        'mt-snj' ) ?></label></th>
                            <td><input class="widefat" id="customer-token" type="text"/></td>
                        </tr>
                    </table>
                    <button type="submit" name="submit" id="submit-btn" class="button button-primary">Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
