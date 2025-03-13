class InfinitumDrawers {
	constructor() {
		this.modalObjects = [];

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
		let drawers = document.querySelectorAll('.wp-block-infinitum-drawer');

		drawers.forEach((drawer) => {
			let autoOffsetBottom = false;
			let autoOffsetLeft = false;
			let autoOffsetRight = false;
			let autoOffsetTop = false;
			let buttonOpen = drawer.querySelector('.wp-block-infinitum-drawer__button--open');
			let buttonClose = drawer.querySelector('.wp-block-infinitum-drawer__button--close');
			let modal = drawer.querySelector('.wp-block-infinitum-drawer__modal');
			let scrollToViewModal = false;

			if ('autoOffsetBottom' in drawer.dataset && drawer.dataset.autoOffsetBottom === 'true') autoOffsetBottom = true;
			if ('autoOffsetLeft' in drawer.dataset && drawer.dataset.autoOffsetLeft === 'true') autoOffsetLeft = true;
			if ('autoOffsetRight' in drawer.dataset && drawer.dataset.autoOffsetRight === 'true') autoOffsetRight = true;
			if ('autoOffsetTop' in drawer.dataset && drawer.dataset.autoOffsetTop === 'true') autoOffsetTop = true;
			if ('scrollToViewModal' in drawer.dataset && drawer.dataset.scrollToViewModal === 'true') scrollToViewModal = true;

			let modalObject = new InfinitumModal({
				autoOffsetBottom: autoOffsetBottom,
				autoOffsetLeft: autoOffsetLeft,
				autoOffsetRight: autoOffsetRight,
				autoOffsetTop: autoOffsetTop,
				closeElement: buttonClose,
				openElement: buttonOpen,
				modalElement: modal,
				scrollToViewModal: scrollToViewModal
			});

			modal.addEventListener('open', this.modalOpenListener.bind(this));

			this.modalObjects.push(modalObject);
		});
	}



	modalOpenListener(event) {
		this.modalObjects.forEach((modalObject) => {
			if (modalObject.isOpen && event.target !== modalObject.modalElement) {
				modalObject.close(false);
			}
		});
	}
}
new InfinitumDrawers();