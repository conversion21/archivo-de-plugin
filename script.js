jQuery(document).ready(function ($) {
    $('#wodx-form').on('submit', function (e) {
        e.preventDefault();

        var data = {
            action: 'wodx_form_submit',
            nonce: $('input[name="nonce"]').val(),
            name: $('#wodx-name').val(),
            email: $('#wodx-email').val()
        };

        $.post(wodxForm.ajax_url, data, function (response) {
            if (response.success) {
                $('#wodx-form-response').html('<p>' + response.data.message + '</p>');
            } else {
                $('#wodx-form-response').html('<p>' + response.data.message + '</p>');
            }
        });
    });
});
