export default class HandlesPosition extends elementorModules.frontend.handlers.Base {
	isFirstSection() {
		return this.$element.is( '.elementor-edit-mode .elementor-top-section:first' );
	}

	isOverflowHidden() {
		return 'hidden' === this.$element.css( 'overflow' );
	}

	getOffset() {
		if ( 'body' === elementor.config.document.container ) {
			return this.$element.offset().top;
		}

		const $container = jQuery( elementor.config.document.container );
		return this.$element.offset().top - $container.offset().top;
	}

	setHandlesPosition() {
		const isOverflowHidden = this.isOverflowHidden();

		if ( ! isOverflowHidden && ! this.isFirstSection() ) {
			return;
		}

		const offset = isOverflowHidden ? 0 : this.getOffset(),
			$handlesElement = this.$element.find( '> .elementor-element-overlay > .elementor-editor-section-settings' ),
			insideHandleClass = 'elementor-section--handles-inside';

		if ( offset < 25 ) {
			this.$element.addClass( insideHandleClass );

			if ( offset < -5 ) {
				$handlesElement.css( 'top', -offset );
			} else {
				$handlesElement.css( 'top', '' );
			}
		} else {
			this.$element.removeClass( insideHandleClass );
		}
	}

	onInit() {
		this.setHandlesPosition();

		this.$element.on( 'mouseenter', this.setHandlesPosition.bind( this ) );
	}
}
