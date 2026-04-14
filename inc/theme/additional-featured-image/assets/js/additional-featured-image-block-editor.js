( function( wp ) {
	if ( ! wp || ! wp.plugins || ! wp.element || ! wp.data ) {
		return;
	}

	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost ? wp.editPost.PluginDocumentSettingPanel : null;
	var PluginPostFeaturedImage = wp.editPost ? wp.editPost.PluginPostFeaturedImage : null;
	var MediaUpload = wp.blockEditor ? wp.blockEditor.MediaUpload : null;
	var MediaUploadCheck = wp.blockEditor ? wp.blockEditor.MediaUploadCheck : null;
	var Button = wp.components ? wp.components.Button : null;
	var ResponsiveWrapper = wp.components ? wp.components.ResponsiveWrapper : null;
	var Spinner = wp.components ? wp.components.Spinner : null;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData ? wp.coreData.useEntityProp : null;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n ? wp.i18n.__ : function( text ) { return text; };

	var config = window.AFI_BLOCK_EDITOR || ( typeof AFI_BLOCK_EDITOR !== 'undefined' ? AFI_BLOCK_EDITOR : {} );
	var additionalImages = Array.isArray( config.additionalImages ) ? config.additionalImages : [];

	if ( ( ! PluginDocumentSettingPanel && ! PluginPostFeaturedImage ) || ! MediaUpload || ! MediaUploadCheck || ! Button || ! useEntityProp ) {
		return;
	}

	function getImageUrl( media ) {
		if ( ! media ) {
			return '';
		}

		if ( media.source_url ) {
			return media.source_url;
		}

		if ( media.media_details && media.media_details.sizes && media.media_details.sizes.full ) {
			return media.media_details.sizes.full.source_url || '';
		}

		return '';
	}

	function AdditionalFeaturedImageItem( props ) {
		var image = props.image;
		var meta = props.meta || {};
		var setMeta = props.setMeta;
		var namespace = props.namespace || 'jrd';
		var metaKey = image.metaKey;
		var attachmentId = meta && meta[ metaKey ] ? parseInt( meta[ metaKey ], 10 ) : 0;

		var media = useSelect( function( select ) {
			if ( ! attachmentId ) {
				return null;
			}

			return select( 'core' ).getMedia( attachmentId );
		}, [ attachmentId ] );

		var isResolving = useSelect( function( select ) {
			if ( ! attachmentId ) {
				return false;
			}

			return select( 'core/data' ).isResolving( 'core', 'getMedia', [ attachmentId ] );
		}, [ attachmentId ] );

		var imageUrl = getImageUrl( media );
		var naturalWidth = media && media.media_details ? media.media_details.width : null;
		var naturalHeight = media && media.media_details ? media.media_details.height : null;

		function updateMeta( newId ) {
			var updated = {};
			updated[ metaKey ] = newId;
			setMeta( Object.assign( {}, meta, updated ) );
		}

		return el(
			Fragment,
			null,
			el( 'p', { className: 'components-base-control__label' }, image.title ),
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: attachmentId,
					onSelect: function( selected ) {
						if ( selected && selected.id ) {
							updateMeta( selected.id );
						}
					},
					render: function( renderProps ) {
						if ( attachmentId && imageUrl ) {
							return el(
								'div',
								{ className: namespace + '-additional-featured-image-meta-box' },
								el(
									Button,
									{
										className: namespace + '-additional-featured-image__preview',
										onClick: renderProps.open
									},
									ResponsiveWrapper && naturalWidth && naturalHeight
										? el( ResponsiveWrapper, { naturalWidth: naturalWidth, naturalHeight: naturalHeight }, el( 'img', { src: imageUrl, alt: '' } ) )
										: el( 'img', { src: imageUrl, alt: '' } )
								),
								el(
									'div',
									{ className: 'components-flex ' + namespace + '-additional-featured-image__actions' },
									el(
										Button,
										{
											className: 'editor-post-featured-image__action ' + namespace + '-additional-featured-image__replace',
											onClick: renderProps.open
										},
										__( 'Replace', 'infinitum' )
									),
									el(
										Button,
										{
											className: 'editor-post-featured-image__action ' + namespace + '-additional-featured-image__remove',
											onClick: function() {
												updateMeta( 0 );
											}
										},
										__( 'Remove', 'infinitum' )
									)
								)
							);
						}

						return el(
							'div',
							{ className: namespace + '-additional-featured-image-meta-box' },
							el(
								Button,
								{
									className: namespace + '-additional-featured-image__set',
									onClick: renderProps.open
								},
								__( 'Set image', 'infinitum' )
							),
							isResolving ? el( Spinner, null ) : null
						);
					}
				} )
			)
		);
	}

	function AdditionalFeaturedImagePanel() {
		var postType = useSelect( function( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var entity = useEntityProp ? useEntityProp( 'postType', postType, 'meta' ) : null;
		var meta = entity ? entity[ 0 ] : {};
		var setMeta = entity ? entity[ 1 ] : function() {};

		var imagesForPostType = additionalImages.filter( function( image ) {
			if ( ! image.screen || ! image.screen.length ) {
				return true;
			}

			return image.screen.indexOf( postType ) !== -1;
		} );

		if ( ! imagesForPostType.length ) {
			return null;
		}

		var items = imagesForPostType.map( function( image ) {
			return el( AdditionalFeaturedImageItem, {
				key: image.id,
				image: image,
				meta: meta,
				setMeta: setMeta,
				namespace: config.namespace || 'jrd'
			} );
		} );

		if ( PluginPostFeaturedImage ) {
			return el( PluginPostFeaturedImage, null, items );
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'additional-featured-images',
				title: __( 'Additional Featured Images', 'infinitum' ),
				className: 'additional-featured-images-panel'
			},
			items
		);
	}

	registerPlugin( 'additional-featured-images', {
		render: AdditionalFeaturedImagePanel
	} );
} )( window.wp );
