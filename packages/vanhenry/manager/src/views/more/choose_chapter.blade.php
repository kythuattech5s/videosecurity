<?php 
$quests = null;
$value=[];
$id=0;
if($actionType=='edit'||$actionType=='copy'){
	$id = FCHelper::er($dataItem,"id");
	$quests = [];
	$dchapter = \App\Chapter::select("id")->where("book_id",$id)->get()->implode('id',',');;
	$value = explode(",", $dchapter);
}
 ?>
<div class="row m0 boxedit">
  <h1 class="col-xs-12">Thêm mục lục vào cuốn sách này</h1>
  <p class="des col-xs-12">Thêm Chương và mục lục vào cuốn sách này</p>
  <div class="col-xs-12 ">
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách chương</p>
  		
  		<input type="hidden" name="_inject_{{$tableMap}}" value='[{{$dchapter}}]'>
  		<div class="_inject_search_chapters">
  			<input type="text" class="_inject_search_chapter">
  			<button type="button">Lọc</button>
  		</div>
  		
  		<div class=" _inject_list_chapters">
  				<ul>
  					<?php $chapters = \App\Chapter::where("act",1)->get(); 
  					//->whereRaw('book_id = 0 or book_id is null')
  					?>
  					@foreach($chapters as $chapter)
					<li data-id="{{$chapter->id}}" data-bookid={{(int)$chapter->book_id}}>
						<input {{in_array($chapter->id,$value)?'checked':''}}  type="checkbox"> <span class="main">{{$chapter->name}}</span>
					</li>
					@endforeach
	  			</ul>
  		</div>
  	</div>
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách mục lục đã thêm</p>
  		<p style="    color: #fb0000;text-align: right">Có <strong class="count_questions">{{@$quests?count($quests):0}}</strong> mục lục</p>
  		
  		<div class="row _inject_list_chapters_choosen">
  			<?php $chapters = $chapters->filter(function ($v, $k) use($value) {
			    return in_array($v->id, $value);
			}); ?>
  				<ul>
  					@foreach($chapters as $chapter)
					<li data-id="{{$chapter->id}}" data-bookid={{(int)$chapter->book_id}}>
						<input checked  type="checkbox"> <span class="main">{{$chapter->name}}</span>
						<span title="Lên" class="fa fa-arrow-up action"></span>
						<span title="Xuống" class="fa fa-arrow-down action"></span>
						<span title="Xem chi tiết" class="fa fa-plus action"></span>
					</li>
					@endforeach
	  			</ul>
  			</div>
  		</div>
  	</div>
  	<div class="boxedit row boxajaxquestion" style="margin:0">
  		
  	</div>
	





</div>
<link rel="stylesheet" type="text/css" href="public/css/jb/adminbook.css">
<script type="text/javascript">
	var globalChaptersSelected =[{{$dchapter}}];
  var globalBookId =[{{$id}}];
	var globalactionType ='{{$actionType}}';
</script>
<script type="text/javascript" src="public/js/jb/adminbook.js"></script>