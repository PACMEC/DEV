import Component from './component';

var BaseSettings = require( 'elementor-editor/components/settings/base/manager' );

module.exports = BaseSettings.extend( {
	onInit: function() {
		BaseSettings.prototype.onInit.apply( this );

		$e.components.register( new Component( { manager: this } ) );
	},

	save: function() {},

	changeCallbacks: {
		post_title: function( newValue ) {
			var $title = elementorFrontend.elements.$document.find( elementor.config.page_title_selector );

			$title.text( newValue );
		},

		template: function() {
			elementor.saver.saveAutoSave( {
				onSuccess: function() {
					elementor.reloadPreview();

					elementor.once( 'preview:loaded', function() {
						$e.route( 'panel/page-settings/settings' );
					} );
				},
			} );
		},
	},

	onModelChange: function() {
		elementor.saver.setFlagEditorChange( true );

		BaseSettings.prototype.onModelChange.apply( this, arguments );
	},

	getDataToSave: function( data ) {
		data.id = elementor.config.document.id;

		return data;
	},
} );
