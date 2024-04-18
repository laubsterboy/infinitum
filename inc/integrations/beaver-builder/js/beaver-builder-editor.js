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
	});
})(jQuery);