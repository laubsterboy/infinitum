<#
var defaults = {
	top: 'inherit',
	right: 'inherit',
	bottom: 'inherit',
	left: 'inherit'
};

if (Object.hasOwn(data.field, 'default') && typeof data.field.default === 'object') {
	defaults = {...defaults, ...data.field.default};
}

var name = data.name;
var options = data.field.options;
var value = data.value === '' ? defaults : {...defaults, ...data.value};
var keys = data.field.keys;
var labels = [];
var i;

if (typeof keys !== 'object') {
	keys = {
		top: '',
		right: '',
		bottom: '',
		left: ''
	};
}

for (i in keys) {
	labels.push(keys[i]);
}

keys = Object.keys(keys);

var labelClass = '';
#>
<div class="infinitum-spacing-field-units">
	<# for (i = 0; i < keys.length; i++) { #>
	<div class="infinitum-spacing-field-unit">
		<select name="{{name}}[][{{keys[i]}}]">
			<# for (var optionKey in options) {
				var optionVal = options[optionKey];
				var label = typeof optionVal === 'object' ? optionVal.label : optionVal;
				var selected = '';

				if (value[keys[i]] !== undefined && optionKey == value[keys[i]]) {
					selected = ' selected="selected"';
				}
				#>
				<option value="{{optionKey}}"{{{selected}}}>{{{label}}}</option>
			<# } #>
		</select>
		<#

		labelClass = keys[i];

		if (labels[i] === '') {
			labelClass += ' icon';
		}
		#>
		<label class="{{{labelClass}}}">{{{labels[i]}}}</label>
	</div>
	<# } #>
</div>