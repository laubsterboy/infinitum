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

    registerBlockType('infinitum/drawer', {
        edit: function ( props ) {
			let drawerPosts = useSelect((select) => {
				return select('core').getEntityRecords('postType', 'drawer', {status: 'publish', 'per_page': -1, 'orderby': 'title', 'order': 'asc'});
			});

			let options = [];

			if (drawerPosts) {
				options.push({value: 0, label: 'Auto'});

				drawerPosts.forEach((drawer) => {
					options.push({value: drawer.id, label: drawer.title.rendered});
				});
			} else {
				options.push({value: 0, label: 'Auto'});
			}

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
								el (SelectControl,
									{
										key: 'select',
										label: 'Drawer',
										help: 'Add, or edit, Drawers posts (under Appearance) to make changes to this list.',
										value: props.attributes.drawerID,
										options: options,
										onChange: (newDrawerID) => {
											props.setAttributes({drawerID: newDrawerID})
										}
									}
								),
								el (TextControl,
									{
										key: 'labelOpen',
										label: 'Open Label',
										value: props.attributes.labelOpen,
										onChange: (newLabel) => {
											props.setAttributes({labelOpen: newLabel})
										}
									}
								),
								el(ToggleControl,
									{
										key: 'showIconOpen',
										label: 'Show Open Icon',
										checked: props.attributes.showIconOpen,
										onChange: (newChecked) => {
											props.setAttributes({showIconOpen: newChecked});
										}
									}
								),
								el (TextControl,
									{
										key: 'labelClose',
										label: 'Close Label',
										value: props.attributes.labelClose,
										onChange: (newLabel) => {
											props.setAttributes({labelClose: newLabel})
										}
									}
								),
								el(ToggleControl,
									{
										key: 'showIconClose',
										label: 'Show Close Icon',
										checked: props.attributes.showIconClose,
										onChange: (newChecked) => {
											props.setAttributes({showIconClose: newChecked});
										}
									}
								),
								el(ToggleControl,
									{
										key: 'nestCloseButton',
										label: 'Nest Close Button',
										help: props.attributes.nestCloseButton ? 'The close button is nested inside the popup modal, which is recommended' : 'The close button is a sibling of the open button, which should only be used in very specific situations',
										checked: props.attributes.nestCloseButton,
										onChange: (newChecked) => {
											props.setAttributes({nestCloseButton: newChecked});
										}
									}
								)
							]
						)
					)
				),
				/*el(BlockControls,
					{
						key: 'controls'
					},
					el(BlockAlignmentToolbar,
						{
							value: props.attributes.blockAlignment,
							onChange: (newAlignment) => {
								props.setAttributes({blockAlignment: newAlignment === undefined ? 'none' : newAlignment});
							}
						}	
					)
				),*/
				el('div',
					useBlockProps({
						key: 'content'
					}),
					el(ServerSideRender,
						{
							block: 'infinitum/drawer',
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