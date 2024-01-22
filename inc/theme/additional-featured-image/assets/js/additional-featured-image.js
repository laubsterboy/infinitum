class JRDAdditionalFeaturedImage {
	constructor() {
		this.namespace = 'jrd';

		if (typeof AFI === 'object') {
			this.namespace = AFI.namespace;
		}

		this._modal = null;

		if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', this.DOMContentLoadedListener.bind(this));
        } else {
            this.DOMContentLoadedListener();
        }
	}



	DOMContentLoadedListener(event) {
		var that = this;
		
		jQuery('.' + this.namespace + '-additional-featured-image-meta-box').each(function() {
			that._addEventListeners(this);
		});
	}



	_addEventListeners(wrap) {
		jQuery('.' + this.namespace + '-additional-featured-image__set', wrap).on('click', this._select.bind(this));
		jQuery('.' + this.namespace + '-additional-featured-image__preview', wrap).on('click', this._select.bind(this));
		jQuery('.' + this.namespace + '-additional-featured-image__replace', wrap).on('click', this._select.bind(this));
		jQuery('.' + this.namespace + '-additional-featured-image__remove', wrap).on('click', this._remove.bind(this));
	}



	_getDataAttributes(wrap) {
		return {
			_ajax_nonce: jQuery(wrap).data('nonce'),
			attachment_id: 0,
			metabox_id: jQuery(wrap).data('metabox-id'),
			post_id: jQuery(wrap).data('post-id')
		};
	}



	_remove(event) {
		var wrap			= jQuery(event.target).closest('.' + this.namespace + '-additional-featured-image-meta-box'),
			dataAttributes	= this._getDataAttributes(wrap);

		event.preventDefault();

		this._updateMetaBox(wrap, dataAttributes);
    }



	_select(event) {
		if (this._modal === null) {
	
			this._modal = wp.media({
				title: 'Select Image',
				button: { text: 'Select Image' },
				library: { type : 'image' },
				multiple: false
			});
		}

		this._modal.once('select', jQuery.proxy(this._selected, this, event.target));
		this._modal.open();
	}



	_selected(button) {
		var attachment		= this._modal.state().get('selection').first().toJSON(),
			wrap			= jQuery(button).closest('.' + this.namespace + '-additional-featured-image-meta-box'),
			dataAttributes	= this._getDataAttributes(wrap);

		dataAttributes.attachment_id = attachment.id;

		this._updateMetaBox(wrap, dataAttributes);
	}



	_updateMetaBox(wrap, dataAttributes) {
		var that = this;

		dataAttributes.action = this.namespace + '_additional_featured_image_update';

		jQuery.ajax({
			method: 'POST',
			url: ajaxurl,
			data: dataAttributes,
			success: function(data) {
				data = JSON.parse(data);

				if (data !== 0 && data.html !== '') {
					jQuery(wrap).html(data.html);
					jQuery('.' + this.namespace + '-additional-featured-image__input', wrap).val(dataAttributes.attachment_id);
					that._addEventListeners(wrap);
				}
			}
		});
	}
}
new JRDAdditionalFeaturedImage();