(function ($) {
    $(document).ready(function () {
        $('body').on('click', '.theme_media_button.button', function (e) {
            e.preventDefault();
            var custom_uploader;
            var parent = '#' + $(this).parents('.widget').attr('id');

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function () {
                attachment = custom_uploader.state().get('selection').first().toJSON();
                $(parent).find('.custom_media_id').val(attachment.id);
                $(parent).find('.custom_media_url').val(attachment.url).trigger('change');
                if ($(parent).find('.custom_media_image').length) {
                    $(parent).find('.custom_media_image').attr('src', attachment.url).css('display', 'block');
                }
                $('#upload_image').val(attachment.url);
            });

            //Open the uploader dialog
            custom_uploader.open();
        });

        $('body').on("change mousemove", '.range-input', function () {
            $(this).next().html($(this).val());
        });

    });
})(jQuery);