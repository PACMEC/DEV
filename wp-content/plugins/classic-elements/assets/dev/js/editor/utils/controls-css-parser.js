var Stylesheet = require( 'elementor-editor-utils/stylesheet' ),
	ControlsCSSParser;

ControlsCSSParser = elementorModules.ViewModule.extend( {
	stylesheet: null,

	getDefaultSettings: function() {
		return {
			id: 0,
			settingsModel: null,
			dynamicParsing: {},
		};
	},

	getDefaultElements: function() {
		return {
			$stylesheetElement: jQuery( '<style>', { id: 'elementor-style-' + this.getSettings( 'id' ) } ),
		};
	},

	initStylesheet: function() {
		var breakpoints = elementorFrontend.config.breakpoints;

		this.stylesheet = new Stylesheet();

		this.stylesheet
			.addDevice( 'mobile', 0 )
			.addDevice( 'tablet', breakpoints.md )
			.addDevice( 'desktop', breakpoints.lg );
	},

	addStyleRules: function( styleControls, values, controls, placeholders, replacements ) {
		var self = this,
			dynamicParsedValues = self.getSettings( 'settingsModel' ).parseDynamicSettings( values, self.getSettings( 'dynamicParsing' ), styleControls );

		_.each( styleControls, function( control ) {
			if ( control.styleFields && control.styleFields.length ) {
				self.addRepeaterControlsStyleRules( values[ control.name ], control.styleFields, controls, placeholders, replacements );
			}

			if ( control.dynamic && control.dynamic.active && values.__dynamic__ && values.__dynamic__[ control.name ] ) {
				self.addDynamicControlStyleRules( values.__dynamic__[ control.name ], control );
			}

			if ( ! control.selectors ) {
				return;
			}

			self.addControlStyleRules( control, dynamicParsedValues, controls, placeholders, replacements );
		} );
	},

	addControlStyleRules: function( control, values, controls, placeholders, replacements ) {
		ControlsCSSParser.addControlStyleRules(
			this.stylesheet,
			control,
			controls,
			( StyleControl ) => this.getStyleControlValue( StyleControl, values ),
			placeholders,
			replacements
		);
	},

	getStyleControlValue: function( control, values ) {
		var value = values[ control.name ];

		if ( control.selectors_dictionary ) {
			value = control.selectors_dictionary[ value ] || value;
		}

		if ( ! _.isNumber( value ) && _.isEmpty( value ) ) {
			return;
		}

		return value;
	},

	addRepeaterControlsStyleRules: function( repeaterValues, repeaterControlsItems, controls, placeholders, replacements ) {
		var self = this;

		repeaterControlsItems.forEach( function( item, index ) {
			var itemModel = repeaterValues.models[ index ];

			self.addStyleRules(
				item,
				itemModel.attributes,
				controls,
				placeholders.concat( [ '{{CURRENT_ITEM}}' ] ),
				replacements.concat( [ '.elementor-repeater-item-' + itemModel.get( '_id' ) ] )
			);
		} );
	},

	addDynamicControlStyleRules: function( value, control ) {
		var self = this;

		elementor.dynamicTags.parseTagsText( value, control.dynamic, function( id, name, settings ) {
			var tag = elementor.dynamicTags.createTag( id, name, settings );

			if ( ! tag ) {
				return;
			}

			var tagSettingsModel = tag.model,
				styleControls = tagSettingsModel.getStyleControls();

			if ( ! styleControls.length ) {
				return;
			}

			self.addStyleRules( tagSettingsModel.getStyleControls(), tagSettingsModel.attributes, tagSettingsModel.controls, [ '{{WRAPPER}}' ], [ '#elementor-tag-' + id ] );
		} );
	},

	addStyleToDocument: function() {
		elementor.$previewContents.find( 'head' ).append( this.elements.$stylesheetElement );

		this.elements.$stylesheetElement.text( this.stylesheet );
	},

	removeStyleFromDocument: function() {
		this.elements.$stylesheetElement.remove();
	},

	onInit: function() {
		elementorModules.ViewModule.prototype.onInit.apply( this, arguments );

		this.initStylesheet();
	},
} );

