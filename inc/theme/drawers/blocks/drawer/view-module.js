import { store, getContext } from "@wordpress/interactivity";

const { state } = store('infinitumDrawer', {
	state: {
		_isOpen: false,
		get isOpen() {
			return this._isOpen;
		},
		set isOpen(value) {
			if (typeof value === 'boolean') {
				this._isOpen = value;
			}
		}
	},
	actions: {
		open: () => {
			console.log('yay it opened');
			state.isOpen = true;
			console.log(state.isOpen);
			console.log(getContext());
		},
		close: () => {
			console.log('it closed');
			state.isOpen = false;
			console.log(state.isOpen);
			console.log(getContext('infinitumDrawer'));
		}
	}
});
console.log(state);
			