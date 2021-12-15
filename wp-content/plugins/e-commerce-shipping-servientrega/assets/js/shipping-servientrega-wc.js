(function($){

    $('button.generate_sticker').click(function (e) {
        e.preventDefault();

        $.ajax({
           data: {
               action: 'servientrega_generate_sticker',
               nonce: $(this).data("nonce"),
               guide_number: $(this).data("guide")
           },
           type: 'POST',
           url: ajaxurl,
           dataType: "json",
           beforeSend : () => {
               Swal.fire({
                    title: 'Generando stickers de la guía',
                    onOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
               });
           },
           success: (r) => {
               if (r.status){
                   Swal.close();
                   window.location.replace(r.url);
               }else{
                   Swal.fire(
                       'Error',
                       r.message,
                       'error'
                   );
               }
           }
        });
    })

})(jQuery);