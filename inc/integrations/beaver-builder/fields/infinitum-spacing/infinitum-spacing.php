<#

var names = data.names;
var defaultValue = 'inherit';
var options = data.field.options;
var values = data.values;
var keys = data.field.keys;
var labels = [];
var i;
var responsive = data.name.replace(data.rootName, '');

if (Object.hasOwn(data.field, 'default') && typeof data.field.default === 'string') {
	defaultValue = data.field.default;
}

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

if (typeof names !== 'object') {
	names = {};
	for (i in keys) {
		names[keys[i]] = data.rootName + '_' + keys[i] + responsive;
	}
}

if (typeof values !== 'object') {
	values = {};
	for (i in keys) {
		values[keys[i]] = data.settings[data.rootName + '_' + keys[i] + responsive];
	}
}

var labelClass = '';
#>
<div class="infinitum-spacing-field-units">
	<# for (i = 0; i < keys.length; i++) { #>
	<div class="infinitum-spacing-field-unit">
		<select name="{{names[keys[i]]}}">
			<# for (var optionKey in options) {
				var optionVal = options[optionKey];
				var label = typeof optionVal === 'object' ? optionVal.label : optionVal;
				var selected = '';

				if ((values[keys[i]] !== undefined && optionKey == values[keys[i]]) || (values[keys[i]] === undefined && optionKey == defaultValue)) {
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