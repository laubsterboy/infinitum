class InfinitumModal {
	constructor(settings) {
		var settings = {...{
			autoOffsetBottom: false,	// Offset the top of the modal based on the position of the open element
			autoOffsetLeft: false,		// Offset the left of the modal based on the position of the open element
			autoOffsetRight: false,		// Offset the right of the modal based on the position of the open element
			autoOffsetTop: false,		// Offset the top of the modal based on the position of the open element
			closeElement: null,			// The close element for the modal
			isOpen: false,				// Whether the modal is open by default or closed
			openElement: null,			// The open element for the modal
			modalElement: null,			// The modal element
			scrollToViewModal: false	// Scroll the window to get the open element to the edge of the window based on the auto offset that is enabled
		}, ...settings};

		this.autoOffsetBottom = settings.autoOffsetBottom;
		this.autoOffsetLeft = settings.autoOffsetLeft;
		this.autoOffsetRight = settings.autoOffsetRight;
		this.autoOffsetTop = settings.autoOffsetTop;
		this.openEvent = new Event('open');
		this.closeEvent = new Event('close');
		this.focusableSelector = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex]:not([tabindex="-1"]), *[contenteditable]';
		this.firstFocusableElement = null;
		this.lastFocusableElement = null;
		this.shiftKeyDown = false;
		this.isOpen = false;
		this.openElement = null;
		this.closeElement = null;
		this.modalElement = null;
		this.nestedCloseElement = true;
		this.scrollToViewModalEnabled = settings.scrollToViewModal;

		// Proxy functions
		this.firstFocusableElementBlurListenerProxy = this.firstFocusableElementBlurListener.bind(this);
		this.lastFocusableElementBlurListenerProxy = this.lastFocusableElementBlurListener.bind(this);
		this.linkClickListenerProxy = this.linkClickListener.bind(this);
		this.keyboardDownListenerProxy = this.keyboardDownListener.bind(this);
		this.keyboardUpListenerProxy = this.keyboardUpListener.bind(this);
		this.nestedCloseElementBlurListenerProxy = this.nestedCloseElementBlurListener.bind(this);

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

		// Initialize close button hierarchy
		if (!this.modalElement.contains(this.closeElement)) {
			this.nestedCloseElement = false;
		}

		// Initialize open/close state
		if (this.isOpen) {
			this.open();
		} else {
			if (this.nestedCloseElement === false) {
				this.disableCloseElement();
			}
		}

		// Add Event Listeners
		if (this.closeElement instanceof HTMLElement) {
			this.closeElement.addEventListener('click', this.closeElementClickListener.bind(this));
		}

		if (this.openElement instanceof HTMLElement) {
			this.openElement.addEventListener('click', this.openElementClickListener.bind(this));
		}

		window.addEventListener('scroll', this.windowScrollListener.bind(this));

		window.addEventListener('resize', this.windowResizeListener.bind(this));
	}



	close(setFocus = true) {
		this.modalElement.classList.remove('is-infinitum-modal-open');

		// Add a class to the body
		document.body.classList.remove('has-infinitum-modal-open');

		// Set aria expanded state
		this.modalElement.setAttribute('aria-expanded', false);

		document.removeEventListener('keydown', this.keyboardDownListenerProxy);
		document.removeEventListener('keyup', this.keyboardUpListenerProxy);

		var modalLinks = this.modalElement.querySelectorAll('a[href]');

		modalLinks.forEach((link) => {
			link.removeEventListener('click', this.linkClickListenerProxy);
		});

		var visibleElements = this.getVisibleElements();

		if (visibleElements.length > 0) {
			if (this.firstFocusableElement instanceof HTMLElement) {
				this.firstFocusableElement.removeEventListener('blur', this.firstFocusableElementBlurListenerProxy);
			}

			if (this.lastFocusableElement instanceof HTMLElement) {
				this.lastFocusableElement.removeEventListener('blur', this.lastFocusableElementBlurListenerProxy);
			}
		}

		if (this.nestedCloseElement === false) {
			this.disableCloseElement();
		}

		if (setFocus === true) {
			this.focusOpenElement();
		}
		
		this.isOpen = false;

		// Dispatch close event on the modal container
		this.modalElement.dispatchEvent(this.closeEvent);
	}



	closeElementClickListener(event) {
		event.preventDefault();

		this.close();
	}



	disableCloseElement() {
		this.openElement.removeAttribute('disabled');
		this.closeElement.setAttribute('disabled', true);
		this.closeElement.removeEventListener('blur', this.nestedCloseElementBlurListenerProxy);
	}



	disableOpenElement() {
		this.openElement.setAttribute('disabled', true);
		this.closeElement.removeAttribute('disabled');
		this.closeElement.addEventListener('blur', this.nestedCloseElementBlurListenerProxy);
	}



	firstFocusableElementBlurListener(event) {
		if (this.shiftKeyDown === true) {
			event.preventDefault();

			if (this.nestedCloseElement) {
				this.focusLast();
			} else {
				this.focusCloseElement();
			}
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

			if (this.nestedCloseElement) {
				this.focusFirst();
			} else {
				this.focusCloseElement();
			}
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



	nestedCloseElementBlurListener(event) {
		event.preventDefault();

		if (this.shiftKeyDown) {
			this.focusLast();
		} else {
			this.focus();
		}
	}



	open(setFocus = true) {
		this.isOpen = true;

		// Set aria expanded state
		this.modalElement.setAttribute('aria-expanded', true);

		// Set focus
		if (setFocus === true) {
			this.focus();
		}

		document.addEventListener('keydown', this.keyboardDownListenerProxy);
		document.addEventListener('keyup', this.keyboardUpListenerProxy);

		// Change the class to open the modal, and also trigger any possible animations or transitions
		this.modalElement.classList.add('is-infinitum-modal-open');
		
		// Add a class to the body
		document.body.classList.add('has-infinitum-modal-open');

		if (this.nestedCloseElement === false) {
			this.disableOpenElement();
		}

		// Dispatch open event on the modal container
		this.modalElement.dispatchEvent(this.openEvent);

		// Update modal offsets
		this.updateModalOffsets();

		// Scroll to modal
		if (this.scrollToViewModalEnabled) {
			this.scrollToViewModal();
		}

		// Wait on animations to complete since elements inside are not considered "visible" under certain circumstances
		Promise.all(this.modalElement.getAnimations({subtree: true}).map((animation) => animation.finished)).then(() => {
			var modalLinks = this.modalElement.querySelectorAll('a[href]');

			modalLinks.forEach((link) => {
				link.addEventListener('click', this.linkClickListenerProxy);
			});

			var visibleElements = this.getVisibleElements();

			if (visibleElements.length > 0) {
				this.firstFocusableElement = visibleElements[0];
				this.lastFocusableElement = visibleElements[visibleElements.length - 1];

				this.firstFocusableElement.addEventListener('blur', this.firstFocusableElementBlurListenerProxy);
				this.lastFocusableElement.addEventListener('blur', this.lastFocusableElementBlurListenerProxy);
			}
		}).catch(error => {
			if (error.name === 'AbortError') {
				// Modal open promise was aborted before the animations finished
				this.close(false);
			}
		});
	}



	openElementClickListener(event) {
		event.preventDefault();

		this.open();
	}



	scrollToViewModal() {
		if (this.autoOffsetBottom) {
			this.openElement.scrollIntoView({behavior: 'smooth', block: 'end'});
		}

		if (this.autoOffsetTop) {
			this.openElement.scrollIntoView({behavior: 'smooth', block: 'start'});
		}
	}



	updateModalOffsets() {
		let windowWidth = document.documentElement.clientWidth;
		let windowHeight = document.documentElement.clientHeight;
		let openElementRect = this.openElement.getBoundingClientRect();

		if (this.autoOffsetBottom) {
			this.modalElement.style.bottom = (windowHeight - openElementRect.top) + 'px';
		}

		if (this.autoOffsetLeft) {
			this.modalElement.style.left = openElementRect.right + 'px';
		}

		if (this.autoOffsetRight) {
			this.modalElement.style.right = (windowWidth - openElementRect.left) + 'px';
		}

		if (this.autoOffsetTop) {
			this.modalElement.style.top = openElementRect.bottom + 'px';
		}
	}



	windowScrollListener(event) {
		if (this.isOpen) {
			this.updateModalOffsets();
		}
	}



	windowResizeListener(event) {
		if (this.isOpen) {
			this.updateModalOffsets();
		}
	}
}