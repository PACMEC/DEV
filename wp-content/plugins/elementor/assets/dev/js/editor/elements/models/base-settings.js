var BaseSettingsModel;

BaseSettingsModel = Backbone.Model.extend( {
	options: {},

	initialize: function( data, options ) {
		var self = this;

		// Keep the options for cloning
		self.options = options;

		self.controls = elementor.mergeControlsSettings( options.controls );

		self.validators = {};

		if ( ! self.controls ) {
			return;
		}

		var attrs = data || {},
			defaults = {};

		_.each( self.controls, function( control ) {
			var isUIControl = -1 !== control.features.indexOf( 'ui' );

			if ( isUIControl ) {
				return;
			}
			var controlName = control.name;

			if ( 'object' === typeof control.default ) {
				defaults[ controlName ] = elementorCommon.helpers.cloneObject( control.default );
			} else {
				defaults[ controlName ] = control.default;
			}

			var isDynamicControl = control.dynamic && control.dynamic.active,
				hasDynamicSettings = isDynamicControl && attrs.__dynamic__ && attrs.__dynamic__[ controlName ];

			if ( isDynamicControl && ! hasDynamicSettings && control.dynamic.default ) {
				if ( ! attrs.__dynamic__ ) {
					attrs.__dynamic__ = {};
				}

				attrs.__dynamic__[ controlName ] = control.dynamic.default;

				hasDynamicSettings = true;
			}

			// Check if the value is a plain object ( and not an array )
			var isMultipleControl = jQuery.isPlainObject( control.default );

			if ( undefined !== attrs[ controlName ] && isMultipleControl && ! _.isObject( attrs[ controlName ] ) && ! hasDynamicSettings ) {
				elementor.debug.addCustomError(
					new TypeError( 'An invalid argument supplied as multiple control value' ),
					'InvalidElementData',
					'Element `' + ( self.get( 'widgetType' ) || self.get( 'elType' ) ) + '` got <' + attrs[ controlName ] + '> as `' + controlName + '` value. Expected array or object.'
				);

				delete attrs[ controlName ];
			}

			if ( undefined === attrs[ controlName ] ) {
				attrs[ controlName ] = defaults[ controlName ];
			}
		} );

		self.defaults = defaults;

		self.handleRepeaterData( attrs );

		self.set( attrs );
	},

	handleRepeaterData: function( attrs ) {
		_.each( this.controls, function( field ) {
			if ( field.is_repeater ) {
				// TODO: Apply defaults on each field in repeater fields
				if ( ! ( attrs[ field.name ] instanceof Backbone.Collection ) ) {
					attrs[ field.name ] = new Backbone.Collection( attrs[ field.name ], {
						model: function( attributes, options ) {
							options = options || {};

							options.controls = field.fields;

							if ( ! attributes._id ) {
								attributes._id = elementor.helpers.getUniqueID();
							}

							return new BaseSettingsModel( attributes, options );
						},
					} );
				}
			}
		} );
	},

	getFontControls() {
		return this.getControlsByType( 'font' );
	},

	getIconsControls() {
		return this.getControlsByType( 'icons' );
	},

	getControlsByType( type ) {
		return _.filter( this.getActiveControls(), ( control ) => {
			return type === control.type;
		} );
	},

	getStyleControls: function( controls, attributes ) {
		var self = this;

		controls = elementorCommon.helpers.cloneObject( self.getActiveControls( controls, attributes ) );

		var styleControls = [];

		jQuery.each( controls, function() {
			var control = this,
				controlDefaultSettings = elementor.config.controls[ control.type ];

			control = jQuery.extend( {}, controlDefaultSettings, control );

			if ( control.fields ) {
				var styleFields = [];

				self.attributes[ control.name ].each( function( item ) {
					styleFields.push( self.getStyleControls( control.fields, item.attributes ) );
				} );

				control.styleFields = styleFields;
			}

			if ( control.fields || ( control.dynamic && control.dynamic.active ) || self.isStyleControl( control.name, controls ) ) {
				styleControls.push( control );
			}
		} );

		return styleControls;
	},

	isStyleControl: function( attribute, controls ) {
		controls = controls || this.controls;

		var currentControl = _.find( controls, function( control ) {
			return attribute === control.name;
		} );

		return currentControl && ! _.isEmpty( currentControl.selectors );
	},

	getClassControls: function( controls ) {
		controls = controls || this.controls;

		return _.filter( controls, function( control ) {
			return ! _.isUndefined( control.prefix_class );
		} );
	},

	isClassControl: function( attribute ) {
		var currentControl = _.find( this.controls, function( control ) {
			return attribute === control.name;
		} );

		return currentControl && ! _.isUndefined( currentControl.prefix_class );
	},

	getControl: function( id ) {
		return _.find( this.controls, function( control ) {
			return id === control.name;
		} );
	},

	getActiveControls: function( controls, attributes ) {
		const activeControls = {};

		if ( ! controls ) {
			controls = this.controls;
		}

		if ( ! attributes ) {
			attributes = this.attributes;
		}

		jQuery.each( controls, ( controlKey, control ) => {
			if ( elementor.helpers.isActiveControl( control, attributes ) ) {
				activeControls[ controlKey ] = control;
			}
		} );

		return activeControls;
	},

	clone: function() {
		return new BaseSettingsModel( elementorCommon.helpers.cloneObject( this.attributes ), elementorCommon.helpers.cloneObject( this.options ) );
	},

	setExternalChange: function( key, value ) {
		var self = this,
			settingsToChange;

		if ( 'object' === typeof key ) {
			settingsToChange = key;
		} else {
			settingsToChange = {};

			settingsToChange[ key ] = value;
		}

		self.set( settingsToChange );

		jQuery.each( settingsToChange, function( changedKey, changedValue ) {
			self.trigger( 'change:external:' + changedKey, changedValue );
		} );
	},

	parseDynamicSettings: function( settings, options, controls ) {
		var self = this;

		settings = elementorCommon.helpers.cloneObject( settings || self.attributes );

		options = options || {};

		controls = controls || this.controls;

		jQuery.each( controls, function() {
			var control = this,
				valueToParse;

			if ( 'repeater' === control.type ) {
				valueToParse = settings[ control.name ];
				valueToParse.forEach( function( value, key ) {
					valueToParse[ key ] = self.parseDynamicSettings( value, options, control.fields );
				} );

				return;
			}

			valueToParse = settings.__dynamic__ && settings.__dynamic__[ control.name ];

			if ( ! valueToParse ) {
				return;
			}

			var dynamicSettings = control.dynamic;

			if ( undefined === dynamicSettings ) {
				dynamicSettings = elementor.config.controls[ control.type ].dynamic;
			}

			if ( ! dynamicSettings || ! dynamicSettings.active ) {
				return;
			}

			var dynamicValue;

			try {
				dynamicValue = elementor.dynamicTags.parseTagsText( valueToParse, dynamicSettings, elementor.dynamicTags.getTagDataContent );
			} catch ( error ) {
				if ( elementor.dynamicTags.CACHE_KEY_NOT_FOUND_ERROR !== error.message ) {
					throw error;
				}

				dynamicValue = '';

				if ( options.onServerRequestStart ) {
					options.onServerRequestStart();
				}

				elementor.dynamicTags.refreshCacheFromServer( function() {
					if ( options.onServerRequestEnd ) {
						options.onServerRequestEnd();
					}
				} );
			}

			if ( dynamicSettings.property ) {
				settings[ control.name ][ dynamicSettings.property ] = dynamicValue;
			} else {
				settings[ control.name ] = dynamicValue;
			}
		} );

		return settings;
	},

	toJSON: function( options ) {
		var data = Backbone.Model.prototype.toJSON.call( this );

		options = options || {};

		delete data.widgetType;
		delete data.elType;
		delete data.isInner;

		_.each( data, function( attribute, key ) {
			if ( attribute && attribute.toJSON ) {
				data[ key ] = attribute.toJSON();
			}
		} );

		// TODO: `options.removeDefault` is a bc since 2.5.14
		if ( ( options.remove && -1 !== options.remove.indexOf( 'default' ) ) || options.removeDefault ) {
			var controls = this.controls;

			_.each( data, function( value, key ) {
				const control = controls[ key ];

				if ( ! control ) {
					return;
				}

				// TODO: use `save_default` in text|textarea controls.
				if ( control.save_default || ( ( 'text' === control.type || 'textarea' === control.type ) && data[ key ] ) ) {
					return;
				}

				if ( _.isEqual( data[ key ], control.default ) ) {
					delete data[ key ];
				}
			} );
		}

		return elementorCommon.helpers.cloneObject( data );
	},
} );

module.exports = BaseSettingsModel;
