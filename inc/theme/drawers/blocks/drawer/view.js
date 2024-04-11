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
		var drawers = document.querySelectorAll('.wp-block-infinitum-drawer');

		drawers.forEach((drawer) => {
			let buttonOpen = drawer.querySelector('.wp-block-infinitum-drawer__button--open');
			let buttonClose = drawer.querySelector('.wp-block-infinitum-drawer__button--close');
			let modal = drawer.querySelector('.wp-block-infinitum-drawer__modal');

			let modalObject = new InfinitumModal({
				closeElement: buttonClose,
				openElement: buttonOpen,
				modalElement: modal
			});
		});
	}
}
new InfinitumDrawers();