ControlsCSSParser.addControlStyleRules = function( stylesheet, control, controls, valueCallback, placeholders, replacements ) {
	var value = valueCallback( control );

	if ( undefined === value ) {
		return;
	}

	_.each( control.selectors, function( cssProperty, selector ) {
		var outputCssProperty;

		try {
			outputCssProperty = cssProperty.replace( /{{(?:([^.}]+)\.)?([^}| ]*)(?: *\|\| *(?:([^.}]+)\.)?([^}| ]*) *)*}}/g, function( originalPhrase, controlName, placeholder, fallbackControlName, fallbackValue ) {
				const externalControlMissing = controlName && ! controls[ controlName ];

				let parsedValue = '';

				if ( ! externalControlMissing ) {
					parsedValue = ControlsCSSParser.parsePropertyPlaceholder( control, value, controls, valueCallback, placeholder, controlName );
				}

				if ( ! parsedValue && 0 !== parsedValue ) {
					if ( fallbackValue ) {
						parsedValue = fallbackValue;

						const stringValueMatches = parsedValue.match( /^(['"])(.*)\1$/ );

						if ( stringValueMatches ) {
							parsedValue = stringValueMatches[ 2 ];
						} else if ( ! isFinite( parsedValue ) ) {
							if ( fallbackControlName && ! controls[ fallbackControlName ] ) {
								return '';
							}

							parsedValue = ControlsCSSParser.parsePropertyPlaceholder( control, value, controls, valueCallback, fallbackValue, fallbackControlName );
						}
					}

					if ( ! parsedValue && 0 !== parsedValue ) {
						if ( externalControlMissing ) {
							return '';
						}

						throw '';
					}
				}

				return parsedValue;
			} );
		} catch ( e ) {
			return;
		}

		if ( _.isEmpty( outputCssProperty ) ) {
			return;
		}

		var devicePattern = /^(?:\([^)]+\)){1,2}/,
			deviceRules = selector.match( devicePattern ),
			query = {};

		if ( deviceRules ) {
			deviceRules = deviceRules[ 0 ];

			selector = selector.replace( devicePattern, '' );

			var pureDevicePattern = /\(([^)]+)\)/g,
				pureDeviceRules = [],
				matches;

			matches = pureDevicePattern.exec( deviceRules );
			while ( matches ) {
				pureDeviceRules.push( matches[ 1 ] );
				matches = pureDevicePattern.exec( deviceRules );
			}

			_.each( pureDeviceRules, function( deviceRule ) {
				if ( 'desktop' === deviceRule ) {
					return;
				}

				var device = deviceRule.replace( /\+$/, '' ),
					endPoint = device === deviceRule ? 'max' : 'min';

				query[ endPoint ] = device;
			} );
		}

		_.each( placeholders, function( placeholder, index ) {
			// Check if it's a RegExp
			var regexp = placeholder.source ? placeholder.source : placeholder,
				placeholderPattern = new RegExp( regexp, 'g' );

			selector = selector.replace( placeholderPattern, replacements[ index ] );
		} );

		if ( ! Object.keys( query ).length && control.responsive ) {
			query = _.pick( elementorCommon.helpers.cloneObject( control.responsive ), [ 'min', 'max' ] );

			if ( 'desktop' === query.max ) {
				delete query.max;
			}
		}

		stylesheet.addRules( selector, outputCssProperty, query );
	} );
};

ControlsCSSParser.parsePropertyPlaceholder = function( control, value, controls, valueCallback, placeholder, parserControlName ) {
	if ( parserControlName ) {
		control = _.findWhere( controls, { name: parserControlName } );

		value = valueCallback( control );
	}

	return elementor.getControlView( control.type ).getStyleValue( placeholder, value, control );
};

module.exports = ControlsCSSParser;
