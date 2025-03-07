import { store, getContext } from "@wordpress/interactivity";

const { state } = store('infinitum/drawer', {
	actions: {
		open: () => {
			const context = getContext();
			context.isOpen = true;
		},
		close: () => {
			const context = getContext();
			context.isOpen = false;
		}
	}
});