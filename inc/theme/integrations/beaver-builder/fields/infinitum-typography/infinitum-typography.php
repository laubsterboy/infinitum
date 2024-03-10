<#

var defaults = {
	font_size: '0',
	line_height: '0'
}

var name = data.name;
var options = data.field.options;
var value = data.value === '' ? defaults : {...defaults, ...data.value};
var labels = [];
var i;
var responsive = data.name.replace(data.rootName, '');

#>
<div class="fl-compound-field infinitum-typography-field">
	<div class="fl-compound-field-section infinitum-typography-field-section-general fl-compound-field-section-visible">
		<div class="fl-compound-field-section-toggle">
			<i class="dashicons dashicons-arrow-right-alt2"></i>
			<?php _e('Font', 'fl-builder'); ?>
		</div>
		<div class="fl-compound-field-row">
			<# if (Object.hasOwn(options, 'font_size')) { #>
			<div class="fl-compound-field-setting infinitum-typography-field-size" data-property="font-size">
				<label class="fl-compound-field-label">
					<?php _e('Size', 'fl-builder'); ?>
				</label>
				<select name="{{data.name}}[][font_size]">
					<# for (var optionKey in options.font_size) {
						var optionVal = options.font_size[optionKey];
						var label = typeof optionVal === 'object' ? optionVal.label : optionVal;
						var selected = '';

						if (value.font_size !== undefined && optionKey == value.font_size) {
							selected = ' selected="selected"';
						}
						#>
						<option value="{{optionKey}}"{{{selected}}}>{{{label}}}</option>
					<# } #>
				</select>
			</div>
			<# } #>
			<# if (Object.hasOwn(options, 'line_height')) { #>
			<div class="fl-compound-field-setting infinitum-typography-field-line-height" data-property="line-height">
				<label class="fl-compound-field-label">
					<?php _e('Line Height', 'fl-builder'); ?>
				</label>
				<select name="{{data.name}}[][line_height]">
					<# for (var optionKey in options.line_height) {
						var optionVal = options.line_height[optionKey];
						var label = typeof optionVal === 'object' ? optionVal.label : optionVal;
						var selected = '';

						if (value.line_height !== undefined && optionKey == value.line_height) {
							selected = ' selected="selected"';
						}
						#>
						<option value="{{optionKey}}"{{{selected}}}>{{{label}}}</option>
					<# } #>
				</select>
			</div>
			<# } #>
		</div>
	</div>
</div>