<?php
add_action( 'admin_menu', 'mailchimp_form_add_admin_menu' );
add_action( 'admin_init', 'mailchimp_form_settings_init' );


function mailchimp_form_add_admin_menu(  ) {

    add_submenu_page( 'tools.php', 'Simple Mailchimp Form', 'Mailchimp Form Options',  'manage_options', 'simple_mailchimp_form', 'mailchimp_form_options_page' );

}


function mailchimp_form_settings_init(  ) {

    register_setting( 'pluginPage', 'mailchimp_form_settings' );

    add_settings_section(
        'mailchimp_form_pluginPage_section',
        __( 'Configure MailChimp Form Settings with correct info', 'mailchimp-form' ),
        'mailchimp_form_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'mailchimp_form_text_api_key',
        __( 'Mailchimp API Key', 'mailchimp-form' ),
        'mailchimp_form_text_api_key_render',
        'pluginPage',
        'mailchimp_form_pluginPage_section'
    );

    add_settings_field(
        'mailchimp_form_text_api_list',
        __( 'MailChimp List ID', 'mailchimp-form' ),
        'mailchimp_form_text_api_list_render',
        'pluginPage',
        'mailchimp_form_pluginPage_section'
    );

    add_settings_field(
        'mailchimp_form_text_name',
        __( 'Name Field Placeholder', 'mailchimp-form' ),
        'mailchimp_form_text_name_render',
        'pluginPage',
        'mailchimp_form_pluginPage_section'
    );

    add_settings_field(
        'mailchimp_form_text_email',
        __( 'E-mail Field Placeholder', 'mailchimp-form' ),
        'mailchimp_form_text_email_render',
        'pluginPage',
        'mailchimp_form_pluginPage_section'
    );

    add_settings_field(
        'mailchimp_form_text_phone',
        __( 'Phone Field Placeholder', 'mailchimp-form' ),
        'mailchimp_form_text_phone_render',
        'pluginPage',
        'mailchimp_form_pluginPage_section'
    );

    add_settings_field(
        'mailchimp_form_text_submit',
        __( 'Submit Button Text', 'mailchimp-form' ),
        'mailchimp_form_text_submit_render',
        'pluginPage',
        'mailchimp_form_pluginPage_section'
    );


}


function mailchimp_form_text_api_key_render(  ) {

    $options = get_option( 'mailchimp_form_settings' );
    ?>
    <input type='text' name='mailchimp_form_settings[mailchimp_form_text_api_key]' value='<?php echo esc_attr($options['mailchimp_form_text_api_key']); ?>'>
    <?php

}


function mailchimp_form_text_api_list_render(  ) {

    $options = get_option( 'mailchimp_form_settings' );
    ?>
    <input type='text' name='mailchimp_form_settings[mailchimp_form_text_api_list]' value='<?php echo esc_attr($options['mailchimp_form_text_api_list']); ?>'>
    <?php

}


function mailchimp_form_text_name_render(  ) {

    $options = get_option( 'mailchimp_form_settings' );
    ?>
    <input type='text' name='mailchimp_form_settings[mailchimp_form_text_name]' value='<?php echo esc_attr($options['mailchimp_form_text_name']); ?>'>
    <?php

}


function mailchimp_form_text_email_render(  ) {

    $options = get_option( 'mailchimp_form_settings' );
    ?>
    <input type='text' name='mailchimp_form_settings[mailchimp_form_text_email]' value='<?php echo esc_attr($options['mailchimp_form_text_email']); ?>'>
    <?php

}


function mailchimp_form_text_phone_render(  ) {

    $options = get_option( 'mailchimp_form_settings' );
    ?>
    <input type='text' name='mailchimp_form_settings[mailchimp_form_text_phone]' value='<?php echo esc_attr($options['mailchimp_form_text_phone']); ?>'>
    <?php

}


function mailchimp_form_text_submit_render(  ) {

    $options = get_option( 'mailchimp_form_settings' );
    ?>
    <input type='text' name='mailchimp_form_settings[mailchimp_form_text_submit]' value='<?php echo esc_attr($options['mailchimp_form_text_submit']); ?>'>
    <?php

}


function mailchimp_form_settings_section_callback(  ) {

    echo __( 'Find or Generate Your <a href="https://mailchimp.com/help/about-api-keys/#Find_or_Generate_Your_API_Key" target="_blank">API Key</a> & ', 'mailchimp-form' );
    echo __( '<a href="https://mailchimp.com/help/find-your-list-id/" target="_blank">List ID</a></br>', 'mailchimp-form' );

}


function mailchimp_form_options_page(  ) {

    ?>
    <form action='options.php' method='post'>

        <h2>Simple Mailchimp Form</h2>

        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>

    </form>
    <?php

}