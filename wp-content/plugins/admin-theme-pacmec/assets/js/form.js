(function ($) {
  "use strict";

  $(document).on('blur', 'input, textarea', function(e){
    $(this).val() ? $(this).addClass('has-value') : $(this).removeClass('has-value');
  });
  jQuery(document).ready(function(){
	jQuery("input").trigger('blur');
  });
  
})(jQuery);
