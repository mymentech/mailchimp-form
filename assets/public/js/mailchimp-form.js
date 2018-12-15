;(function($){
    $('document').ready(function(){

        $("#mailchimp-submit").on('click', function (e) {
            $(".mailchimp_form_wrap .loader").css("display", "block");
            var name = $("#mailchimp_name").val();
            var phone = $("#mailchimp_phone").val();
            var email = $("#mailchimp_email").val();
            var nonce = $("#smf_nonce").val();

            $.post(wpfurls.ajaxurl, {
                action: "mailchimp_connect",
                smf_name:name,
                smf_phone:phone,
                smf_email:email,
                smf_s:nonce
            }, function (data) {
                $(".mailchimp_form_wrap .loader").css("display", "none");
                data = parseInt(data);
                if(200===data){
                    var html_success = "Thanks, Your data has been recorded successfully!";
                    $('.mailchimp_form_wrap .error-message').text('');
                    $('.mailchimp_form_wrap .success-message h3').text(html_success);
                    $('.mailchimp_form_wrap form').remove();
                }else{
                    var html_err = "Something went wrong, please try again";
                    $('.mailchimp_form_wrap .error-message').text(html_err);
                }
            });

            return false;
        });
    });
})(jQuery);