var PROMOTION = (function(){
	var selectDeal = function(){
		$('input[name="type"]').change(function(event) {
			var type = $(this).val();
			if (type == 1 && !$('.deal-type-two').hasClass('hidden')) {
				$('.deal-type-one').removeClass('hidden');
				$('.deal-type-one input').prop('disabled', false);
				$('.deal-type-two').addClass('hidden');
				$('.deal-type-two input').prop('disabled', true);

			}
			else if(type == 2 && !$('.deal-type-one').hasClass('hidden')){
				$('.deal-type-two').removeClass('hidden');
				$('.deal-type-two input').prop('disabled', false);
				$('.deal-type-one').addClass('hidden');
				$('.deal-type-one input').prop('disabled', true);
			}
		});
	}
	var datetimePicker = function(){
        jQuery.datetimepicker.setLocale('vi');
        $('#deal-start-picker').datetimepicker({ 
            format: 'd-m-Y H:i:s',
            step:5,
            minDate: new Date(),
            minTime: moment().add(1, 'hours'),
        });
    
        $('#deal-end-picker').datetimepicker({ 
            format: 'd-m-Y H:i:s',
            step:5,
            minDate: new Date(),
            minTime: moment().add(2, 'hours'),
        });
    }
    var other = function(){
    	$(document).on('click', '.close-modal-sale', function(event) {
    		event.preventDefault();
    		$(this).closest('.deal-product-main').modal('hide');
    	});
    }
    var dealMainHandler = function(){
    	$(document).on('change', '.deal_choose_all_product', function(event) {
    		event.preventDefault();
    		if ($(this).is(':checked')) {
    			$('.deal_choose_single_product').prop('checked', true);	
    		}
    		else{
    			$('.deal_choose_single_product').prop('checked', false);		
    		}
    		var countChosen = dealProductMainChosen();
    		$('.count-deal-product-chosen-main').text(countChosen.length);
    	});	
    	$(document).on('change', '.deal_choose_single_product', function(event) {
    		event.preventDefault();
    		var countChosen = dealProductMainChosen();
    		if (countChosen == 0) {
    			$('.deal_choose_all_product').prop('checked', false);
    		}
    		$('.count-deal-product-chosen-main').text(countChosen.length);
    	});
    	$(document).on('click', '.action-product-chosen-deal button', function(event) {
    		event.preventDefault();
    		var action = $(this).attr('data-action');
    		var products = dealProductMainChosen();
    		var deal_id = $(this).parent().find('input[name="deal_id"]').val();
    		ajaxDealProductMainAction(deal_id, action, products);
    	});
    	function ajaxDealProductMainAction(deal_id, action, products) {
    		$.ajax({
    			url: 'esystem/deal-product-main-action/'+deal_id,
    			type: 'post',
    			dataType: 'json',
    			data: {action: action, products: products},
    			global: false,
    		})
    		.done(function(json) {
    			if (json.code == 200) {
					$.simplyToast(json.message, 'success');
    				if (json.handler == 'reload') {
    					window.location.reload();
    				}
    			}
    			else{
    				$.simplyToast(json.message, 'danger');
    			}
    		})
    	}
    	function dealProductMainChosen() {
    		var productChosenIds = [];
    		$('.deal_choose_single_product:checked').each(function(index, el) {
    			productChosenIds.push($(el).val());
    		});
    		return productChosenIds;
    	}
    	$(document).on('change', '.toggle-action-deal-product-main', function(event) {
    		event.preventDefault();
    		var product = $(this).closest('tr').attr('data-id');
    		var deal_id = $(this).closest('tr').attr('deal-id');
    		if($(this).is(':checked')){
    			var action = 2;
    		}
    		else var action = 1;
    		ajaxDealProductMainAction(deal_id, action, product);
    	});
    	$(document).on('click', '.deal_remove_product_main', function(event) {
    		event.preventDefault();
    		var deal_id = $(this).closest('tr').attr('deal-id');
    		var product = $(this).closest('tr').attr('data-id');
    		var action = 3;
    		ajaxDealProductMainAction(deal_id, action, product);
    	});
    	$(document).on('change', '.toggle-action-deal-product-sub', function(event) {
    		event.preventDefault();
    		var product = $(this).closest('tr').attr('data-id');
    		var price = $(this).closest('tr').find('input[name="price_deal_sub"]').val();
    		var deal_id = $(this).closest('tr').attr('deal-id');
    		if($(this).is(':checked')){
    			var action = 2;
    		}
    		else var action = 1;
    		ajaxDealProductSubAction($(this), deal_id, action, product, price);
    	});
    	$(document).on('click', '.deal_remove_product_sub', function(event) {
    		event.preventDefault();
    		var deal_id = $(this).closest('tr').attr('deal-id');
    		var product = $(this).closest('tr').attr('data-id');
    		var action = 3;
    		ajaxDealProductSubAction($(this), deal_id, action, product);
    	});
    	function ajaxDealProductSubAction(_this, deal_id, action, products, price = null) {
    		$.ajax({
    			url: 'esystem/deal-product-sub-action/'+deal_id,
    			type: 'post',
    			dataType: 'json',
    			data: {action: action, products: products, price: price},
    			global: false,
    		})
    		.done(function(json) {
    			if (json.code == 200) {
                    $.simplyToast(json.message, 'success');
    				if (json.handler == 'reload') {
    					window.location.reload();
    				}
    			}
    			else if(json.code == 102){
    				_this.prop('checked', false);
    				$.simplyToast(json.message, 'danger');
    			}
    			else{
    				$.simplyToast(json.message, 'danger');
    			}
    		})
    	}
    	$(document).on('click', '.deal-product-sub-save', function(event) {
    		event.preventDefault();
    		var deal_id = $(this).attr('deal-id');
    		var inputPrices = $('input[name="price_deal_sub"]');
    		var prices = {};
    		inputPrices.each(function(index, el) {
    			var product = $(el).closest('tr').attr('data-id');
    			prices[product] = $(el).val();
    		});
    		$.ajax({
    			url: 'esystem/update-price-deal-sub/'+deal_id,
    			type: 'post',
    			dataType: 'json',
    			data: {prices: prices},
    			global: false,
    		})
    		.done(function(json) {
    			if (json.code == 200) {
    				$.simplyToast(json.message, 'success');
    			}
    			else{
    				$.simplyToast(json.message, 'danger');
    			}
    		})
    	});
    }
	return {_:function(){
		selectDeal();
		datetimePicker();
		other();
		dealMainHandler();
	},
};
})();
$(function() {
	PROMOTION._();
});