document.addEventListener('DOMContentLoaded', function() {
    (function($) {
        

        $('#wodx-form').on('submit', function(e) {
            e.preventDefault();
            $('#wodx-form-response').html('<p>Procesando solicitud...</p>');
            $('.tnp-submit').prop('disabled', true).css('opacity', 0.5);

            var customFormData = $(this).serializeArray();
            console.log("Serialized form data custom inputs: ", customFormData);

            var newsletterFormData = $(this).find('.tnp-subscription :input').serializeArray();
            console.log("Serialized form data from newsletter: ", newsletterFormData);

            customFormData.push({ name: 'nonce', value: $('input[name="nonce"]').val() });

            var combinedFormData = $.param(newsletterFormData.concat(customFormData));
            console.log("Combined form data: ", combinedFormData);

            $.post(wodxForm.ajax_url + '?action=wodx_form_submit', $.param(customFormData), function(response) {
                console.log("Custom form data submission response: ", response);
                if (response.success) {
                    $.post('/?na=s', combinedFormData, function(newsletterResponse) {
                        console.log("Newsletter form data submission response: ", newsletterResponse);
                        $('#wodx-form-response').html('<p>' + response.data.message + '</p>');
                        var redirectUrl = response.data.redirect_url ? response.data.redirect_url : wodxForm.redirect_url;
                        if (redirectUrl) {
                            setTimeout(function() {
                                window.location.href = redirectUrl;
                            }, 500);
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error submitting newsletter form data: ", textStatus, errorThrown);
                        $('#wodx-form-response').html('<p>Error submitting newsletter form data. Please try again.</p>');
                    });
                } else {
                    $('#wodx-form-response').html('<p>' + response.data.message + '</p>');
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error submitting custom form data: ", textStatus, errorThrown);
                $('#wodx-form-response').html('<p>Error submitting custom form data. Please try again.</p>');
            });
        });
    })(jQuery);
});
