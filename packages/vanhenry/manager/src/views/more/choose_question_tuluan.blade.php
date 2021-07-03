<?php 
$quests = null;
$value=[];
$id = 0;
$dquestion = "";
if($actionType=='edit'||$actionType=='copy'){
	$id = FCHelper::er($dataItem,"id");
	$quests = [];
	$dquestion = \App\Question::select("id")->where("chapter_id",$id)->get()->implode('id',',');;
	$value = explode(",", $dquestion);
}
 ?>
<div class="row m0 boxedit">
  <h1 class="col-xs-12">Thêm câu tự luận vào cuốn sách này</h1>
  <p class="des col-xs-12">Thêm Chương và câu tự luận vào cuốn sách này</p>
  <div class="col-xs-12 ">
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách chương</p>
  		
  		<input type="hidden" name="_inject_{{$tableMap}}" value='[{{$dquestion}}]'>
  		<div class="_inject_search_chapters row">
  			
  			<div class="col-md-5">
  				<input type="text" placeholder="Nhập code câu hỏi" class="_inject_search_code">
  			</div>
  			<div class="col-md-5" style="padding:0">
	  			<select class="select2 _inject_search_chapter">
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
  		
  		<div class=" _inject_list_questions">
  				<ul>
  					
	  			</ul>
	  			<p class="hidden next_pagination" ></p>
  		</div>
  	</div>
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách mục lục đã thêm</p>
  		<p style="    color: #fb0000;text-align: right">Có <strong class="count_questions">{{@$quests?count($quests):0}}</strong> mục lục</p>
  		
  		<div class="row _inject_list_questions_choosen">
  				<ul>
	  			</ul>
  		</div>
  	</div>
  </div>
</div>
<style type="text/css">
	._inject_list_questions{
		    padding: 2px 5px;
    border: 1px solid #00923f;
	}
	._inject_list_questions ul,._inject_list_questions_choosen ul{
		height: 200px;
	}
	._inject_list_questions li ,
	._inject_list_questions_choosen li 
	{
		padding: 3px 0px 3px 3px;
    	background: #eee;
    	cursor: pointer;
	}
	._inject_list_questions li:nth-child(odd),
	._inject_list_questions_choosen li:nth-child(odd)
	 {
		background: #f8f8f8;
	}
	._inject_list_questions li input[type=checkbox]{
		float:left;
		margin-right: 3px;
	}
	._inject_list_questions li span {
		display: block;
	}
	._inject_search_chapters{
	    margin-bottom: 2px;
	}
	._inject_search_chapters input[type="text"]{
		height: 28px;
	}
	._inject_search_chapters button{
		height: 28px;
		width: 100%
	}
	._inject_list_questions_choosen{
		    border: 1px solid #00923f;
    margin-top: 11px;
    padding: 2px 5px;
	}
	._inject_list_questions_choosen ul{
		height: 230px;
	}
