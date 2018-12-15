<?php
/*
Plugin Name:  Simple MailChimp Form
Plugin URI:   https://www.mymentech.com
Description:  Basic WordPress Plugin to display simple inline MailChimp form
Version:      9.9.9
Author:       MymenTech
Author URI:   https://www.mymentech.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  mailchimp-form
*/
include_once plugin_dir_path(__FILE__) . 'mailchimp-form-settings.php';

class MailChimpForm
{
    public  $options = array();
    private $VERSION = '';

    public function __construct() {
        add_action('plugins_loaded', array($this, 'mailchimp_form_plugin_textdomain'));
        add_action('init', array($this, 'set_mailchimp_form_data'));
        add_action('wp_enqueue_scripts', array($this, 'mailchimp_form_scripts'));
        add_shortcode('mailchimp-form', array($this, 'create_mailchimpform_shortcode'));
        add_action('wp_ajax_nopriv_mailchimp_connect', array($this, 'mailchimp_form_mailchimp_connect'));
        add_action('wp_ajax_mailchimp_connect', array($this, 'mailchimp_form_mailchimp_connect'));


        $this->VERSION = time();

    }


    public function mailchimp_form_plugin_textdomain() {
        load_plugin_textdomain('mailchimp-form', false, plugin_dir_path(__FILE__) . "/languages");
    }


    function mailchimp_form_mailchimp_connect() {
        if (check_ajax_referer('mailchimp_connect', 'smf_s')) {
            $data['firstname'] = $_POST['smf_name'];
            $data['lastname']  = '';
            $data['phone']     = $_POST['smf_phone'];
            $data['email']     = $_POST['smf_email'];
            $data['status']    = 'subscribed';
            $response          = $this->syncMailchimp($data);

            echo $response;
            die();
        }
    }


    public function syncMailchimp($data) {
        $apiKey = $this->options['api_key'];
        $listId = $this->options['list_id'];

        $memberId   = md5(strtolower($data['email']));
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url        = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

        $json = json_encode([
            'email_address' => $data['email'],
            'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
            'merge_fields'  => [
                'FNAME' => $data['firstname'],
                'LNAME' => $data['lastname'],
                'PHONE' => $data['phone']
            ]
        ]);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode;
    }


    public function set_mailchimp_form_data() {
        $options                  = get_option('mailchimp_form_settings');
        $this->options['api_key'] = (isset($options['mailchimp_form_text_api_key']) && '' != $options['mailchimp_form_text_api_key']) ? $options['mailchimp_form_text_api_key'] : __('xxxxxxxx', 'mailchimp-form');
        $this->options['list_id'] = (isset($options['mailchimp_form_text_api_list']) && '' != $options['mailchimp_form_text_api_list']) ? $options['mailchimp_form_text_api_list'] : __('xxxxxxxx', 'mailchimp-form');
        $this->options['name']    = (isset($options['mailchimp_form_text_name']) && '' != $options['mailchimp_form_text_name']) ? $options['mailchimp_form_text_name'] : __('Name', 'mailchimp-form');
        $this->options['email']   = (isset($options['mailchimp_form_text_email']) && '' != $options['mailchimp_form_text_email']) ? $options['mailchimp_form_text_email'] : __('E-mail', 'mailchimp-form');
        $this->options['phone']   = (isset($options['mailchimp_form_text_phone']) && '' != $options['mailchimp_form_text_phone']) ? $options['mailchimp_form_text_phone'] : __('Phone', 'mailchimp-form');
        $this->options['submit']  = (isset($options['mailchimp_form_text_submit']) && '' != $options['mailchimp_form_text_submit']) ? $options['mailchimp_form_text_submit'] : __('Subscribe', 'mailchimp-form');
    }

    public function mailchimp_form_scripts() {
        wp_enqueue_style('mailchimp-form-style', plugin_dir_url(__FILE__) . 'assets/public/css/mailchimp-form.css', null, $this->VERSION);
        wp_enqueue_script('mailchimp-form-js', plugin_dir_url(__FILE__) . 'assets/public/js/mailchimp-form.js', array('jquery'), $this->VERSION);
        $ajaxurl = admin_url('admin-ajax.php');

        wp_localize_script('mailchimp-form-js', 'wpfurls', array('ajaxurl' => $ajaxurl));

    }

    // Create Shortcode mailchimp-form
    // Shortcode: [mailchimp-form name_placeholder="Name" phone_placeholder="Phone" email_placeholder="E-mail" submit_text="Subscribe"]Content[/mailchimp-form]
    function create_mailchimpform_shortcode($atts) {

        $atts = shortcode_atts(
            array(
                'name_placeholder'  => $this->options['name'],
                'phone_placeholder' => $this->options['phone'],
                'email_placeholder' => $this->options['email'],
                'submit_text'       => $this->options['submit'],
            ),
            $atts,
            'mailchimp-form'
        );

        $name_placeholder  = $atts['name_placeholder'];
        $phone_placeholder = $atts['phone_placeholder'];
        $email_placeholder = $atts['email_placeholder'];
        $submit_text       = $atts['submit_text'];

        $nonce  = wp_create_nonce('mailchimp_connect');
        $loader = plugin_dir_url(__FILE__) . 'assets/public/img/ajax-loader.gif';


        $mailchimp_form = <<<EOD
<div class="smf-container">
<div class="mailchimp_form_wrap">
    <div class="error-message"></div>
    <div class="success-message"><h3></h3></div>
    <div class="loader">
            <img src="{$loader}" alt="">
        </div>
    <form action="">
        <div class="mailchim_row">
            <div class="input_container">
                <input type="text" name="name" id="mailchimp_name" placeholder="{$name_placeholder}" required />
            </div>
            <div class="input_container">
                <input type="tel" id="mailchimp_phone" name="mailchimp_phone" placeholder="{$phone_placeholder}" required/>
            </div>
        </div>
        <input type="hidden" id="smf_nonce" value="{$nonce}">
        <div class="mailchim_row">
            <div class="input_container">
                <input type="email" name="mailchimp_email" id="mailchimp_email" placeholder="{$email_placeholder}" required/>
            </div>
            <button id="mailchimp-submit" type="submit">{$submit_text}</button>
        </div>
    </form>
</div>
</div>
EOD;

        return $mailchimp_form;

    }


}

new MailChimpForm();