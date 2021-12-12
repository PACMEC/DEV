export default class extends elementorModules.Module {
	constructor( ...args ) {
		super( ...args );

		this.components = {};
		this.activeComponents = {};
	}

	getAll() {
		return Object.keys( this.components ).sort();
	}

	register( component ) {
		this.components[ component.getNamespace() ] = component;

		return component;
	}

	get( id ) {
		return this.components[ id ];
	}

	getActive() {
		return this.activeComponents;
	}

	activate( namespace ) {
		// Add as last.
		this.inactivate( namespace );
		this.activeComponents[ namespace ] = true;
	}

	inactivate( namespace ) {
		delete this.activeComponents[ namespace ];
	}

	isActive( namespace ) {
		return !! this.activeComponents[ namespace ];
	}
}
