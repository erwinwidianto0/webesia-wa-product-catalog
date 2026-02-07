jQuery(document).ready(function ($) {
    console.log('WPWA Gallery: Script v1.0.2 loaded');

    // Robust Sidebar Force: Move Gallery to sidebar if it's in the main area
    function forceSidebarGallery() {
        var $galleryBox = $('#wpwa_product_gallery').closest('.postbox');
        var $sidebar = $('#side-sortables');
        var $featuredImageBox = $('#postimagediv');

        if ($galleryBox.length && $sidebar.length) {
            if ($galleryBox.parent('#postbox-container-2').length || $galleryBox.parent('#normal-sortables').length || $galleryBox.parent('#advanced-sortables').length) {
                if ($featuredImageBox.length) {
                    $galleryBox.insertAfter($featuredImageBox);
                } else {
                    $sidebar.append($galleryBox);
                }
                $(document).trigger('postboxes-order-changed');
            }
        }
    }

    forceSidebarGallery();
    setTimeout(forceSidebarGallery, 500);

    var product_gallery_frame;
    var $image_gallery_ids = $('#wpwa_product_gallery_ids');
    var $product_images = $('.wpwa-gallery-images');
    var $status = $('.wpwa-gallery-status');

    /**
     * Better Post ID Detection
     */
    function getPostID() {
        // 1. Try data attribute
        var post_id = $image_gallery_ids.data('post-id');

        // 2. Try URL
        if (!post_id || post_id === '0' || post_id === 0) {
            var urlParams = new URLSearchParams(window.location.search);
            post_id = urlParams.get('post') || urlParams.get('post_id');
        }

        // 3. Try hidden field #post_ID (standard WP)
        if (!post_id || post_id === '0' || post_id === 0) {
            post_id = $('#post_ID').val();
        }

        return post_id;
    }

    /**
     * AJAX Save Logic
     */
    function saveGalleryAjax(ids_string) {
        var current_post_id = getPostID();

        if (!current_post_id || current_post_id === '0' || current_post_id === 0) {
            console.log('WPWA Gallery: Skipping AJAX save (No Post ID yet).');
            return;
        }

        console.log('WPWA Gallery: Saving via AJAX for:', current_post_id);
        $status.text('Saving...').css('color', '#f0b849').show();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wpwa_save_gallery',
                post_id: current_post_id,
                gallery_ids: ids_string,
                nonce: $('#wpwa_gallery_nonce').val()
            },
            success: function (response) {
                if (response.success) {
                    $status.text('✓ Gallery saved').css('color', '#46b450').show();
                    setTimeout(function () { $status.fadeOut(); }, 2000);
                } else {
                    $status.text('✗ Save failed: ' + response.data).css('color', '#dc3232').show();
                }
            },
            error: function () {
                $status.text('✗ Save error').css('color', '#dc3232').show();
            }
        });
    }

    // Add gallery images
    $('#wpwa-add-gallery-images').on('click', function (e) {
        e.preventDefault();

        if (product_gallery_frame) {
            product_gallery_frame.open();
            return;
        }

        product_gallery_frame = wp.media({
            title: 'Add Images to Product Gallery',
            button: { text: 'Add to gallery' },
            multiple: true
        });

        product_gallery_frame.on('select', function () {
            var selection = product_gallery_frame.state().get('selection');
            var current_value = $image_gallery_ids.val();
            var ids = current_value ? current_value.split(',').filter(function (id) { return id.trim() !== ''; }) : [];

            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                if (attachment.id && ids.indexOf(attachment.id.toString()) === -1) {
                    ids.push(attachment.id.toString());
                    var thumbUrl = attachment.url;
                    if (attachment.sizes && attachment.sizes.thumbnail) thumbUrl = attachment.sizes.thumbnail.url;

                    var $li = $('<li class="image" data-attachment_id="' + attachment.id + '"></li>');
                    $li.append('<img src="' + thumbUrl + '" />');
                    $li.append('<a href="#" class="delete" title="Delete image"></a>');
                    $product_images.append($li);
                }
            });

            var new_value = ids.join(',');
            $image_gallery_ids.val(new_value);
            saveGalleryAjax(new_value);
        });

        product_gallery_frame.open();
    });

    // Delete image
    $product_images.on('click', 'a.delete', function (e) {
        e.preventDefault();
        $(this).closest('li').remove();

        var ids = [];
        $product_images.find('li.image').each(function () {
            var id = $(this).data('attachment_id');
            if (id) ids.push(id.toString());
        });

        var new_value = ids.join(',');
        $image_gallery_ids.val(new_value);
        saveGalleryAjax(new_value);
    });

    // Character Counter for Excerpt
    var $excerpt = $('#wpwa_custom_excerpt');
    var $count = $('#wpwa-char-count');
    if ($excerpt.length && $count.length) {
        function updateCount() {
            var len = $excerpt.val().length;
            $count.text(len);
            $count.css('color', len >= 90 ? '#ef4444' : '#2271b1');
        }
        $excerpt.on('input', updateCount);
        updateCount();
    }
});
