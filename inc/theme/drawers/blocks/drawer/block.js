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
		PanelBody = components.PanelBody,
		SelectControl = components.SelectControl,
		TextControl = components.TextControl,
		ToggleControl = components.ToggleControl,
		ServerSideRender = serverSideRender;

    registerBlockType('infinitum/drawer', {
		icon: 'menu',
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
					el(PanelBody,
						{
							title: 'Settings'
						},
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
						)
					),
					el(PanelBody,
						{
							title: 'Advanced Settings',
							initialOpen: false
						},
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
						),
						el(ToggleControl,
							{
								key: 'scrollToViewModal',
								label: 'Scroll to Modal',
								help: props.attributes.scrollToViewModal ? 'When the drawer is opened the window scrolls until the open element is at the edge matching the auto offset that is enabled' : 'The window will not scroll when the drawer is opened',
								checked: props.attributes.scrollToViewModal,
								onChange: (newChecked) => {
									props.setAttributes({scrollToViewModal: newChecked});
								}
							}
						),
						el(ToggleControl,
							{
								key: 'autoOffsetTop',
								label: 'Auto Offset Top',
								help: props.attributes.autoOffsetTop ? 'The drawer modal top is offset to match the position of the bottom of the open element' : 'The drawer modal top position is the default value (whatever is set via CSS)',
								checked: props.attributes.autoOffsetTop,
								onChange: (newChecked) => {
									props.setAttributes({autoOffsetTop: newChecked});

									if (newChecked && props.attributes.autoOffsetBottom) {
										props.setAttributes({autoOffsetBottom: false});
									}
								}
							}
						),
						el(ToggleControl,
							{
								key: 'autoOffsetRight',
								label: 'Auto Offset Right',
								help: props.attributes.autoOffsetRight ? 'The drawer modal right is offset to match the position of the left of the open element' : 'The drawer modal right position is the default value (whatever is set via CSS)',
								checked: props.attributes.autoOffsetRight,
								onChange: (newChecked) => {
									props.setAttributes({autoOffsetRight: newChecked});

									if (newChecked && props.attributes.autoOffsetLeft) {
										props.setAttributes({autoOffsetLeft: false});
									}
								}
							}
						),
						el(ToggleControl,
							{
								key: 'autoOffsetBottom',
								label: 'Auto Offset Bottom',
								help: props.attributes.autoOffsetBottom ? 'The drawer modal bottom is offset to match the position of the top of the open element' : 'The drawer modal bottom position is the default value (whatever is set via CSS)',
								checked: props.attributes.autoOffsetBottom,
								onChange: (newChecked) => {
									props.setAttributes({autoOffsetBottom: newChecked});

									if (newChecked && props.attributes.autoOffsetTop) {
										props.setAttributes({autoOffsetTop: false});
									}
								}
							}
						),
						el(ToggleControl,
							{
								key: 'autoOffsetLeft',
								label: 'Auto Offset Left',
								help: props.attributes.autoOffsetLeft ? 'The drawer modal left is offset to match the position of the right of the open element' : 'The drawer modal left position is the default value (whatever is set via CSS)',
								checked: props.attributes.autoOffsetLeft,
								onChange: (newChecked) => {
									props.setAttributes({autoOffsetLeft: newChecked});

									if (newChecked && props.attributes.autoOffsetRight) {
										props.setAttributes({autoOffsetRight: false});
									}
								}
							}
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