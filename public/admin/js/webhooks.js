
var STATUS = (function(){
	var webhooks = function(){
		$(document).ready(function(){
			$key = $(location).attr('pathname').split('/')[3];
			if($key == 'order_payment_ghns'){
				ajax('/esystem/update-status-ghn');
			}
			if($key == 'order_payment_ghtks'){
				ajax('/esystem/update-status-ghtk');
			}
		})
	} 

	var ajax = function($url){
		$.post($url)
		.done(function(json){
			if(json.code == 200){
				$.simplyToast(json.message,'info');
				location.reload();
			}else{
				$.simplyToast(json.message, 'success');
			}
		})
	}

	return{_:function(){
		webhooks();
	}}
})();

$(function(){
	STATUS._();
})