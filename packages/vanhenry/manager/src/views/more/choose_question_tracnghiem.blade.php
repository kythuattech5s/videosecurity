<?php 
$quests = null;
$value=[];
$id = 0;
$dquestion = "";
if($actionType=='edit'||$actionType=='copy'){
	$id = FCHelper::er($dataItem,"id");
	$quests = [];
	$dquestion = \App\Exam::select("id")->where("chapter_id",$id)->get()->implode('id',',');;
	$value = explode(",", $dquestion);
}
 ?>
<div class="row m0 boxedit">
  <h1 class="col-xs-12">Thêm câu trắc nghiệm vào chương này</h1>
  <p class="des col-xs-12">Thêm Chương và câu trắc nghiệm vào chương này</p>
  <div class="col-xs-12 ">
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách chương</p>
  		
  		<input type="hidden" name="_inject_{{$tableMap}}" value='[{{$dquestion}}]'>
  		<div class="_inject_search_chapters_test row">
  			
  			<div class="col-md-5">
  				<input type="text" placeholder="Nhập code câu hỏi trắc nghiệm" class="_inject_search_code_test">
  			</div>
  			<div class="col-md-5" style="padding:0">
	  			<select class="select2 _inject_search_chapter_test">
	  				<option value="">Lựa chọn</option>
	  				<option value="0">Mục chưa thuộc chương nào</option>
	  				<?php $chapters = \App\Chapter::where("act",1)->get(); 
  					?>
	  				@foreach($chapters as $chapter)
	  				<option {{$chapter->id==$id?'selected':''}} value="{{$chapter->id}}">{{$chapter->name}}</option>
	  				@endforeach
	  			</select>
  			</div>
  			<div class="col-md-2">
  				<button type="button">Lọc</button>
  			</div>
  			<div class="class col-md-12">
  				<input style="width: 100%;margin-top: 3px;" placeholder="Nhập tên của câu hỏi" type="text" class="_inject_search_local">
  			</div>
  		</div>
  		
  		<div class=" _inject_list_exams_test">
  				<ul>
  					
	  			</ul>
	  			<p class="hidden next_pagination" ></p>
  		</div>
  	</div>
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách mục lục đã thêm</p>
  		<p style="    color: #fb0000;text-align: right">Có <strong class="count_exams">{{@$quests?count($quests):0}}</strong> mục lục</p>
  		
  		<div class="row _inject_list_exams_test_choosen">
  				<ul>
	  			</ul>
  		</div>
  	</div>
  </div>
</div>
<style type="text/css">
	._inject_list_exams_test{
		    padding: 2px 5px;
    border: 1px solid #00923f;
	}
	._inject_list_exams_test ul,._inject_list_exams_test_choosen ul{
		height: 200px;
	}
	._inject_list_exams_test li ,
	._inject_list_exams_test_choosen li 
	{
		padding: 3px 0px 3px 3px;
    	background: #eee;
    	cursor: pointer;
	}
	._inject_list_exams_test li:nth-child(odd),
	._inject_list_exams_test_choosen li:nth-child(odd)
	 {
		background: #f8f8f8;
	}
	._inject_list_exams_test li input[type=checkbox]{
		float:left;
		margin-right: 3px;
	}
	._inject_list_exams_test li span {
		display: block;
	}
	._inject_search_chapters_test{
	    margin-bottom: 2px;
	}
	._inject_search_chapters_test input[type="text"]{
		height: 28px;
	}
	._inject_search_chapters_test button{
		height: 28px;
		width: 100%
	}
	._inject_list_exams_test_choosen{
		    border: 1px solid #00923f;
    margin-top: 11px;
    padding: 2px 5px;
	}
	._inject_list_exams_test_choosen ul{
		height: 230px;
	}
