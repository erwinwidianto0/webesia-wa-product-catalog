(function (blocks, element, components, i18n) {
    var el = element.createElement;
    var __ = i18n.__;
    var registerBlockType = blocks.registerBlockType;
    var ServerSideRender = wp.serverSideRender; // Native WordPress component

    registerBlockType('webesia/product-catalog', {
        title: __('WebEsia Catalog', 'webesia-wa-product-catalog'),
        icon: 'cart',
        category: 'widgets',
        keywords: [__('product', 'webesia-wa-product-catalog'), __('catalog', 'webesia-wa-product-catalog'), __('store', 'webesia-wa-product-catalog')],
        attributes: {
            posts_per_page: {
                type: 'number',
                default: 9
            },
            posts_per_page_tablet: {
                type: 'number',
                default: 9
            },
            posts_per_page_mobile: {
                type: 'number',
                default: 9
            },
            category: {
                type: 'string',
                default: ''
            },
            filter: {
                type: 'boolean',
                default: false
            }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Fetch categories using useSelect
            var categories = wp.data.useSelect(function (select) {
                return select('core').getEntityRecords('taxonomy', 'product_category', {
                    per_page: -1,
                    hide_empty: false
                });
            }, []);

            var categoryOptions = [{ label: __('All Categories', 'webesia-wa-product-catalog'), value: '' }];

            if (categories === null) {
                categoryOptions = [{ label: __('Loading categories...', 'webesia-wa-product-catalog'), value: '' }];
            } else if (categories && categories.length > 0) {
                categories.forEach(function (cat) {
                    categoryOptions.push({ label: cat.name, value: cat.slug });
                });
            } else if (categories && categories.length === 0) {
                categoryOptions = [{ label: __('No categories found', 'webesia-wa-product-catalog'), value: '' }];
            }

            // Inspector Controls (Sidebar)
            return [
                el(wp.blockEditor.InspectorControls, { key: 'inspector' },
                    el(components.PanelBody, { title: __('Catalog Settings', 'webesia-wa-product-catalog'), initialOpen: true },
                        el(components.TextControl, {
                            label: __('Limit Products (Desktop)', 'webesia-wa-product-catalog'),
                            type: 'number',
                            value: attributes.posts_per_page,
                            onChange: function (value) {
                                setAttributes({ posts_per_page: parseInt(value) });
                            }
                        }),
                        el(components.TextControl, {
                            label: __('Limit Products (Tablet)', 'webesia-wa-product-catalog'),
                            type: 'number',
                            value: attributes.posts_per_page_tablet,
                            onChange: function (value) {
                                setAttributes({ posts_per_page_tablet: parseInt(value) });
                            }
                        }),
                        el(components.TextControl, {
                            label: __('Limit Products (Mobile)', 'webesia-wa-product-catalog'),
                            type: 'number',
                            value: attributes.posts_per_page_mobile,
                            onChange: function (value) {
                                setAttributes({ posts_per_page_mobile: parseInt(value) });
                            }
                        }),
                        el(components.SelectControl, {
                            label: __('Category', 'webesia-wa-product-catalog'),
                            value: attributes.category,
                            options: categoryOptions,
                            onChange: function (value) {
                                setAttributes({ category: value });
                            }
                        }),
                        el(components.ToggleControl, {
                            label: __('Show Filter (Sidebar)', 'webesia-wa-product-catalog'),
                            checked: attributes.filter,
                            onChange: function (value) {
                                setAttributes({ filter: value });
                            }
                        })
                    )
                ),
                // Block Preview in Editor
                el('div', { className: props.className },
                    el(ServerSideRender, {
                        block: 'webesia/product-catalog',
                        attributes: attributes
                    })
                )
            ];
        },
        save: function () {
            return null; // Rendered on PHP side
        },
    });
}(window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n));
