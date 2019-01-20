<?php

function wooacct_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    test_handle_post();
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form method="post" enctype="multipart/form-data">

          <div class="selectfile">
            <p>Please select an exported CSV file from 'WooCommerce/Export Orders'</p>
            <input type='file' class='button-secondary' id='test_upload_pdf' name='test_upload_pdf'></input>
          </div>
                <?php submit_button('Convert CSV') ?>
            <?php
            // // output security fields for the registered setting "wporg_options"
            // settings_fields('wooacct_options');
            // // output setting sections and their fields
            // // (sections are registered for "wporg", each field is registered to a specific section)
            // do_settings_sections('wooacct');
            // // output save settings button
            // submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}
function test_handle_post(){
        // First check if the file appears on the _FILES array
        if(isset($_FILES['test_upload_pdf'])){
                $pdf = $_FILES['test_upload_pdf'];

                //TODO: This is where we will put the code to parse the file and convert it to new CSV

                // Use the wordpress function to upload
                // test_upload_pdf corresponds to the position in the $_FILES array
                // 0 means the content is not associated with any other posts
                $uploaded=media_handle_upload('test_upload_pdf', 0);
                // Error checking using WP functions
                if(is_wp_error($uploaded)){
                        echo "Error uploading file: " . $uploaded->get_error_message();
                }else{
                        echo "File upload successful!";
                }
        }
}
function wooacct_options_page()
{
    add_menu_page(
        'OSC Account Export',
        'OSC Account Export',
        'manage_options',
        'wooacct',
        'wooacct_options_page_html',
        'dashicons-analytics', //plugin_dir_url(__FILE__) . 'media/icon_osc.png',
        20
    );
}
add_action('admin_menu', 'wooacct_options_page');
