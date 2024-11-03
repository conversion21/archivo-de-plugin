jQuery(document).ready(function($) {
    $('#select-all').on('click', function() {
        $('input[name="submissions[]"]').prop('checked', this.checked);
    });
});
