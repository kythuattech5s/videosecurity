@if($actionType=='edit'||$actionType=='copy')

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
<div class="row m0 boxedit _inject_box">
  <h1 class="col-xs-12">Thêm Chương vào cuốn sách này</h1>
  <div class="col-xs-12">
    <h4 class="_inject_subtitle">Chọn Chương có sẵn</h4>
    <select class="select2 _inject_choose_exist_chapter">

        <?php 
        $chapters = \App\Chapter::where("act",1)->whereRaw('(book_id = 0 or book_id is null)')->get();
              ?>
        <option value="0">Chọn chương sẽ tự động thêm vào sách</option>
        @foreach($chapters as $chapter)
          <option value="{{$chapter->id}}">{{$chapter->name}}</option>
        @endforeach
    </select>

  </div>	
  <div class="col-xs-12">
    <h4 class="_inject_subtitle">Hoặc tạo mới</h4>
    <div class="form-group col-md-5">
      <label for="name_chapter">Tên Chương:</label>
      <input type="text" class="form-control" id="name_chapter">
    </div>
    <div class="form-group col-md-5">
      <label for="ord_chapter">Thứ tự:</label>
      <input type="text" class="form-control" id="ord_chapter">
    </div>
    <div class=" col-md-2">
      <label for="name_chapter"></label>
      <button class="_inject_add_chapter" type="button">Thêm Chương</button>
    </div>
    
  </div>
  <?php $chapters =  \App\Chapter::where("book_id",$id)->get() ;
  $groupQuestions = \App\QuestCategory::where("act",1)->get();
  ?>
  @foreach($chapters as $chapter)
    @include("vh::more.jb.ajax_chapter_item")
  @endforeach





</div>

<style type="text/css">
  ._inject_add_chapter{
        margin-top: 3px;
    height: 34px;
    padding: 5px;
    background: #00923f;
    color: #fff;
    text-transform: uppercase;
  }
  ._inject_list_question,
  ._inject_list_question_choosen,
    ._inject_list_exam,
  ._inject_list_exam_choosen{
    min-height: 100px;
    border: 1px solid #00923f;
    margin-top: 3px;
  }
  ._inject_list_question_choosen{
        margin-top: 7px;
  }
  ._inject_list_question ul,
  ._inject_list_question_choosen ul,
  ._inject_list_exam ul,
  ._inject_list_exam_choosen ul
  {
    height: 200px;
  }
  ._inject_list_exam_choosen ul,
  ._inject_list_question_choosen ul{
    height: 230px;
  }
  ._inject_list_question li ,
  ._inject_list_question_choosen li,
    ._inject_list_exam li ,
  ._inject_list_exam_choosen li 
  {
    padding: 3px 0px 3px 3px;
      background: #eee;
      cursor: pointer;
  }
  ._inject_list_question li:nth-child(odd),
  ._inject_list_question_choosen li:nth-child(odd),
    ._inject_list_exam li:nth-child(odd),
  ._inject_list_exam_choosen li:nth-child(odd)
   {
    background: #f8f8f8;
  }
  ._inject_list_question span.checkall,
  ._inject_list_question_choosen span.removeall,
  ._inject_list_exam span.checkall,
  ._inject_list_exam_choosen span.removeall{
        background: #00923f;
    display: block;
    text-align: center;
    text-transform: uppercase;
    color: #fff;
    padding: 3px 0px;
    cursor: pointer;
  }
  ._inject_list_exam li input[type=checkbox],
  ._inject_list_question li input[type=checkbox]{
    float:left;
    margin-right: 3px;
  }
  ._inject_chapter_item h4._inject_question_title_chapter {
    display: block;
    background: #33323a;
    padding: 8px;
    text-transform: uppercase;
    color: #fff;
    margin-bottom: 3px;
    position: relative;
  }
   ._inject_chapter_item h4._inject_question_title_chapter span.expand{
        display: block;
    float: right;
    background: #00923f;
    padding: 5px;
    position: absolute;
    right: 0px;
    top: 0;
    cursor: pointer;
    font-size: 25px;
   } 
     ._inject_chapter_item h4._inject_question_title_chapter span.remove{
        display: block;
    float: right;
    background: #00923f;
    padding: 5px;
    position: absolute;
    right: 40px;
    top: 0;
    cursor: pointer;
    font-size: 25px;
   }

  ._inject_list_question li span {
    display: block;
  }
    ._inject_list_question_choosen li:after,
    ._inject_list_exam_choosen li:after{
      display: block;
      content: "";
      clear: both;
    }
  ._inject_list_question_choosen li input[type=checkbox] ,
  ._inject_list_exam_choosen li input[type=checkbox] {
    float: left;
  }
  ._inject_list_exam_choosen li span.main ,
  ._inject_list_question_choosen li span.main {
    width: 78%;
    display: block;
    float: left;
  }
  span.action {
    display: inline-block;
    padding: 3px;
    border: 1px solid #00923f;
    color: #00923f;
    float: right;
    margin: 0px 1px;
  }
  span.action.active{
    background: #00923f;
    color: #fff;
  }
  ._inject_box input.ord{
        width: 25px;
    float: right;
    height: 23px;
    font-size: 11px;
  }
  ._inject_search_name_question ,
  ._inject_search_name_exam {
    margin-bottom: 3px;
  }
</style>
<script type="text/javascript">
  var actionType = '{{$actionType}}';
  var globalBookId = '{{$id}}';
  var globalChaptersSelected = [];
</script>
<script type="text/javascript" src="public/js/jb/choose_chapter_new.js"></script>
<script type="text/javascript">
  
</script>

@endif