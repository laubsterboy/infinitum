class InfinitumBlockBreadcrumbs {
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
		var allBreadcrumbs = document.querySelectorAll('.infinitum-block-breadcrumbs');

		allBreadcrumbs.forEach((breadcrumbs) => {
			/*
			let buttonOpen = breadcrumbs.querySelector('.infinitum-block-drawer__button--open');
			let buttonClose = breadcrumbs.querySelector('.infinitum-block-drawer__button--close');
			let modal = breadcrumbs.querySelector('.infinitum-block-drawer__modal');

			new InfinitumBreadcrumbs({
				closeElement: buttonClose,
				openElement: buttonOpen,
				modalElement: modal
			});
			*/
		});
	}
}
new InfinitumBlockBreadcrumbs();