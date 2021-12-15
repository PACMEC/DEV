export default class extends Marionette.ItemView {
	getTemplate() {
		return '#tmpl-elementor-panel-revisions-loading';
	}

	id() {
		return 'elementor-panel-revisions-loading';
	}

	onRender() {
		elementor.history.revisions.requestRevisions( () => {
			setTimeout( () => $e.routes.refreshContainer( 'panel' ) );
		} );
	}
}
