(function (blockEditor, blocks, components, data, editor, element, serverSideRender) {
    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
		BlockControls = editor.BlockControls,
		BlockAlignmentToolbar = editor.BlockAlignmentToolbar,
		ColorPaletteControl = editor.ColorPaletteControl,
		InspectorControls = blockEditor.InspectorControls,
        useBlockProps = blockEditor.useBlockProps,
		useSelect = data.useSelect,
		Card = components.Card,
		CardBody = components.CardBody,
		SelectControl = components.SelectControl,
		TextControl = components.TextControl,
		ToggleControl = components.ToggleControl,
		ServerSideRender = serverSideRender;

    registerBlockType('infinitum/breadcrumbs', {
        edit: function ( props ) {
            return [
				el(InspectorControls,
					{
						key: 'settings',
						group: 'settings'
					},
					el(Card,
						{},
						el(CardBody,
							{},
							[
								el (TextControl,
									{
										key: 'separator',
										label: 'Separator',
										value: props.attributes.separator,
										onChange: (newSeparator) => {
											props.setAttributes({separator: newSeparator})
										}
									}
								)
							]
						)
					)
				),
				el('div',
					useBlockProps({
						key: 'content'
					}),
					el(ServerSideRender,
						{
							block: 'infinitum/breadcrumbs',
							attributes: props.attributes
						}
					)
				)
			];
        }
    });
})(
    window.wp.blockEditor,
	window.wp.blocks,
	window.wp.components,
	window.wp.data,
	window.wp.editor,
    window.wp.element,
	window.wp.serverSideRender
);