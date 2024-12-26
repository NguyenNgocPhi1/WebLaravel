(function($) {
   "use strict";
   var HT = {}; // Khai báo là 1 đối tượng
   var timer = null
   var _token = $('meta[name="csrf-token"]').attr('content');

/* MAIN VARIABLE */

   // FUNCTION DECLARGE
   $.fn.elExists = function() {
     return this.length > 0;
   };

   HT.addCart = () => {
      $(document).on('click', '.addToCart', function(e){
         e.preventDefault()
         let _this = $(this)
         let id = _this.attr('data-id')
         let quantity = $('.quantity-text').val()
         if(typeof quantity === 'undefined'){
            quantity = 1
         }

         let attribute_id = []
         $('.attribute-value .choose-attribute').each(function(){
            let _this = $(this)
            if(_this.hasClass('active')){
               attribute_id.push(_this.attr('data-attributeid'))
            }
         })

         let option = {
            id: id,
            quantity: quantity,
            attribute_id: attribute_id,
            _token: _token
         }

         $.ajax({
				url: 'ajax/cart/create', 
				type: 'POST', 
				data: option, 
				dataType: 'json', 
				beforeSend: function() {
					
				},
				success: function(res) {
               toastr.clear()
               if(res.code === 10){
                  toastr.success(res.messages, 'Thông báo từ hệ thống')
               }else{
                  toastr.error('Có vấn đề xảy ra! Hãy thử lại', 'Thông báo từ hệ thống')
               }
				},
			});
      })
   }
   

   // Document ready functions
   $(document).ready(function() {
      HT.addCart()
   });

})(jQuery);
