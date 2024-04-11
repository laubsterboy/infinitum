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
			
		});
	}
}
new InfinitumBlockBreadcrumbs();