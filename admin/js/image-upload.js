jQuery(document).ready(function() {

    var frame;
    jQuery('body').delegate('.upload-icon-image', 'click', function() {
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( frame ) {
          frame.open();
          return;
        }

        targetfield = jQuery(this).prev('.icon-image-url');

        // Create the media frame.
        frame = wp.media({
            title: 'Choose An Image',
            button: {
            text: 'Select',
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        frame.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = frame.state().get('selection').first().toJSON();
            if(attachment.sizes.thumbnail) {
                imgurl = attachment.sizes.thumbnail.url;
            } else {
                imgurl = attachment.url;
            }
            targetfield.attr("value", imgurl);
        });

        // Finally, open the modal
        frame.open();
    });
});
