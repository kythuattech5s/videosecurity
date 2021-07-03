<h1 class="col-xs-12">Thêm câu tự luận vào cuốn sách này</h1>
  <p class="des col-xs-12">Thêm Chương và câu tự luận vào cuốn sách này</p>
  <div class="col-xs-12 ">
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách chương</p>
  		
  		<input type="hidden" name="_inject_questions" value='[]'>
  		<div class="_inject_search_questions _inject_search_questions row">
  			
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
	  				<option value="{{$chapter->id}}">{{$chapter->name}}</option>
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
  <link rel="stylesheet" type="text/css" href="public/css/jb/adminquestion.css">
  <script type="text/javascript">
   var globalQuestionSelected =[];
   var globalChapterId =0;
  </script>
  <script type="text/javascript" src="public/js/jb/adminquestion.js"></script>