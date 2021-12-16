var ElementHistoryBehavior = require( './element-behavior' ),
	CollectionHistoryBehavior = require( './collection-behavior' );

import ItemModel from './item-model';
import Component from './component';

var	Manager = function() {
	var self = this,
		currentItemID = null,
		items = new Backbone.Collection( [], { model: ItemModel } ),
		editorSaved = false,
		active = true;

	var translations = {
		add: elementor.translate( 'added' ),
		remove: elementor.translate( 'removed' ),
		change: elementor.translate( 'edited' ),
		move: elementor.translate( 'moved' ),
		paste_style: elementor.translate( 'style_pasted' ),
		reset_style: elementor.translate( 'style_reset' ),
	};

	var addBehaviors = function( behaviors ) {
		behaviors.ElementHistory = {
			behaviorClass: ElementHistoryBehavior,
		};

		behaviors.CollectionHistory = {
			behaviorClass: CollectionHistoryBehavior,
		};

		return behaviors;
	};

	var addCollectionBehavior = function( behaviors ) {
		behaviors.CollectionHistory = {
			behaviorClass: CollectionHistoryBehavior,
		};

		return behaviors;
	};

	var getActionLabel = function( itemData ) {
		if ( translations[ itemData.type ] ) {
			return translations[ itemData.type ];
		}

		return itemData.type;
	};

	this.navigate = function( isRedo ) {
		var currentItem = items.find( function( model ) {
				return 'not_applied' === model.get( 'status' );
			} ),
			currentItemIndex = items.indexOf( currentItem ),
			requiredIndex = isRedo ? currentItemIndex - 1 : currentItemIndex + 1;

		if ( ( ! isRedo && ! currentItem ) || requiredIndex < 0 || requiredIndex >= items.length ) {
			return;
		}

		self.doItem( requiredIndex );
	};

	var updatePanelPageCurrentItem = function() {
		if ( $e.routes.is( 'panel/history/actions' ) ) {
			elementor.getPanelView().getCurrentPageView().getCurrentTab().updateCurrentItem();
		}
	};

	var onPanelSave = function() {
		if ( items.length >= 2 ) {
			// Check if it's a save after made changes, `items.length - 1` is the `Editing Started Item
			var firstEditItem = items.at( items.length - 2 );
			editorSaved = ( 'not_applied' === firstEditItem.get( 'status' ) );
		}
	};

	var init = function() {
		$e.components.register( new Component( { manager: self } ) );

		elementor.hooks.addFilter( 'elements/base/behaviors', addBehaviors );
		elementor.hooks.addFilter( 'elements/base-section-container/behaviors', addCollectionBehavior );

		elementor.channels.data
			.on( 'drag:before:update', self.startMovingItem )
			.on( 'drag:after:update', self.endItem )

			.on( 'element:before:add', self.startAddElement )
			.on( 'element:after:add', self.endItem )

			.on( 'element:before:remove', self.startRemoveElement )
			.on( 'element:after:remove', self.endItem )

			.on( 'element:before:paste:style', self.startPasteStyle )
			.on( 'element:after:paste:style', self.endItem )

			.on( 'element:before:reset:style', self.startResetStyle )
			.on( 'element:after:reset:style', self.endItem )

			.on( 'section:before:drop', self.startDropElement )
			.on( 'section:after:drop', self.endItem )

			.on( 'template:before:insert', self.startInsertTemplate )
			.on( 'template:after:insert', self.endItem );

		elementor.channels.editor.on( 'saved', onPanelSave );
	};

	this.setActive = function( value ) {
		active = value;
	};

	this.getActive = function() {
		return active;
	};

	this.getItems = function() {
		return items;
	};

	this.startItem = function( itemData ) {
		currentItemID = this.addItem( itemData );
	};

	this.endItem = function() {
		currentItemID = null;
	};

	this.isItemStarted = function() {
		return null !== currentItemID;
	};

	this.addItem = function( itemData ) {
		if ( ! this.getActive() ) {
			return;
		}

		if ( ! items.length ) {
			items.add( {
				status: 'not_applied',
				title: elementor.translate( 'editing_started' ),
				subTitle: '',
				action: '',
				editing_started: true,
			} );
		}

		// Remove old applied items from top of list
		while ( items.length && 'applied' === items.first().get( 'status' ) ) {
			items.shift();
		}

		var id = currentItemID ? currentItemID : new Date().getTime();

		var	currentItem = items.findWhere( {
			id: id,
		} );

		if ( ! currentItem ) {
			currentItem = new ItemModel( {
				id: id,
				title: itemData.title,
				subTitle: itemData.subTitle,
				action: getActionLabel( itemData ),
				type: itemData.type,
				elementType: itemData.elementType,
			} );

			self.startItemTitle = '';
			self.startItemAction = '';
		}

		var position = 0;

		// Temp fix. On move a column - insert the `remove` subItem before the section changes subItem.
		// In a multi columns section - the structure has been changed,
		// In a one column section - it's filled with an empty column,
		// The order is important for the `redoItem`, that needed to change the section first
		// and only after that - to remove the column.
		if ( 'column' === itemData.elementType && 'remove' === itemData.type && 'column' === currentItem.get( 'elementType' ) ) {
			position = 1;
		}

		currentItem.get( 'items' ).add( itemData, { at: position } );

		items.add( currentItem, { at: 0 } );

		updatePanelPageCurrentItem();

		return id;
	};

	this.doItem = function( index ) {
		// Don't track while restoring the item
		this.setActive( false );

		var item = items.at( index );

		if ( 'not_applied' === item.get( 'status' ) ) {
			this.undoItem( index );
		} else {
			this.redoItem( index );
		}

		this.setActive( true );

		var panel = elementor.getPanelView(),
			panelPage = panel.getCurrentPageView(),
			viewToScroll;

		if ( $e.routes.isPartOf( 'panel/editor' ) ) {
			if ( panelPage.getOption( 'editedElementView' ).isDestroyed ) {
				// If the the element isn't exist - show the history panel
				$e.route( 'panel/history/actions' );
			} else {
				// If element exist - render again, maybe the settings has been changed
				viewToScroll = panelPage.getOption( 'editedElementView' );
			}
		} else if ( item instanceof Backbone.Model && item.get( 'items' ).length ) {
			const history = item.get( 'items' ).first().get( 'history' );

				if ( history && history.behavior.view.model ) {
					viewToScroll = self.findView( history.behavior.view.model.get( 'id' ) );
				}
			}

		updatePanelPageCurrentItem();

		if ( viewToScroll && ! elementor.helpers.isInViewport( viewToScroll.$el[ 0 ], elementor.$previewContents.find( 'html' )[ 0 ] ) ) {
			elementor.helpers.scrollToView( viewToScroll.$el );
		}

		if ( item.get( 'editing_started' ) ) {
			if ( ! editorSaved ) {
				elementor.saver.setFlagEditorChange( false );
			}
		}
	};

	this.undoItem = function( index ) {
		var item;

		for ( var stepNum = 0; stepNum < index; stepNum++ ) {
			item = items.at( stepNum );

			if ( 'not_applied' === item.get( 'status' ) ) {
				item.get( 'items' ).each( function( subItem ) {
					var history = subItem.get( 'history' );

					if ( history ) { /* type duplicate first items hasn't history */
						history.behavior.restore( subItem );
					}
				} );

				item.set( 'status', 'applied' );
			}
		}
	};

	this.redoItem = function( index ) {
		for ( var stepNum = items.length - 1; stepNum >= index; stepNum-- ) {
			var item = items.at( stepNum );

			if ( 'applied' === item.get( 'status' ) ) {
				var reversedSubItems = _.toArray( item.get( 'items' ).models ).reverse();

				_( reversedSubItems ).each( function( subItem ) {
					var history = subItem.get( 'history' );

					if ( history ) { /* type duplicate first items hasn't history */
						history.behavior.restore( subItem, true );
					}
				} );

				item.set( 'status', 'not_applied' );
			}
		}
	};

	this.getModelLabel = function( model ) {
		if ( ! ( model instanceof Backbone.Model ) ) {
			model = new Backbone.Model( model );
		}

		return elementor.getElementData( model ).title;
	};

	this.findView = function( modelID, views ) {
		var founded = false;

		if ( ! views ) {
			views = elementor.getPreviewView().children;
		}

		_.each( views._views, ( view ) => {
			if ( founded ) {
				return;
			}
			// Widget global used getEditModel
			var model = view.getEditModel ? view.getEditModel() : view.model;

			if ( modelID === model.get( 'id' ) ) {
				founded = view;
			} else if ( view.children && view.children.length ) {
				founded = this.findView( modelID, view.children );
			}
		} );

		return founded;
	};

	this.startMovingItem = function( model ) {
		elementor.history.history.startItem( {
			type: 'move',
			title: self.getModelLabel( model ),
			elementType: model.elType || model.get( 'elType' ),
		} );
	};

	this.startInsertTemplate = function( model ) {
		elementor.history.history.startItem( {
			type: 'add',
			title: elementor.translate( 'template' ),
			subTitle: model.get( 'title' ),
			elementType: 'template',
		} );
	};

	this.startDropElement = function() {
		var elementView = elementor.channels.panelElements.request( 'element:selected' );
		elementor.history.history.startItem( {
			type: 'add',
			title: self.getModelLabel( elementView.model ),
			elementType: elementView.model.get( 'widgetType' ) || elementView.model.get( 'elType' ),
		} );
	};

	this.startAddElement = function( model ) {
		elementor.history.history.startItem( {
			type: 'add',
			title: self.getModelLabel( model ),
			elementType: model.elType,
		} );
	};

	this.startPasteStyle = function( model ) {
		elementor.history.history.startItem( {
			type: 'paste_style',
			title: self.getModelLabel( model ),
			elementType: model.get( 'elType' ),
		} );
	};

	this.startResetStyle = function( model ) {
		elementor.history.history.startItem( {
			type: 'reset_style',
			title: self.getModelLabel( model ),
			elementType: model.get( 'elType' ),
		} );
	};

	this.startRemoveElement = function( model ) {
		elementor.history.history.startItem( {
			type: 'remove',
			title: self.getModelLabel( model ),
			elementType: model.get( 'elType' ),
		} );
	};

	init();
};

module.exports = new Manager();
