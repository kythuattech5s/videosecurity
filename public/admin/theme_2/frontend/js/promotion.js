var PROMOTION = (function(){
	var selectDeal = function(){
		$('input[name="deal_type"]').change(function(event) {
			var deal_type = $(this).val();
			if (deal_type == 1 && !$('.deal-type-two').hasClass('hidden')) {
				$('.deal-type-one').removeClass('hidden');
				$('.deal-type-one input').prop('disabled', false);
				$('.deal-type-two').addClass('hidden');
				$('.deal-type-two input').prop('disabled', true);

			}
			else if(deal_type == 2 && !$('.deal-type-one').hasClass('hidden')){
				$('.deal-type-two').removeClass('hidden');
				$('.deal-type-two input').prop('disabled', false);
				$('.deal-type-one').addClass('hidden');
				$('.deal-type-one input').prop('disabled', true);
			}
		});
	}
	return {_:function(){
		selectDeal();
	},
};
})();
$(function() {
	PROMOTION._();
});