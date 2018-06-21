(function ($) {
    $(document).ready(function () {
        $(document).foundation();

        $(".text-color .field input[data-colorpicker]").each(function () {
            var $this = $(this),
                options = JSON.parse($this.attr('data-options'));
            $this.ColorPickerSliders(options);
        });

        $('[data-dependents]').on('change', function () {
            var $this = $(this),
                attrDependents = $this.attr('data-dependents'),
                dependents = attrDependents ? JSON.parse(attrDependents) : {},
                tag = this.nodeName.toLowerCase(),
                type = 'input' === tag ? $this.attr('type') : tag,
                value = undefined;

            switch (type) {
                case 'checkbox':
                    value = $this.prop('checked') ? 1 : 0;
                    break;
                case 'radio':
                case 'select':
                    value = $this.val();
                    break;
            }

            if (dependents[value]) {
                $.each(dependents[value], function (action, selectors) {
                    $.each(selectors, function (i, selector) {
                        if ($.isFunction($(selector)[action])) {
                            $(selector)[action]();
                        }
                    })
                });
            }
        });
        $('input[type="checkbox"][data-dependents]:checked').trigger('change');
        $('input[type="radio"][data-dependents]:checked').trigger('change');
        $('select[data-dependents]').trigger('change');
    });

    $(document).ready(function() {
        // file uploads


        $(document).on('click', '.brisum-custom-field .field.image .js-add-image', function(event) {
            var $el = $(this),
                $wrap = $el.closest('.field'),
                $field = $wrap.find('input'),
                $thumb = $wrap.find('.thumb'),
                loadImageFrame;

            event.preventDefault();


            // Create the media frame.
            loadImageFrame = wp.media({
                // Set the title of the modal.
                title: $el.data( 'choose' ),
                button: {
                    text: $el.data( 'update' )
                },
                states: [
                    new wp.media.controller.Library({
                        title: $el.data( 'choose' ),
                        filterable: 'all',
                        multiple: false
                    })
                ]
            });

            // When an image is selected, run a callback.
            loadImageFrame.on( 'select', function() {
                var selection = loadImageFrame.state().get( 'selection' );

                selection.map( function( attachment ) {
                    attachment = attachment.toJSON();

                    if ( attachment.id ) {
                        var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        $thumb.append( '<img src="' + attachment_image + '" />' );

                        $field.val(attachment.id);
                    }

                    $('.js-delete-image', $wrap).show();
                    $('.js-add-image', $wrap).hide();
                });
            });

            // Finally, open the modal.
            loadImageFrame.open();
        });

        // Remove images
        $(document).on('click', '.brisum-custom-field .field.image .js-delete-image', function(event) {
            var $el = $(this),
                $wrap = $el.closest('.wrap-field'),
                $field = $wrap.find('.field input'),
                $thumb = $wrap.find('.thumb');

            $field.val('');
            $thumb.html('');
            $('.js-delete-image', $wrap).hide();
            $('.js-add-image', $wrap).show();
        });
    });
})(jQuery);
