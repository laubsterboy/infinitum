class InfinitumModal {
	constructor(settings) {
		var settings = Object.assign({
			closeElement: null,
			isOpen: false,
			openElement: null,
			modalElement: null
		}, settings);

		this.focusableSelector = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex]:not([tabindex="-1"]), *[contenteditable]';
		this.firstFocusableElement = null;
		this.lastFocusableElement = null;
		this.shiftKeyDown = false;
		this.isOpen = false;
		this.openElement = null;
		this.closeElement = null;
		this.modalElement = null;

		if (settings.isOpen) {
			this.isOpen = true;
		}
		
		if (settings.closeElement instanceof HTMLElement) {
			this.closeElement = settings.closeElement;
		} else if (typeof settings.closeElement === 'string') {
			this.closeElement = document.querySelector(settings.closeElement);
		}
		
		if (settings.openElement instanceof HTMLElement) {
			this.openElement = settings.openElement;
		} else if (typeof settings.openElement === 'string') {
			this.openElement = document.querySelector(settings.openElement);
		}

		if (settings.modalElement instanceof HTMLElement) {
			this.modalElement = settings.modalElement;
		} else if (typeof settings.modalElement === 'string') {
			this.modalElement = document.querySelector(settings.modalElement);
		} else {
			console.log('InfinitumModal requires settings.modalElement to be an HTMLElement or a selector string. This may mean that the Drawer has been been removed.');
			return;
		}

		// Move the modal to be a direct child of the body (ideally want to avoid this, but need to ensure CSS inheritance isn't causing problems)
		//document.body.appendChild(this.modalElement);

		// Make the modal focusable
		this.modalElement.setAttribute('tabindex', '-1');

		this.closeElement.classList.add('infinitum-modal__button', 'infinitum-modal__button--close');
		this.openElement.classList.add('infinitum-modal__button', 'infinitum-modal__button--open');
		this.modalElement.classList.add('infinitum-modal');

		// Initialize open/close state
		if (this.isOpen) {
			this.open();
		}

		// Add Event Listeners
		if (this.closeElement !== null) {
			this.closeElement.addEventListener('click', this.closeElementClickListener.bind(this));
		}

		if (this.openElement !== null) {
			this.openElement.addEventListener('click', this.openElementClickListener.bind(this));
		}
	}



	close() {
		this.modalElement.classList.remove('is-infinitum-modal-open');

		// Add a class to the body
		document.body.classList.remove('has-infinitum-modal-open');

		// Set aria expanded state
		this.modalElement.setAttribute('aria-expanded', false);

		document.removeEventListener('keydown', this.keyboardDownListener.bind(this));
		document.removeEventListener('keyup', this.keyboardUpListener.bind(this));

		var modalLinks = this.modalElement.querySelectorAll('a[href]');

		modalLinks.forEach((link) => {
			link.removeEventListener('click', this.linkClickListener.bind(this));
		});

		var visibleElements = this.getVisibleElements();

		if (visibleElements.length > 0) {
			this.firstFocusableElement.removeEventListener('blur', this.firstFocusableElementBlurListener.bind(this));
			this.lastFocusableElement.removeEventListener('blur', this.lastFocusableElementBlurListener.bind(this));
		}

		this.focusOpenElement();
		
		this.isOpen = false;
	}



	closeElementClickListener(event) {
		event.preventDefault();

		this.close();
	}



	firstFocusableElementBlurListener(event) {
		if (this.shiftKeyDown === true) {
			event.preventDefault();

			this.focusLast();
		}
	}



	focus() {
		this.modalElement.focus();
	}



	focusCloseElement() {
		this.closeElement.focus();
	}



	focusFirst() {
		this.firstFocusableElement.focus();
	}



	focusLast() {
		this.lastFocusableElement.focus();
	}
	
	
	
	focusOpenElement() {
		this.openElement.focus();
	}



	getVisibleElements() {
		return Array.prototype.slice.call(this.modalElement.querySelectorAll(this.focusableSelector)).filter((item, index) => {
			var computedStyle = window.getComputedStyle(item);

			return computedStyle.getPropertyValue('display') !== 'none' && computedStyle.getPropertyValue('visibility') !== 'hidden' && parseFloat(computedStyle.getPropertyValue('opacity')) > 0;
		});
	}



	keyboardDownListener(event) {
		if (event.keyCode == 16 || event.which == 16) {
			this.shiftKeyDown = true;
		}

		if (event.keyCode == 9 || event.which == 9) {
			if (this.firstFocusableElement == document.activeElement) {
				this.firstFocusableElementBlurListener(event);
			} else if (this.lastFocusableElement == document.activeElement) {
				this.lastFocusableElementBlurListener(event);
			}
		}
	}



	keyboardUpListener(event) {
		var escapeKeyDown = false;

		if (event.keyCode == 16 || event.which == 16) {
			this.shiftKeyDown = false;
		}

		if ('key' in event) {
			escapeKeyDown = (event.key === 'Escape' || event.key === 'Esc');
		} else {
			escapeKeyDown = (event.keyCode == 27 || event.which == 27);
		}

		if (escapeKeyDown) this.close();
	}



	lastFocusableElementBlurListener(event) {
		if (this.shiftKeyDown !== true) {
			event.preventDefault();
			this.focusFirst();
		}
	}



	linkClickListener(event) {
		var href = event.target.href;

		if (href.indexOf('#') !== -1) {
			var hrefParts = href.split('#');
			var windowHref = window.location.href;

			if (window.indexOf('#') !== -1) {
				windowHref = windowHref.split('#')[0];
			}

			if (hrefParts[0] == windowHref) {
				this.close();
			}
		}
	}



	open() {
		// Change the class to open the modal, and also trigger any possible animations or transitions
		this.modalElement.classList.add('is-infinitum-modal-open');
		
		// Add a class to the body
		document.body.classList.add('has-infinitum-modal-open');

		Promise.all(this.modalElement.getAnimations({subtree: true}).map((animation) => animation.finished)).then(() => {
			// Set aria expanded state
			this.modalElement.setAttribute('aria-expanded', true);

			document.addEventListener('keydown', this.keyboardDownListener.bind(this));
			document.addEventListener('keyup', this.keyboardUpListener.bind(this));

			var modalLinks = this.modalElement.querySelectorAll('a[href]');

			modalLinks.forEach((link) => {
				link.addEventListener('click', this.linkClickListener.bind(this));
			});

			var visibleElements = this.getVisibleElements();

			if (visibleElements.length > 0) {
				this.firstFocusableElement = visibleElements[0];
				this.lastFocusableElement = visibleElements[visibleElements.length - 1];

				this.firstFocusableElement.addEventListener('blur', this.firstFocusableElementBlurListener.bind(this));
				this.lastFocusableElement.addEventListener('blur', this.lastFocusableElementBlurListener.bind(this));
			}

			this.focus();

			this.isOpen = true;
		}).catch(error => {
			if (error.name === 'AbortError') {
				// Modal open promise was aborted before the animations finished
			}
		});
	}



	openElementClickListener(event) {
		event.preventDefault();

		this.open();
	}
}