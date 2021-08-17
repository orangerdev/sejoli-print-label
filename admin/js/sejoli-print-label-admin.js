(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(window).load(function() {

	 	// executes when complete page is fully loaded, including all frames, objects and images
		$('select.update-order-select').append('<option value="print-shipment-label">Cetak Label Pengiriman</option>');

	});

	$(document).ready(function($){

        $('body').on( 'change', 'select.update-order-select', function(e){
        	e.preventDefault();

        	if($(this).val() == 'print-shipment-label'){

        		$(this).parent().find('.button').removeClass('update-order').addClass('print-shipment-label');

        	}

        });

        let sejoli_print_shipment_label = function(order_id) {

	        $.ajax({
	            url  : sejoli_print_label.print_shipment_label.ajaxurl,
	            type : 'POST',
	            data : {
	                orders : order_id,
	                nonce  : sejoli_print_label.print_shipment_label.nonce
	            },
				beforeSend: function() {
					sejoli.helper.blockUI('.sejoli-table-holder');
				},
	            success  : function(response) {
	            	sejoli.helper.unblockUI('.sejoli-table-holder');

	            	var file = new Blob([response], { type: 'application/pdf' });
					var fileURL = URL.createObjectURL(file);
					var file_path = response;
					var a = document.createElement('A');
					a.href = file_path;
					a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
					document.body.appendChild(a);
					a.click();
					document.body.removeChild(a);
	            },
	            error: function (request, status, error) {
	                // console.log(error);
	            }
	        });

	    }

        $(document).on('click', '.print-shipment-label', function(){

            let proceed  = true;
            let order_id = [];
            let status   = $(this).parent().find('select[name=update-order-select]').val();

            if('print-shipment-label' == status) {

                proceed = confirm('Anda yakin akan melakukan pencetakan label pengiriman pada order yang dipilih?');

                if(proceed) {
	                $("tbody input[type=checkbox]:checked").each(function(i, el){
	                    order_id.push($(el).data('id'));
	                });

	                if(0 < order_id.length) {
	                	sejoli_print_shipment_label(order_id);
	                } else {
	                    alert('Anda belum memilih order');
	                    return;
	                }
	            }

            }

        });

    });

})( jQuery );
