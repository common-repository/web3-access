( function( blocks, editor, i18n, element, components, _ ) {
	var el = wp.element.createElement;
	var ChildBlocks = wp.blockEditor.InnerBlocks;
	var DefaultBlocks = wp.blockEditor.DefaultBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	const { Fragment } = element;
	const { SelectControl, Panel, PanelBody, PanelRow } = components;
    const { serverSideRender } = wp;
    const metapress_products = [];
    var metapress_product_list = metapressblockcontent.product_list;

    jQuery(metapress_product_list).each(function(index, product) {
        metapress_products.push( { label: product.product_name, value: product.product_id } );
    });
    blocks.registerBlockType( 'metapress-blocks/restricted-content-block', {
        title: 'Web3 Access Restricted Content',
        description: 'Restrict content and require a payment via browser wallet to access it.',
        category: 'layout',
        icon: 'editor-expand',
        attributes: {
            product_id: {
                type: 'number',
                default: 0
            },
				},
				supports: {
					anchor: true,
					multiple: false
				},
        edit: function( props ) {

            return [
                el( Fragment, {},
                    el( InspectorControls, {},
                        el( PanelBody, { title: 'Web3 Access Settings', initialOpen: true },
                            el( PanelRow, {},
                                el( SelectControl,
                                    {
                                        label: 'Select A Product',
                                        value: props.attributes.product_id,
                                        options: metapress_products,
                                        onChange: function( value ) {
                                            props.setAttributes( { product_id: parseInt( value ) } );
                                        }
                                    }
                                )
															),
														)
													)
												),
						 el( 'div', { className: 'metapress-restricted-content', dataProductid: props.attributes.product_id },
							 el(ChildBlocks, DefaultBlocks)
						 )
            ]
        },
        save: function( props ) {
			return (
				el( 'div', { className: 'metapress-restricted-content', dataProductid: props.attributes.product_id },
					el(ChildBlocks.Content)
				)
			);
        },
    } );

} )(
    window.wp.blocks,
    window.wp.editor,
    window.wp.i18n,
    window.wp.element,
    window.wp.components,
    window._,
);