</style>
<script type="text/javascript">
	$(function() {
		var globalChaptersSelected =[{{$dquestion}}];
		function getQuestionOfChapter(id,code){
			if(id.length == 0) {
				$.simplyToast("Vui lòng chọn Chương ", 'danger');
				return;
			}
			$.ajax({
					url: '{{$admincp}}/getQuestionByChapter/'+ id,
					type: 'GET',
					dataType: 'json',
					data : {code:code}
				})
			.done(function(data) {
				var str ="";
				for (var i = 0; i < data.length; i++) {
					var item = data[i];
					str += '<li data-id="'+item.id+'" data-chapterid="'+item.chapter_id+'">\
					<input '+(globalChaptersSelected.indexOf(item.id)!=-1?'checked':'')+' type="checkbox"> <span>'+item.name+'</span>\
					</li>';
				}
				$('._inject_list_questions ul .mCSB_container').html(str);
				if(data.next_page_url!=null){
					$("._inject_list_questions .next_pagination").text(data.next_page_url);
				}
				else{
					$("._inject_list_questions .next_pagination").text("");	
				}
				@if($actionType=='edit'||$actionType=='copy')
				if(id!=0){
					$('._inject_list_questions_choosen ul .mCSB_container').html(str);
				}
				@endif
			});
			
			if(id!=0){
				$("select._inject_search_chapter").trigger('change');
			}
		}
		function getPaginationQuestion(link){
			$.ajax({
					url: link,
					type: 'GET',
					dataType: 'json'
				})
			.done(function(data) {
				var str ="";
				for (var i = 0; i < data.data.length; i++) {
					var item = data.data[i];
					str += '<li data-id="'+item.id+'" data-chapterid="'+item.chapter_id+'">\
					<input '+(globalChaptersSelected.indexOf(item.id)!=-1?'checked':'')+' type="checkbox"> <span>'+item.name+'</span>\
					</li>';
				}
				$('._inject_list_questions ul .mCSB_container').html(str);
				if(data.next_page_url!=null){
					$("._inject_list_questions .next_pagination").text(data.next_page_url);
				}
				else{
					$("._inject_list_questions .next_pagination").text("");	
				}
			});
			
			if(id!=0){
				$("select._inject_search_chapter").trigger('change');
			}
		}
		function convertToListChapters(){
			$('input[name="_inject_{{$tableMap}}"]').val(JSON.stringify(globalChaptersSelected));
			$('.count_questions').text(globalChaptersSelected.length);
		}
		function _chapter_init(){
			getQuestionOfChapter({{$id}},"");
			$('._inject_list_questions ul').mCustomScrollbar({
				callbacks:{
				    onTotalScroll: function(){
				    	var next = $("._inject_list_questions .next_pagination");
				    	var nexttext = next.text();
				    	if(nexttext.length>0){
				    		getPaginationQuestion(next.text());
				    	}
				    	next.text("");
				    }
				}
			});
			$('._inject_list_questions_choosen ul').mCustomScrollbar();
			$("._inject_search_chapters button").click(function(event) {
				getQuestionOfChapter($("select._inject_search_chapter").val(),$('._inject_search_chapters input._inject_search_code').val());
			});
			$(document).on('input', '._inject_search_chapters input._inject_search_local', function(event) {
				event.preventDefault();
				$('._inject_list_questions li').show();
				var lis = $('._inject_list_questions li');
				var text = $(this).val().toLowerCase();
				if(text.length==0){
					$('._inject_list_questions li').show();
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
			$(document).on('click', '._inject_list_questions ul li', function(event) {
				event.preventDefault();
				var checkbox = $(this).find('input[type="checkbox"]');
				var id = $(this).data("id");
				var chapter_id = $(this).data("chapterid");
				if(checkbox.is(":checked")){
					checkbox.prop('checked', false);
					$('._inject_list_questions_choosen ul .mCSB_container li[data-id="'+id+'"]').remove();
					globalChaptersSelected = globalChaptersSelected.filter(item => item !== id);
				}
				else{
					
					var _this = this;
					if(chapter_id>0 && chapter_id != {{$id}}){
						bootbox.confirm("Mục này đã gắn với 1 chương khác, bạn có muốn thực hiện thay đổi này?", function(result){ 
							if(result){
								checkbox.prop('checked', true);
								$('._inject_list_questions_choosen ul .mCSB_container').append($(_this).clone());
								globalChaptersSelected.push(id);
								convertToListChapters();
							}
						});
					}
					else{
						checkbox.prop('checked', true);
						$('._inject_list_questions_choosen ul .mCSB_container').append($(_this).clone());
						globalChaptersSelected.push(id);
					}
					
				}
				convertToListChapters();
			});
			$(document).on('click', '._inject_list_questions_choosen ul li', function(event) {
				event.preventDefault();
				var id = $(this).data("id");
				var checkbox = $('._inject_list_questions ul li[data-id="'+id+'"]').find("input[type=checkbox]");
				
				if(checkbox.is(":checked")){
					checkbox.prop('checked', false);
					
				}
				else{
					checkbox.prop('checked', true);
				}
				$(this).remove();
				globalChaptersSelected = globalChaptersSelected.filter(item => item !== id);
				convertToListChapters();
			});
		}




		_chapter_init();

		
		
	});
</script>