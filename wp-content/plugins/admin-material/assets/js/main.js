(function ($) {
  	"use strict";

  	$(document).on('click', '.box > h3, .box > h4, .toggle', function(){
		$(this).toggleClass('active');
		$(this).next( ".hide" ).toggleClass('show');
	});

	$(document).on('click', '#adminmenuback', function(e){
		$('#wp-admin-bar-menu-toggle a').trigger('click');
		$('body').removeClass('folded auto-fold');
	});

	$(document).on('click', '#wp-admin-bar-menu-toggle', function(e){
		$('body').removeClass('folded auto-fold');
	});

	$(document).on('click', '#adminmenu .wp-has-submenu .wp-menu-arrow', function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).parent().next('ul').slideToggle('50');
		$(this).parent().toggleClass('wp-submenu-open');
	});

	jQuery(document).ready(function($){
	    // menu sortable
		$('.admin-menus').sortable({
			items: '.admin-menu-item',
			cursor: 'move',
			containment: 'parent',
			placeholder: 'box box-placeholder'
		});

		$('.admin-menus').on( "sortout", function( event, ui ) {
			ui.item.parent().find('.admin-menu-item').each(function(){
				var item = $(this).find('> div > input');
				if( item.val() != '' ){
					item.val( $(this).index() );
					item.attr('data-sort', $(this).index());
				}
			});
		});
		
		$(document).on('click', '#tab-iconlist ul a', function(e){
			e.stopPropagation();
			e.preventDefault();
			var c = $('#tab-iconlist');
			c.find('.iconlist').hide();
			c.find('a.current').removeClass('current');
			$(this).addClass('current');
			$( $(this).attr('href') ).show();
		});

		// icons dropdown
		var select_icon;
		$('#dropdown').on('show.bs.dropdown', function (e) {
		  var  t = $('#dropdown')
		  	  ,i = $(e.relatedTarget)
		  	  ,p = $( '#'+i.attr('id') ).parent().parent().position()
		  	  ;
		  select_icon = $( '#'+i.attr('id') );
		  $('div', '.iconlist').each(function(){
		  	$(this).removeClass('active');
		  	if($(this).hasClass( i.attr('class') )){
		  		$(this).addClass('active');
		  	}
		  });
		  t.css('top', p.top+42);
		})

		// select icon
		$(document).on('click', '.iconlist div', function(e){
			var c = $(this).attr('class');
			select_icon.attr('class', c);
			select_icon.next().val(c);
		});

		// color
		$('.color-field').wpColorPicker();

		// uploader
		$('.upload-btn').click(function(e) {
	        e.preventDefault();
	        var that = $(this);
	        var image = wp.media({ 
	            title: 'Upload Image',
	            multiple: false
	        }).open()
	        .on('select', function(e){
	            var uploaded_image = image.state().get('selection').first();
	            var image_url = uploaded_image.toJSON().url;
	            that.prev().val(image_url);
	        });
	    });
	});

})(jQuery);
