/*
(function($) {
	$(function() {
		var preview = null;

		function _getDefaultValue(selector, property) {
			var ignoredProperties = [
				'font-size',
				'line-height',
				'margin-top',
				'margin-right',
				'margin-bottom',
				'margin-left',
				'padding-top',
				'padding-right',
				'padding-bottom',
				'padding-left',
			]
			var value = preview._originalGetDefaultValue(selector, property);

			if (ignoredProperties.includes(property)) {
				value = '';
			}

			return value;
		}



		FLBuilder.addHook( 'preview-init', function(event, _preview) {
			preview = _preview;

			// Save the original so if this function changes in the BB plugin the current version will always be used
			preview._originalGetDefaultValue = preview._getDefaultValue;

			// Set the new that will reference the original
			preview._getDefaultValue = _getDefaultValue;
		});



		function setMarginPadding() {
			var form = $('.fl-builder-settings:visible', window.parent.document);
			var preview = FLBuilder.preview;

			if (preview == null) return;

			var settings = FLBuilder._getSettings(form);
			var selector = preview._getPreviewSelector(preview.classes.node, preview.selector);

			if (preview.type === 'module') {
				selector = selector + ' .fl-module-content';

				preview.updateCSSRule(selector, 'margin', '25px');
			}
		}



		FLBuilder.addHook( 'didRenderLayoutJSComplete', function(event) {
			var form = $('.fl-builder-settings:visible', window.parent.document);
			var preview = FLBuilder.preview;

			if (preview == null) return;

			var settings = FLBuilder._getSettings(form);
			var selector = preview._getPreviewSelector(preview.classes.node, preview.selector);

			if (preview.type === 'module') {
				selector = selector + ' .fl-module-content';

				preview.updateCSSRule(selector, 'margin', '25px', true);
			}

			console.log(preview);
			console.log(selector);
			console.log(settings);
		});



		FLBuilder.addHook('responsive-editing-switched', function(event, mode) {
			console.log('responsive-editing-switched');
			console.log(mode);
		});



		FLBuilder.addHook('responsive-editing-after-preview-fields', function(event, mode) {
			console.log('responsive-editing-after-preview-fields');
			console.log(mode);
		});
	});
})(jQuery);
*/