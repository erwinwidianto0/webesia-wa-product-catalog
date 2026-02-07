(function (blocks, element, components, i18n, editor, serverSideRender) {
    var el = element.createElement;
    var __ = i18n.__;
    var ServerSideRender = serverSideRender;

    blocks.registerBlockType('webesia/related-products', {
        title: __('WA Related Products', 'webesia-wa-product-catalog'),
        icon: 'admin-links',
        category: 'widgets',
        attributes: {
            limit: {
                type: 'number',
                default: 4
            }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', { className: 'wpwa-block-related-wrapper' },
                el(editor.InspectorControls, {},
                    el(components.PanelBody, { title: __('Settings', 'webesia-wa-product-catalog') },
                        el(components.RangeControl, {
                            label: __('Number of products', 'webesia-wa-product-catalog'),
                            value: attributes.limit,
                            onChange: function (value) {
                                setAttributes({ limit: value });
                            },
                            min: 1,
                            max: 12
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'webesia/related-products',
                    attributes: attributes
                })
            );
        },
        save: function () {
            return null; // Rendered via PHP
        }
    });

    blocks.registerBlockType('webesia/product-gallery', {
        title: __('WA Product Gallery', 'webesia-wa-product-catalog'),
        icon: 'format-gallery',
        category: 'widgets',
        edit: function (props) {
            return el(
                element.Fragment,
                {},
                el(ServerSideRender, {
                    block: 'webesia/product-gallery',
                    attributes: props.attributes
                })
            );
        },
        save: function () {
            return null; // Rendered via PHP
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n,
    window.wp.editor,
    window.wp.serverSideRender
);
