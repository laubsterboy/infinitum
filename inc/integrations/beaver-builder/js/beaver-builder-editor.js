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



		FLBuilder.addHook('preview-init', function(event, _preview) {
			preview = _preview;

			// _getDefaultValue
			preview._originalGetDefaultValue = preview._getDefaultValue;
			preview._getDefaultValue = _getDefaultValue;
		});

		// Disable margin placeholders
		FLBuilder._initModuleMarginPlaceholders = () => {};

		// Disable margin/padding placeholders when switching responsive modes
		//FLBuilderResponsiveEditing._setMarginPaddingPlaceholders = () => {};
	});
})(jQuery);