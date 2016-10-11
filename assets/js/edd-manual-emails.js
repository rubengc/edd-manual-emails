jQuery(document).ready(function($) {
    $('#tab_container form').on('submit',  function(e) {
        e.preventDefault();

        var subject = $('input[name="edd_settings[edd_manual_emails_subject]"]').val();
        var to = $('textarea[name="edd_settings[edd_manual_emails_to]"]').val().split("\n");
        var content = $('textarea[name="edd_settings[edd_manual_emails_content]"]').val();

        $('#edd-manual-emails-no-emails-error').remove();
        $('#edd-manual-emails-ajax-response').remove();

        if($('textarea[name="edd_settings[edd_manual_emails_to]"]').val().trim() != '' && to.length > 0) {
            $.ajax({
                url: ajax_auth_object["ajaxurl"],
                data: {
                    action: 'edd_manual_emails_send',
                    subject: subject,
                    to: to,
                    content: content
                },
                success: function (response) {
                    var sent_emails=0;
                    $.each(response, function( index, value ) {
                        if(value == true) {
                            sent_emails++;
                        }
                    });

                    var text = '';
                    var notice_type = 'updated';

                    if(sent_emails == to.length) {
                        text = 'Emails sent to all.';
                    } else if(sent_emails == 0) {
                        text = 'Could not send email to anyone.';
                        notice_type = 'error';
                    } else {
                        text = 'Emails sent to ' + sent_emails + ', but there are ' + (to.length - sent_emails) + ' emails that have not been able to send.';
                        notice_type = 'error';
                    }

                    $(
                        '<div id="edd-manual-emails-ajax-response" class="' + notice_type + ' notice is-dismissible">' +
                            '<p><strong>' + text + '</strong></p>' +
                            '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' +
                        '</div>'
                    ).insertAfter('.wrap-emails h1.nav-tab-wrapper');
                }
            });
        } else {
            $(
                '<div id="edd-manual-emails-no-emails-error" class="error notice is-dismissible">' +
                '<p><strong>You need set at least one recipient to send the email.</strong></p>' +
                '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' +
                '</div>'
            ).insertAfter('.wrap-emails h1.nav-tab-wrapper');
        }
    });
});