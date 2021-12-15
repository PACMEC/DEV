module.exports = elementorModules.ViewModule.extend( {
	oldAspectRatio: null,

	oldAnimation: null,

	swiper: null,

	player: null,

	getDefaultSettings: function() {
		return {
			classes: {
				aspectRatio: 'elementor-aspect-ratio-%s',
				item: 'elementor-lightbox-item',
				image: 'elementor-lightbox-image',
				videoContainer: 'elementor-video-container',
				videoWrapper: 'elementor-fit-aspect-ratio',
				playButton: 'elementor-custom-embed-play',
				playButtonIcon: 'fa',
				playing: 'elementor-playing',
				hidden: 'elementor-hidden',
				invisible: 'elementor-invisible',
				preventClose: 'elementor-lightbox-prevent-close',
				slideshow: {
					container: 'swiper-container',
					slidesWrapper: 'swiper-wrapper',
					prevButton: 'elementor-swiper-button elementor-swiper-button-prev',
					nextButton: 'elementor-swiper-button elementor-swiper-button-next',
					prevButtonIcon: 'eicon-chevron-left',
					nextButtonIcon: 'eicon-chevron-right',
					slide: 'swiper-slide',
				},
			},
			selectors: {
				links: 'a, [data-elementor-lightbox]',
				slideshow: {
					activeSlide: '.swiper-slide-active',
					prevSlide: '.swiper-slide-prev',
					nextSlide: '.swiper-slide-next',
				},
			},
			modalOptions: {
				id: 'elementor-lightbox',
				entranceAnimation: 'zoomIn',
				videoAspectRatio: 169,
				position: {
					enable: false,
				},
			},
		};
	},

	getModal: function() {
		if ( ! module.exports.modal ) {
			this.initModal();
		}

		return module.exports.modal;
	},

	initModal: function() {
		var modal = module.exports.modal = elementorFrontend.getDialogsManager().createWidget( 'lightbox', {
			className: 'elementor-lightbox',
			closeButton: true,
			closeButtonClass: 'eicon-close',
			selectors: {
				preventClose: '.' + this.getSettings( 'classes.preventClose' ),
			},
			hide: {
				onClick: true,
			},
		} );

		modal.on( 'hide', function() {
			modal.setMessage( '' );
		} );
	},

	showModal: function( options ) {
		var self = this,
			defaultOptions = self.getDefaultSettings().modalOptions;

		self.setSettings( 'modalOptions', jQuery.extend( defaultOptions, options.modalOptions ) );

		var modal = self.getModal();

		modal.setID( self.getSettings( 'modalOptions.id' ) );

		modal.onShow = function() {
			DialogsManager.getWidgetType( 'lightbox' ).prototype.onShow.apply( modal, arguments );

			self.setEntranceAnimation();
		};

		modal.onHide = function() {
			DialogsManager.getWidgetType( 'lightbox' ).prototype.onHide.apply( modal, arguments );

			modal.getElements( 'message' ).removeClass( 'animated' );
		};

		switch ( options.type ) {
			case 'image':
				self.setImageContent( options.url );

				break;
			case 'video':
				self.setVideoContent( options );

				break;
			case 'slideshow':
				self.setSlideshowContent( options.slideshow );

				break;
			default:
				self.setHTMLContent( options.html );
		}

		modal.show();
	},

	setHTMLContent: function( html ) {
		this.getModal().setMessage( html );
	},

	setImageContent: function( imageURL ) {
		var self = this,
			classes = self.getSettings( 'classes' ),
			$item = jQuery( '<div>', { class: classes.item } ),
			$image = jQuery( '<img>', { src: imageURL, class: classes.image } );

		$item.append( $image );

		self.getModal().setMessage( $item );
	},

	setVideoContent: function( options ) {
		var classes = this.getSettings( 'classes' ),
			$videoContainer = jQuery( '<div>', { class: `${ classes.videoContainer } ${ classes.preventClose }` } ),
			$videoWrapper = jQuery( '<div>', { class: classes.videoWrapper } ),
			$videoElement,
			modal = this.getModal();

		if ( 'hosted' === options.videoType ) {
			var videoParams = jQuery.extend( { src: options.url, autoplay: '' }, options.videoParams );

			$videoElement = jQuery( '<video>', videoParams );
		} else {
			var videoURL = options.url.replace( '&autoplay=0', '' ) + '&autoplay=1';

			$videoElement = jQuery( '<iframe>', { src: videoURL, allowfullscreen: 1 } );
		}

		$videoContainer.append( $videoWrapper );

		$videoWrapper.append( $videoElement );

		modal.setMessage( $videoContainer );

		this.setVideoAspectRatio();

		var onHideMethod = modal.onHide;

		modal.onHide = function() {
			onHideMethod();

			modal.getElements( 'message' ).removeClass( 'elementor-fit-aspect-ratio' );
		};
	},

	setSlideshowContent: function( options ) {
		var $ = jQuery,
			self = this,
			classes = self.getSettings( 'classes' ),
			slideshowClasses = classes.slideshow,
			$container = $( '<div>', { class: slideshowClasses.container } ),
			$slidesWrapper = $( '<div>', { class: slideshowClasses.slidesWrapper } ),
			$prevButton = $( '<div>', { class: slideshowClasses.prevButton + ' ' + classes.preventClose } ).html( $( '<i>', { class: slideshowClasses.prevButtonIcon } ) ),
			$nextButton = $( '<div>', { class: slideshowClasses.nextButton + ' ' + classes.preventClose } ).html( $( '<i>', { class: slideshowClasses.nextButtonIcon } ) );

		options.slides.forEach( function( slide ) {
			var slideClass = slideshowClasses.slide + ' ' + classes.item;

			if ( slide.video ) {
				slideClass += ' ' + classes.video;
			}

			var $slide = $( '<div>', { class: slideClass } );

			if ( slide.video ) {
				$slide.attr( 'data-elementor-slideshow-video', slide.video );

				var $playIcon = $( '<div>', { class: classes.playButton } ).html( $( '<i>', { class: classes.playButtonIcon } ) );

				$slide.append( $playIcon );
			} else {
				var $zoomContainer = $( '<div>', { class: 'swiper-zoom-container' } ),
					$slideImage = $( '<img>', { class: classes.image + ' ' + classes.preventClose, src: slide.image } );

				$zoomContainer.append( $slideImage );

				$slide.append( $zoomContainer );
			}

			$slidesWrapper.append( $slide );
		} );

		$container.append(
			$slidesWrapper,
			$prevButton,
			$nextButton
		);

		var modal = self.getModal();

		modal.setMessage( $container );

		var onShowMethod = modal.onShow;

		modal.onShow = function() {
			onShowMethod();

			var swiperOptions = {
				navigation: {
					prevEl: $prevButton,
					nextEl: $nextButton,
				},
				pagination: {
					clickable: true,
				},
				on: {
					slideChangeTransitionEnd: self.onSlideChange,
				},
				grabCursor: true,
				runCallbacksOnInit: false,
				loop: true,
				keyboard: true,
			};

			if ( options.swiper ) {
				$.extend( swiperOptions, options.swiper );
			}

			self.swiper = new Swiper( $container, swiperOptions );

			self.setVideoAspectRatio();

			self.playSlideVideo();
		};
	},

	setVideoAspectRatio: function( aspectRatio ) {
		aspectRatio = aspectRatio || this.getSettings( 'modalOptions.videoAspectRatio' );

		var $widgetContent = this.getModal().getElements( 'widgetContent' ),
			oldAspectRatio = this.oldAspectRatio,
			aspectRatioClass = this.getSettings( 'classes.aspectRatio' );

		this.oldAspectRatio = aspectRatio;

		if ( oldAspectRatio ) {
			$widgetContent.removeClass( aspectRatioClass.replace( '%s', oldAspectRatio ) );
		}

		if ( aspectRatio ) {
			$widgetContent.addClass( aspectRatioClass.replace( '%s', aspectRatio ) );
		}
	},

	getSlide: function( slideState ) {
		return jQuery( this.swiper.slides ).filter( this.getSettings( 'selectors.slideshow.' + slideState + 'Slide' ) );
	},

	playSlideVideo: function() {
		const $activeSlide = this.getSlide( 'active' ),
			videoURL = $activeSlide.data( 'elementor-slideshow-video' );

		if ( ! videoURL ) {
			return;
		}

		const classes = this.getSettings( 'classes' ),
			$videoContainer = jQuery( '<div>', { class: classes.videoContainer + ' ' + classes.invisible } ),
			$videoWrapper = jQuery( '<div>', { class: classes.videoWrapper } ),
			$playIcon = $activeSlide.children( '.' + classes.playButton );

		let videoType, apiProvider;

		$videoContainer.append( $videoWrapper );

		$activeSlide.append( $videoContainer );

		if ( -1 !== videoURL.indexOf( 'vimeo.com' ) ) {
			videoType = 'vimeo';
			apiProvider = elementorFrontend.utils.vimeo;
		} else if ( videoURL.match( /^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com)/ ) ) {
			videoType = 'youtube';
			apiProvider = elementorFrontend.utils.youtube;
		}

		const videoID = apiProvider.getVideoIDFromURL( videoURL );

		apiProvider.onApiReady( ( apiObject ) => {
			if ( 'youtube' === videoType ) {
				this.prepareYTVideo( apiObject, videoID, $videoContainer, $videoWrapper, $playIcon );
			} else if ( 'vimeo' === videoType ) {
				this.prepareVimeoVideo( apiObject, videoID, $videoContainer, $videoWrapper, $playIcon );
			}
		} );

		$playIcon.addClass( classes.playing ).removeClass( classes.hidden );
	},

	prepareYTVideo: function( YT, videoID, $videoContainer, $videoWrapper, $playIcon ) {
		const classes = this.getSettings( 'classes' ),
			$videoPlaceholderElement = jQuery( '<div>' );
		let startStateCode = YT.PlayerState.PLAYING;

		$videoWrapper.append( $videoPlaceholderElement );

		// Since version 67, Chrome doesn't fire the `PLAYING` state at start time
		if ( window.chrome ) {
			startStateCode = YT.PlayerState.UNSTARTED;
		}

		$videoContainer.addClass( 'elementor-loading' + ' ' + classes.invisible );

		this.player = new YT.Player( $videoPlaceholderElement[ 0 ], {
			videoId: videoID,
			events: {
				onReady: () => {
					$playIcon.addClass( classes.hidden );

					$videoContainer.removeClass( classes.invisible );

					this.player.playVideo();
				},
				onStateChange: ( event ) => {
					if ( event.data === startStateCode ) {
						$videoContainer.removeClass( 'elementor-loading' + ' ' + classes.invisible );
					}
				},
			},
			playerVars: {
				controls: 0,
				rel: 0,
			},
		} );
	},

	prepareVimeoVideo: function( Vimeo, videoId, $videoContainer, $videoWrapper, $playIcon ) {
		const classes = this.getSettings( 'classes' ),
			vimeoOptions = {
				id: videoId,
				autoplay: true,
				transparent: false,
				playsinline: false,
			};

		this.player = new Vimeo.Player( $videoWrapper, vimeoOptions );

		this.player.ready().then( () => {
			$playIcon.addClass( classes.hidden );

			$videoContainer.removeClass( classes.invisible );
		} );
	},

	setEntranceAnimation: function( animation ) {
		animation = animation || elementorFrontend.getCurrentDeviceSetting( this.getSettings( 'modalOptions' ), 'entranceAnimation' );

		var $widgetMessage = this.getModal().getElements( 'message' );

		if ( this.oldAnimation ) {
			$widgetMessage.removeClass( this.oldAnimation );
		}

		this.oldAnimation = animation;

		if ( animation ) {
			$widgetMessage.addClass( 'animated ' + animation );
		}
	},

	isLightboxLink: function( element ) {
		if ( 'A' === element.tagName && ( element.hasAttribute( 'download' ) || ! /\.(png|jpe?g|gif|svg)(\?.*)?$/i.test( element.href ) ) ) {
			return false;
		}

		var generalOpenInLightbox = elementorFrontend.getGeneralSettings( 'elementor_global_image_lightbox' ),
			currentLinkOpenInLightbox = element.dataset.elementorOpenLightbox;

		return 'yes' === currentLinkOpenInLightbox || ( generalOpenInLightbox && 'no' !== currentLinkOpenInLightbox );
	},

	openLink: function( event ) {
		var element = event.currentTarget,
			$target = jQuery( event.target ),
			editMode = elementorFrontend.isEditMode(),
			isClickInsideElementor = !! $target.closest( '#elementor' ).length;

		if ( ! this.isLightboxLink( element ) ) {
			if ( editMode && isClickInsideElementor ) {
				event.preventDefault();
			}

			return;
		}

		event.preventDefault();

		if ( editMode && ! elementorFrontend.getGeneralSettings( 'elementor_enable_lightbox_in_editor' ) ) {
			return;
		}

		var lightboxData = {};

		if ( element.dataset.elementorLightbox ) {
			lightboxData = JSON.parse( element.dataset.elementorLightbox );
		}

		if ( lightboxData.type && 'slideshow' !== lightboxData.type ) {
			this.showModal( lightboxData );

			return;
		}

		if ( ! element.dataset.elementorLightboxSlideshow ) {
			this.showModal( {
				type: 'image',
				url: element.href,
			} );

			return;
		}

		var slideshowID = element.dataset.elementorLightboxSlideshow;

		var $allSlideshowLinks = jQuery( this.getSettings( 'selectors.links' ) ).filter( function() {
			const $this = jQuery( this );

			return slideshowID === this.dataset.elementorLightboxSlideshow && ! $this.parent( '.swiper-slide-duplicate' ).length && ! $this.parents( '.slick-cloned' ).length;
		} );

		const slides = [];

		$allSlideshowLinks.each( function() {
			const slideVideo = this.dataset.elementorLightboxVideo;

			let slideIndex = this.dataset.elementorLightboxIndex;

			if ( undefined === slideIndex ) {
				slideIndex = $allSlideshowLinks.index( this );
			}

			var slideData = {
				image: this.href,
				index: slideIndex,
			};

			if ( slideVideo ) {
				slideData.video = slideVideo;
			}

			slides.push( slideData );
		} );

		slides.sort( function( a, b ) {
			return a.index - b.index;
		} );

		var initialSlide = element.dataset.elementorLightboxIndex;

		if ( undefined === initialSlide ) {
			initialSlide = $allSlideshowLinks.index( element );
		}

		this.showModal( {
			type: 'slideshow',
			modalOptions: {
				id: 'elementor-lightbox-slideshow-' + slideshowID,
			},
			slideshow: {
				slides: slides,
				swiper: {
					initialSlide: +initialSlide,
				},
			},
		} );
	},

	bindEvents: function() {
		elementorFrontend.elements.$document.on( 'click', this.getSettings( 'selectors.links' ), this.openLink );
	},

	onSlideChange: function() {
		this
			.getSlide( 'prev' )
			.add( this.getSlide( 'next' ) )
			.add( this.getSlide( 'active' ) )
			.find( '.' + this.getSettings( 'classes.videoWrapper' ) )
			.remove();

		this.playSlideVideo();
	},
} );
