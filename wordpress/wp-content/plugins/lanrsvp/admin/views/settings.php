<div class="lanrsvp-admin wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <form method="post" action="options.php">

        <?php settings_fields( 'lanrsvp_settings' ); ?>
        <?php do_settings_sections( 'lanrsvp_settings' ); ?>

        <table class="form-table">

            <tr valign="top">

                <th scope="row">New Option Name</th>

                <td><input type="checkbox" name="logip" value="<?php echo get_option('logip'); ?>" /></td>

            </tr>

        </table>

        <?php submit_button(); ?>

    </form>
</div>