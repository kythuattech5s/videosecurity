$(function() {
	var baseurl = $('base').attr("href");
	var admincp = $('meta[name=admincp]').attr("content");
	$('textarea.editor').tinymce({
		height: $(this).attr('dt-height'),
		theme: 'modern',
		plugins: [
		'advlist autolink lists link image charmap print preview hr anchor pagebreak',
		'searchreplace wordcount visualblocks visualchars code fullscreen',
		'insertdatetime media nonbreaking save table contextmenu directionality',
		'emoticons template paste textcolor colorpicker textpattern tech5sfilemanager'
		],
		toolbar1:"code preview |bold italic underline hr strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat subscript superscript",
		toolbar2:"styleselect formatselect fontselect fontsizeselect | link unlink table | forecolor backcolor | pastetext pagebreak | spellchecker | tech5sfilemanager | image media ",
		toolbar3:"",
		style_formats: [
	    {title: 'Bold text', inline: 'b'},
	    {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
	    {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
	    {title: 'Example 1', inline: 'span', classes: 'example1'},
	    {title: 'Example 2', inline: 'span', classes: 'example2'},
	    {title: 'Table styles'},
	    {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
	    ],
	    fontsize_formats: '10px 11px 12px 13px 14px 16px 18px 20px 22px 24px 30px 36px',
	    lineheight_formats: "10px 11px 12px 13px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 38px 40px 42px 46px 50px",
	    /*content_css: ['theme/frontend/css/font-face.css'],
	    font_formats: 'Andale Mono=andale mono,monospace;'+
	    'Arial=arial,helvetica,sans-serif;'+
	    'Arial Black=arial black,avant garde;'+
	    'Book Antiqua=book antiqua,palatino;'+
	    'Comic Sans MS=comic sans ms,sans-serif;'+
	    'Courier New=courier new,courier;'+
	    'Georgia=georgia,palatino;'+
	    'Helvetica=helvetica;'+
	    'Impact=impact,chicago;'+
	    'Symbol=symbol;'+
	    'Tahoma=tahoma,arial,helvetica,sans-serif;'+
	    'Terminal=terminal,monaco;'+
	    'Times New Roman=times new roman,times;'+
	    'Trebuchet MS=trebuchet ms,geneva;'+
	    'Verdana=verdana,geneva;'+
	    'Webdings=webdings;'+
	    'Wingdings=wingdings,zapf dingbats;'+
	    
	    'OpenSans-Regular=OpenSans-Regular;'+
	    'OpenSans-Semibold=OpenSans-Semibold',*/
	    inline_styles : true,
	    /* extended_valid_elements: '#p', Nếu thẻ p empty nó sẽ thêm &nbsp; giúp việc enter xuống dòng có tác dụng */
	    entity_encoding : "raw",
		document_base_url:baseurl,
		image_advtab: true,
		init_instance_callback:myCustomInitInstance,
		external_filemanager_path:baseurl+admincp+'/media/view',
		filemanager_title:"Tech5s File Manager" ,
		external_plugins: { "filemanager" : baseurl+"admin/plug/tinymce/plugin.min.js"},
		file_browser_callback: function(field_name, url, type, win) {
			func_value = win.document.getElementById(field_name).value;
			fixTinymceFileManager(field_name, func_value, type, win); 
		}
	});
	function fixTinymceFileManager(t, i, a, s) {
		e=tinymce.activeEditor;
		var r = $(window).innerWidth() - 30,
		g = $(window).innerHeight() - 60;
		if (r > 1800 && (r = 1800), g > 1200 && (g = 1200), r > 600) {
			var d = (r - 20) % 138;
			r = r - d + 10
		}
		urltype = 2, "image" == a && (urltype = 1), "media" == a && (urltype = 3);
		var o = "Tech5s FileManager";
		"undefined" != typeof e.settings.filemanager_title && e.settings.filemanager_title && (o = e.settings.filemanager_title);
		var l = "key";
		"undefined" != typeof e.settings.filemanager_sort_by && e.settings.filemanager_sort_by && (f = "&sort_by=" + e.settings.filemanager_sort_by);
		var m = "false";
		"undefined" != typeof e.settings.filemanager_descending && e.settings.filemanager_descending && (m = e.settings.filemanager_descending);
		var v = "";
		"undefined" != typeof e.settings.filemanager_crossdomain && e.settings.filemanager_crossdomain && (v = "&crossdomain=1", window.addEventListener ? window.addEventListener("message", n, !1) : window.attachEvent("onmessage", n)), tinymce.activeEditor.windowManager.open({
			title: o,
			file: e.settings.external_filemanager_path+"?istiny=2",
			width: r,
			height: g,
			resizable: !0,
			maximizable: !0,
			inline: 1
		}, {
			setUrl: function(n) {
				var i = s.document.getElementById(t);
				if (i.value = e.convertURL(n), "createEvent" in document) {
					var a = document.createEvent("HTMLEvents");
					a.initEvent("change", !1, !0), i.dispatchEvent(a)
				} else i.fireEvent("onchange");
				tinymce.activeEditor.windowManager.close();
			}
		})
	}
	function myCustomInitInstance(ins){
		var height = $('textarea#'+ins.id).attr('dt-height');
		height = (height==undefined|| height=="")?'400px':height;
		$('#'+ins.id+"_ifr").height(height);
	}
});