</style>
<script type="text/javascript">
	$(function() {
		var globalChaptersSelected =[{{$dquestion}}];
		function getExamOfChapter(id,code){
			if(id.length == 0) {
				$.simplyToast("Vui lòng chọn Chương ", 'danger');
				return;
			}
			$.ajax({
					url: '{{$admincp}}/getExamByChapter/'+ id,
					type: 'GET',
					dataType: 'json',
					data : {code:code}
				})
			.done(function(data) {
				var str ="";
				for (var i = 0; i < data.data.length; i++) {
					var item = data.data[i];
					var name = "<div>"+item.name+"</div>";
					str += '<li data-id="'+item.id+'" data-chapterid="'+item.chapter_id+'">\
					<input '+(globalChaptersSelected.indexOf(item.id)!=-1?'checked':'')+' type="checkbox"> <span>'+$(name).text()+'</span>\
					</li>';
				}
				$('._inject_list_exams_test ul .mCSB_container').html(str);
				if(data.next_page_url!=null){
					$("._inject_list_exams_test .next_pagination").text(data.next_page_url);
				}
				else{
					$("._inject_list_exams_test .next_pagination").text("");	
				}
				@if($actionType=='edit'||$actionType=='copy')
				if(id!=0){
					$('._inject_list_exams_test_choosen ul .mCSB_container').html(str);
				}
				@endif
			});
			
			if(id!=0){
				$("select._inject_search_chapter_test").trigger('change');
			}
		}
		function getPaginateExams(link){

			$.ajax({
					url: link,
					type: 'GET',
					dataType: 'json'
				})
			.done(function(data) {
				var str ="";
				for (var i = 0; i < data.data.length; i++) {
					var item = data.data[i];
					var name = "<div>"+item.name+"</div>";
					str += '<li data-id="'+item.id+'" data-chapterid="'+item.chapter_id+'">\
					<input '+(globalChaptersSelected.indexOf(item.id)!=-1?'checked':'')+' type="checkbox"> <span>'+$(name).text()+'</span>\
					</li>';
				}
				$('._inject_list_exams_test ul .mCSB_container').append(str);
				if(data.next_page_url!=null){
					$("._inject_list_exams_test .next_pagination").text(data.next_page_url);
				}
				else{
					$("._inject_list_exams_test .next_pagination").text("");	
				}
			});
		}
		function convertExamToListChapters(){
			$('input[name="_inject_{{$tableMap}}"]').val(JSON.stringify(globalChaptersSelected));
			$('.count_exams').text(globalChaptersSelected.length);
		}
		function _chapter_exam_init(){
			getExamOfChapter({{$id}},"");
			$('._inject_list_exams_test ul').mCustomScrollbar({
				callbacks:{
				    onTotalScroll: function(){
				    	var next = $("._inject_list_exams_test .next_pagination");
				    	var nexttext = next.text();
				    	if(nexttext.length>0){
				    		getPaginateExams(next.text());
				    	}
				    	next.text("");
				    }
				}
			});
			$('._inject_list_exams_test_choosen ul').mCustomScrollbar();
			$("._inject_search_chapters_test button").click(function(event) {
				getExamOfChapter($("select._inject_search_chapter_test").val(),$('._inject_search_chapters_test input._inject_search_code_test').val());
			});
			$(document).on('input', '._inject_search_chapters_test input._inject_search_local', function(event) {
				event.preventDefault();
				$('._inject_list_exams_test li').show();
				var lis = $('._inject_list_exams_test li');
				var text = $(this).val().toLowerCase();
				if(text.length==0){
					$('._inject_list_exams_test li').show();
				}
				else{
					for (var i = 0; i < lis.length; i++) {
						var item = $(lis[i]);
						if(item.text().toLowerCase().indexOf(text)==-1){
							item.hide();
						}
					}
				}
			});
			$(document).on('click', '._inject_list_exams_test ul li', function(event) {
				event.preventDefault();
				var checkbox = $(this).find('input[type="checkbox"]');
				var id = $(this).data("id");
				var chapter_id = $(this).data("chapterid");
				if(checkbox.is(":checked")){
					checkbox.prop('checked', false);
					$('._inject_list_exams_test_choosen ul .mCSB_container li[data-id="'+id+'"]').remove();
					globalChaptersSelected = globalChaptersSelected.filter(item => item !== id);
				}
				else{
					
					var _this = this;
					if(chapter_id>0 && chapter_id != {{$id}}){
						bootbox.confirm("Mục này đã gắn với 1 chương khác, bạn có muốn thực hiện thay đổi này?", function(result){ 
							if(result){
								checkbox.prop('checked', true);
								$('._inject_list_exams_test_choosen ul .mCSB_container').append($(_this).clone());
								globalChaptersSelected.push(id);
								convertExamToListChapters();
							}
						});
					}
					else{
						checkbox.prop('checked', true);
						$('._inject_list_exams_test_choosen ul .mCSB_container').append($(_this).clone());
						globalChaptersSelected.push(id);
					}
					
				}
				convertExamToListChapters();
			});
			$(document).on('click', '._inject_list_exams_test_choosen ul li', function(event) {
				event.preventDefault();
				var id = $(this).data("id");
				var checkbox = $('._inject_list_exams_test ul li[data-id="'+id+'"]').find("input[type=checkbox]");
				
				if(checkbox.is(":checked")){
					checkbox.prop('checked', false);
					
				}
				else{
					checkbox.prop('checked', true);
				}
				$(this).remove();
				globalChaptersSelected = globalChaptersSelected.filter(item => item !== id);
				convertExamToListChapters();
			});
		}




		_chapter_exam_init();

		
		
	});
</script>