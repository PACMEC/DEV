var TagPanelView = require( 'elementor-dynamic-tags/tag-panel-view' );

module.exports = Marionette.Behavior.extend( {

	tagView: null,

	listenerAttached: false,

	ui: {
		tagArea: '.elementor-control-tag-area',
		dynamicSwitcher: '.elementor-control-dynamic-switcher',
	},

	events: {
		'click @ui.dynamicSwitcher': 'onDynamicSwitcherClick',
	},

	initialize: function() {
		if ( ! this.listenerAttached ) {
			this.listenTo( this.view.options.elementSettingsModel, 'change:external:__dynamic__', this.onAfterExternalChange );
			this.listenerAttached = true;
		}
	},

	renderTools: function() {
		if ( this.getOption( 'dynamicSettings' ).default ) {
			return;
		}

		var $dynamicSwitcher = jQuery( Marionette.Renderer.render( '#tmpl-elementor-control-dynamic-switcher' ) );

		if ( this.view.model.get( 'label_block' ) ) {
			this.ui.controlTitle.after( $dynamicSwitcher );

			const $responsiveSwitchers = $dynamicSwitcher.next( '.elementor-control-responsive-switchers' );

			if ( $responsiveSwitchers.length ) {
				$responsiveSwitchers.after( $dynamicSwitcher );
			}
		} else {
			this.ui.controlTitle.before( $dynamicSwitcher );
		}

		this.ui.dynamicSwitcher = this.$el.find( this.ui.dynamicSwitcher.selector );
	},

	toggleDynamicClass: function() {
		this.$el.toggleClass( 'elementor-control-dynamic-value', this.isDynamicMode() );
	},

	isDynamicMode: function() {
		var dynamicSettings = this.view.elementSettingsModel.get( '__dynamic__' );

		return ! ! ( dynamicSettings && dynamicSettings[ this.view.model.get( 'name' ) ] );
	},

	createTagsList: function() {
		var tags = _.groupBy( this.getOption( 'tags' ), 'group' ),
			groups = elementor.dynamicTags.getConfig( 'groups' ),
			$tagsList = this.ui.tagsList = jQuery( '<div>', { class: 'elementor-tags-list' } ),
			$tagsListInner = jQuery( '<div>', { class: 'elementor-tags-list__inner' } );

		$tagsList.append( $tagsListInner );

		jQuery.each( groups, function( groupName ) {
			var groupTags = tags[ groupName ];

			if ( ! groupTags ) {
				return;
			}

			var group = this,
				$groupTitle = jQuery( '<div>', { class: 'elementor-tags-list__group-title' } ).text( group.title );

			$tagsListInner.append( $groupTitle );

			groupTags.forEach( function( tag ) {
				var $tag = jQuery( '<div>', { class: 'elementor-tags-list__item' } );

				$tag.text( tag.title ).attr( 'data-tag-name', tag.name );

				$tagsListInner.append( $tag );
			} );
		} );

		$tagsListInner.on( 'click', '.elementor-tags-list__item', this.onTagsListItemClick.bind( this ) );

		elementorCommon.elements.$body.append( $tagsList );
	},

	getTagsList: function() {
		if ( ! this.ui.tagsList ) {
			this.createTagsList();
		}

		return this.ui.tagsList;
	},

	toggleTagsList: function() {
		var $tagsList = this.getTagsList();

		if ( $tagsList.is( ':visible' ) ) {
			$tagsList.hide();

			return;
		}

		const direction = elementorCommon.config.isRTL ? 'left' : 'right';

		$tagsList.show().position( {
			my: `${ direction } top`,
			at: `${ direction } bottom+5`,
			of: this.ui.dynamicSwitcher,
		} );
	},

	setTagView: function( id, name, settings ) {
		if ( this.tagView ) {
			this.tagView.destroy();
		}

		var tagView = this.tagView = new TagPanelView( {
			id: id,
			name: name,
			settings: settings,
			controlName: this.view.model.get( 'name' ),
			dynamicSettings: this.getOption( 'dynamicSettings' ),
		} );

		tagView.render();

		this.ui.tagArea.after( tagView.el );

		this.listenTo( tagView.model, 'change', this.onTagViewModelChange.bind( this ) )
			.listenTo( tagView, 'remove', this.onTagViewRemove.bind( this ) );
	},

	setDefaultTagView: function() {
		var tagData = elementor.dynamicTags.tagTextToTagData( this.getDynamicValue() );

		this.setTagView( tagData.id, tagData.name, tagData.settings );
	},

	tagViewToTagText: function() {
		var tagView = this.tagView;

		return elementor.dynamicTags.tagDataToTagText( tagView.getOption( 'id' ), tagView.getOption( 'name' ), tagView.model );
	},

	getDynamicValue: function() {
		return this.view.elementSettingsModel.get( '__dynamic__' )[ this.view.model.get( 'name' ) ];
	},

	getDynamicControlSettings: function() {
		return {
			control: {
				name: '__dynamic__',
				label: this.view.model.get( 'label' ),
			},
		};
	},

	setDynamicValue: function( value ) {
		var settingKey = this.view.model.get( 'name' ),
			dynamicSettings = this.view.elementSettingsModel.get( '__dynamic__' ) || {};

		dynamicSettings = elementorCommon.helpers.cloneObject( dynamicSettings );

		dynamicSettings[ settingKey ] = value;

		this.view.elementSettingsModel.set( '__dynamic__', dynamicSettings, this.getDynamicControlSettings( settingKey ) );

		this.toggleDynamicClass();
	},

	destroyTagView: function() {
		if ( this.tagView ) {
			this.tagView.destroy();

			this.tagView = null;
		}
	},

	onRender: function() {
		this.$el.addClass( 'elementor-control-dynamic' );

		this.renderTools();

		this.toggleDynamicClass();

		if ( this.isDynamicMode() ) {
			this.setDefaultTagView();
		}
	},

	onDynamicSwitcherClick: function() {
		this.toggleTagsList();
	},

	onTagsListItemClick: function( event ) {
		var $tag = jQuery( event.currentTarget );

		this.setTagView( elementor.helpers.getUniqueID(), $tag.data( 'tagName' ), {} );

		this.setDynamicValue( this.tagViewToTagText() );

		this.toggleTagsList();

		if ( this.tagView.getTagConfig().settings_required ) {
			this.tagView.showSettingsPopup();
		}
	},

	onTagViewModelChange: function() {
		this.setDynamicValue( this.tagViewToTagText() );
	},

	onTagViewRemove: function() {
		var settingKey = this.view.model.get( 'name' ),
			dynamicSettings = this.view.elementSettingsModel.get( '__dynamic__' );

		dynamicSettings = elementorCommon.helpers.cloneObject( dynamicSettings );

		delete dynamicSettings[ settingKey ];

		if ( Object.keys( dynamicSettings ).length ) {
			this.view.elementSettingsModel.set( '__dynamic__', dynamicSettings, this.getDynamicControlSettings( settingKey ) );
		} else {
			this.view.elementSettingsModel.unset( '__dynamic__', this.getDynamicControlSettings( settingKey ) );
		}

		this.toggleDynamicClass();
	},

	onAfterExternalChange: function() {
		this.destroyTagView();

		if ( this.isDynamicMode() ) {
			this.setDefaultTagView();
		}

		this.toggleDynamicClass();
	},

	onDestroy: function() {
		this.destroyTagView();
	},
} );
