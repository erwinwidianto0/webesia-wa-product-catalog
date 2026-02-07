jQuery(document).ready(function ($) {
    const modal = $('#wpwa-order-modal');
    const closeBtn = $('.wpwa-close');
    const orderForm = $('#wpwa-order-form');

    let currentProduct = {
        id: 0,
        name: '',
        price: 0,
        min: 1
    };

    $('.wpwa-trigger-order').on('click', function (e) {
        e.preventDefault(); // Prevent default link behavior (double action)

        // Capture data attributes
        currentProduct.id = $(this).data('id');
        currentProduct.name = $(this).data('name');

        // Populate ALL instances of hidden fields (fix for duplicate modals)
        $('input[name="product_id"]').val(currentProduct.id);

        // Reset defaults for all instances
        $('input[name="qty"]').val(1);

        // Fix NaN: Robust check. If data-price is missing/empty, default to 0. 
        // Also handle "Rp" prefixes or commas if user mistakenly put formatted string in data-price
        var rawPrice = $(this).attr('data-price');
        currentProduct.price = parseFloat(rawPrice);

        if (isNaN(currentProduct.price)) {
            currentProduct.price = 0;
        }

        currentProduct.min = parseInt($(this).data('min')) || 1;

        $('input[name="product_id"]').val(currentProduct.id);
        $('#wpwa-modal-product-name').text(currentProduct.name);
        $('input[name="qty"]').val(currentProduct.min).attr('min', currentProduct.min);

        updateTotal();
        modal.fadeIn();

        // Force flex display for desktop centering logic to work
        modal.css('display', 'flex');
    });

    closeBtn.on('click', function () {
        modal.fadeOut();
    });

    $(window).on('click', function (event) {
        if ($(event.target).is(modal)) {
            modal.fadeOut();
        }
    });

    $('#wpwa-qty').on('input change', function () {
        updateTotal();
    });

    function updateTotal() {
        var qtyVal = $('#wpwa-qty').val();
        var qty = parseInt(qtyVal);

        // Paranoid check: If NaN, null, undefined, or < 1, default to 1
        if (!qty || isNaN(qty) || qty < 1) {
            qty = 1;
        }

        var price = parseFloat(currentProduct.price);

        // Paranoid check: If price is invalid, default to 0
        if (!price || isNaN(price)) {
            price = 0;
        }

        var total = qty * price;

        // Final check on total
        if (!total || isNaN(total)) {
            total = 0;
        }

        try {
            // Use custom currency symbol
            var currencySymbol = wpwa_ajax.currency_symbol || 'Rp';
            var currencyCode = (currencySymbol === 'Rp' || currencySymbol === 'IDR') ? 'IDR' : 'USD';

            // If it's IDR or RP, use specific ID formatting
            if (currencyCode === 'IDR') {
                const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(total);
                // Replace IDR with custom symbol if needed (e.g. if symbol is 'Rp' but Intl gives 'IDR')
                $('#wpwa-modal-total-display').text(formatted.replace('IDR', 'Rp'));
            } else {
                // For others, just append symbol
                $('#wpwa-modal-total-display').text(currencySymbol + ' ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            }
        } catch (e) {
            // Absolute fallback
            var currencySymbol = wpwa_ajax.currency_symbol || 'Rp';
            $('#wpwa-modal-total-display').text(currencySymbol + ' ' + total);
        }
    }

    orderForm.on('submit', function (e) {
        e.preventDefault();

        // Form validation removed as per user request
        // Proceed directly to submission

        const submitBtn = $('.wpwa-submit-order');
        submitBtn.prop('disabled', true).text('Processing...');

        // Context-aware data collection (Dynamic Fields Support)
        const $form = $(this);
        const formData = {
            action: 'wpwa_submit_order',
            nonce: wpwa_ajax.nonce,
            product_id: $form.find('#wpwa-product-id').val(),
            qty: $form.find('#wpwa-qty').val()
        };

        // Collect all dynamic fields
        $form.find('[name^="wpwa_field_"]').each(function () {
            formData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: wpwa_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    window.location.href = response.data.wa_url;
                } else {
                    alert('Error: ' + response.data);
                    submitBtn.prop('disabled', false).text('Send Order via WhatsApp');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
                submitBtn.prop('disabled', false).text('Send Order via WhatsApp');
            }
        });
    });

    // Mobile Filter Sidebar Toggle
    $('.wpwa-mobile-filter-trigger').on('click', function () {
        $('.wpwa-sidebar.wpwa-mobile-only').addClass('wpwa-active');
        $('body').addClass('wpwa-modal-open');
    });

    // Close mobile filter when clicking outside
    $(document).on('click', function (e) {
        if ($(e.target).data('wpwa-is-sidebar') === false || $(e.target).hasClass('wpwa-catalog-layout')) {
            $('.wpwa-sidebar.wpwa-mobile-only').removeClass('wpwa-active');
            $('body').removeClass('wpwa-modal-open');
        }
    });

    // We'll also close it if they click the sidebar background but not the widget itself
    $('.wpwa-sidebar.wpwa-mobile-only').on('click', function (e) {
        if (e.target === this) {
            $(this).removeClass('wpwa-active');
            $('body').removeClass('wpwa-modal-open');
        }
    });

    // Custom Product Tabs switching logic
    $('.wpwa-tab-trigger').on('click', function () {
        var $tabs = $(this).closest('.wpwa-product-tabs');
        var tabId = $(this).data('tab');

        // Update Buttons
        $tabs.find('.wpwa-tab-trigger').removeClass('active');
        $(this).addClass('active');

        // Update Panels
        $tabs.find('.wpwa-tab-panel').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    // --- Product Gallery Logic ---
    // Canvas/Main Image click -> Open Lightbox
    $(document).on('click', '.wpwa-tokped-main-image, .wpwa-main-img', function (e) {
        // Find the closest wrapper to ensure we target the correct instance (in case of multiple widgets)
        var $wrapper = $(this).closest('.wpwa-tokped-gallery');
        var src = $wrapper.find('.wpwa-main-img').attr('src');

        // If lightbox is global, use ID. If specific, class. Assuming ID for now based on template.
        $('#wpwa-lightbox-img').attr('src', src);
        $('#wpwa-lightbox').css('display', 'flex').hide().fadeIn(); // Ensure flex for centering
    });

    // Lightbox Close
    $(document).on('click', '.wpwa-lightbox-close, .wpwa-lightbox', function (e) {
        if (e.target !== document.getElementById('wpwa-lightbox-img')) {
            $('#wpwa-lightbox').fadeOut();
        }
    });

    // Close on Escape key
    $(document).on('keydown', function (e) {
        if (e.key === "Escape") {
            $('#wpwa-lightbox').fadeOut();
        }
    });

    // Thumbnail Click
    $(document).on('click', '.wpwa-tokped-thumb', function () {
        var $wrapper = $(this).closest('.wpwa-tokped-gallery');
        var imageUrl = $(this).attr('data-image');

        if (imageUrl) {
            $wrapper.find('.wpwa-main-img').attr('src', imageUrl);
            $wrapper.find('.wpwa-tokped-thumb').removeClass('active');
            $(this).addClass('active');
        }
    });

    // Slider Arrows Navigation
    $(document).on('click', '.wpwa-prev', function () {
        var $wrapper = $(this).closest('.wpwa-tokped-thumb-wrapper');
        var $thumbContainer = $wrapper.find('.wpwa-tokped-thumbnails');
        var scrollAmount = 150;

        $thumbContainer.animate({
            scrollLeft: "-=" + scrollAmount
        }, 200);
    });

    $(document).on('click', '.wpwa-next', function () {
        var $wrapper = $(this).closest('.wpwa-tokped-thumb-wrapper');
        var $thumbContainer = $wrapper.find('.wpwa-tokped-thumbnails');
        var scrollAmount = 150;

        $thumbContainer.animate({
            scrollLeft: "+=" + scrollAmount
        }, 200);
    });
});
