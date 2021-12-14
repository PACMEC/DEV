(function ($) {
    "use strict";
    function arrowpress_coreLikeCountGallery() {
		$('body').on('click', '.arrowpress_core-post-like', function (event) {
			event.preventDefault();
			var heart = $(this);
			var post_id = heart.data("post_id");
			var like_type = heart.data('like_type') ? heart.data('like_type') : 'post';
			heart.html("<i id='icon-like' class='ion-android-favorite-outline'></i><i id='icon-spinner' class='fa fa-spinner fa-spin'></i>");
			$.ajax({
				type: "post",
				url: ajax_var.url,
				data: "action=arrowpress_core-post-like&nonce=" + ajax_var.nonce + "&arrowpress_core_post_like=&post_id=" + post_id + "&like_type=" + like_type,
				success: function (count) {
					var lecount = count.replace("already", "");
					if(count == "0" || count == "1"){
						lecount = "Like";
					}else{
						lecount = "Likes"
					}
					if (count.indexOf("already") !== -1)
					{
						heart.prop('title', cryptcio_params.cryptcio_like_text);
						heart.removeClass("liked");
						heart.html("<i id='icon-unlike' class='ion-android-favorite-outline'></i>" + " " + lecount);
					}
					else
					{
						heart.prop('title', cryptcio_params.cryptcio_unlike_text);
						heart.addClass("liked");
						heart.html("<i id='icon-like' class='ion-android-favorite-outline'></i>" + count + " " + lecount);
					}
				}
			});
		});
	}    
    $(document).ready(function () {
		$( ".arrowpress-container" ).each(function( index ) {
		  	if ($(this).attr('data-max-width')) {
		  		var mwidth=$(this).attr('data-max-width')+'px';
		  		$(this).css("max-width",mwidth);
		  		$(this).addClass('container-set-mwidth');
			}
		}); 
		var target = $('.arrowpress-heading').data("arrowpress-target");   	
    	var tablet = $('.'+target).data("arrowpress-responsive-tablet");
    	var desktop = $('.'+desktop).data("arrowpress-responsive-desktop");
    	var tablet_port = $('.'+target).data("arrowpress-responsive-tablet-port");
    	var mobile_land = $('.'+target).data("arrowpress-responsive-mob-land");
    	var mobile = $('.'+target).data("arrowpress-responsive-mob");
    	var csstarget = $('.'+target+' '+$('.arrowpress-heading').data("arrowpress-csstarget"));
    	var styles = {};
    	var stylesmob = {};
    	function apr_responsive(){
			if(window.matchMedia('(max-width: 2000px)').matches){
	    		$.each(desktop, function(index, val) {   
					styles[index] = val;   			
				});		
			    $(csstarget).css(styles);			
	    	}	     	    		
	    	if(window.matchMedia('(max-width: 1199px)').matches){
	    		$.each(tablet, function(index, val) {   
					styles[index] = val;   			
				});		
			    $(csstarget).css(styles);			
	    	}
	    	if(window.matchMedia('(max-width: 991px)').matches){
	    		$.each(tablet_port, function(index, val) {   
					styles[index] = val;   			
				});		
			    $(csstarget).css(styles);			
	    	}	 
	    	if(window.matchMedia('(max-width: 767px)').matches){
	    		$.each(mobile_land, function(index, val) {   
					styles[index] = val;   			
				});	
			    $(csstarget).css(styles);			
	    	}	 
	    	if(window.matchMedia('(max-width: 479px)').matches){
	    		$.each(mobile, function(index, val) {   
					styles[index] = val;   			
				});
			    $(csstarget).css(styles);			
	    	}
	    	if(window.matchMedia('(min-width: 1200px)').matches){
	    		$.each(desktop, function(index, val) {   
					styles[index] = val;   			
				});		
			    $(csstarget).css(styles);			
	    	}	    			    	   	   	
    	}
    	arrowpress_coreLikeCountGallery();
    	// call on document load
	    apr_responsive();
	    $(window).resize(function(){
	        // call on window resize
	        apr_responsive();
	    });
    });   
})(jQuery);