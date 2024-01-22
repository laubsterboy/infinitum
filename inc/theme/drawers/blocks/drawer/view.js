class InfinitumDrawers {
	constructor() {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', this.DOMContentLoadedListener.bind(this));
		} else {
			this.DOMContentLoadedListener();
		}
	}



	DOMContentLoadedListener(event) {
		this.init();
	}



	init() {
		var drawers = document.querySelectorAll('.infinitum-block-drawer');

		drawers.forEach((drawer) => {
			let buttonOpen = drawer.querySelector('.infinitum-block-drawer__button--open');
			let buttonClose = drawer.querySelector('.infinitum-block-drawer__button--close');
			let modal = drawer.querySelector('.infinitum-block-drawer__modal');

			new InfinitumModal({
				closeElement: buttonClose,
				openElement: buttonOpen,
				modalElement: modal
			});
		});
	}
}
new InfinitumDrawers();