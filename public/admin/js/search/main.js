$(function() {

	$('.ajxform_simple').submit(function(event) {
		event.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
		})
		.done(function(data) {
			$('#main-table').html(data);
			$('input.ccb[type=checkbox]').checkboxpicker();
		})
		.fail(function() {
		})
		.always(function() {
		});
	});
	$(document).on('click', 'ul.pagination > a', function(event) {
		var href= $(this).attr('href');
		if(href.indexOf('/search/')!=-1){
			$.ajax({
				url: href,
				type: 'GET',
			})
			.done(function(data) {
				$('#main-table').html(data);
				$('input.ccb[type=checkbox]').checkboxpicker();
			})
			.fail(function() {
			})
			.always(function() {
			});
			
			return false;
		}
		return true;

	});

	$(document).on('click', 'a.xtbdhttgn', function(event) {
		$('form#frmsearch').attr('action',$(this).attr('data-href'));
		if($('input[name="raw_name"]').length>0) {
			$('input[name="raw_name"]').attr('name','raw_create_time');
		}
		$('input[name="raw_create_time"]').val($(this).attr('rel'));
		$('form#frmsearch').submit();
	});

	$(document).on('click', 'button.btnexcel', function(event) {
		// event.preventDefault();
		// if($('table#minhpro').length>0) {
  //           $("#minhpro").excelexportjs({
  //               containerid: "minhpro"
  //              , datatype: 'table'
  //           });
  //       }
  
		$.ajax({
			url: $(this).attr('rel'),
			type: 'POST',
			data: $('form#frmsearch').serialize(),
		})
		.done(function(data) {
			$('button.btnexcel').before('<a href="/MyExcel.xls" style="position: absolute; right: 400px; top: 18px;">Download file</a>');
		})
		.fail(function() {
			console.log('Loi');
			$('button.btnexcel').before('<a href="/MyExcel.xls" style="position: absolute; right: 400px; top: 18px;">Download file</a>');
		})
		.always(function() {
			location.redirect='/esystem/view/orders';
		});
	});
});
function submitForm(frm){
	var options = { 
		type:      'POST',
		beforeSend: function() {
		},
		uploadProgress: function(event, position, total, percentComplete) {
		},
		success: function(e) {
		},
		complete: function(xhr) {

		}
	}; 
	$(frm).ajaxForm(options); 
	$(frm).submit();
}