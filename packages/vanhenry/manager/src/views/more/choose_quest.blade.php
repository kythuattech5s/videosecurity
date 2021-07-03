<?php 
$quests = null;
if($actionType=='edit'||$actionType=='copy'){
	$id = FCHelper::er($dataItem,"id");
	$quests = \App\ExamQuestion::select("id","code","name","id_quest")->where("id_exam",$id)->get();
}
 ?>
<div class="row m0 boxedit">
  <h1 class="col-xs-12">Thêm câu hỏi từ ngân hàng đề thi</h1>
  <p class="des col-xs-12">Lựa chọn trong các câu hỏi trong ngân hàng đề thêm vào bộ đề thi này</p>
  <div class="col-xs-12 ">
  	<div class="col-md-6 col-xs-12">
  		<p>Tìm kiếm đề từ ngân hàng câu hỏi</p>
  		<?php 
  		$x = array();
  		if($quests!=null){
  			foreach ($quests as $k => $v) {
	  			array_push($x, $v->id_quest);
	  		}
  		}
  		
  		 ?>
  		<input type="hidden" name="_inject_{{$tableMap}}" value='{!!json_encode($x)!!}'>
  		<div class="row">
  			<div class="col-xs-5" style="padding-right: 0;">
  			<select style="width:auto !important" class="_inject_select2"  id="">
  				<?php $qcates = \App\QuestCategory::where("act",1)->where("parent",0)->get() ?>
  				<option value="0">Chọn danh mục câu hỏi</option>
  				@foreach($qcates as $qc)
  					<option value={{$qc->id}}>{{$qc->name}}</option>
  					<?php $qcates2 = \App\QuestCategory::where("act",1)->where("parent",$qc->id)->get() ?>
  					@foreach($qcates2 as $qc1)
  						<option value={{$qc1->id}}>--{{$qc1->name}}</option>
  					@endforeach
  				@endforeach
  			</select>
  			</div>
  			<div class="col-xs-5" style="padding-right: 0;">
  			<input class="_inject_input_questions" type="text"  placeholder="Tên câu hỏi">
  			</div>
  			<div class="col-xs-2">
  				<button class="_inject_search" type="button"><i class="fa fa-search"></i></button>
  			</div>
  		</div>
  		<div class="row _inject_list_questions">
  			<div class="col-xs-12">
  				<ul>

	  			</ul>
  			</div>
  		</div>
  	</div>
  	<div class="col-md-6 col-xs-12">
  		<p>Danh sách câu hỏi đã thêm</p>
  		<p style="    color: #fb0000;text-align: right">Có <strong class="count_questions">{{@$quests?count($quests):0}}</strong> câu hỏi</p>
  		
  		<div class="row _inject_list_questions_choosen">
  			<div class="col-xs-12">
  				<ul>
  					<?php $questid = array(); ?>
					@if($quests!=null)
						@foreach($quests as $quest)
						<?php array_push($questid, $quest->id_quest); ?>
							<li data-id="{{$quest->id_quest}}" class="_inject_item_questions">
			  					<i class="fa fa-trash _inject_remove_choosen"></i>
			  					<span class="code">Code: {{$quest->code}}</span>
			  					<span class="line"></span>
								<p class="name">{{strip_tags($quest->name)}}</p>
			  				</li>
						@endforeach
					@endif
	  			</ul>
  			</div>
  		</div>
  	</div>
  </div>
</div>

<script type="text/javascript">
	$(function() {
		var QUEST_CHOOSEN = [<?php for($i=0;$i<count($questid);$i++){
			echo '"'.$questid[$i].'"';
			if($i<count($questid)-1){
			 echo ",";
			}
		}
		?>];
		$("._inject_select2").select2();
		$('._inject_list_questions ul').mCustomScrollbar();
		$('._inject_list_questions_choosen ul').mCustomScrollbar();
		$('._inject_select2').change(function(event) {
			
			searchJBQuestions($(this).val(),$('input._inject_input_questions').val());
		});
		$("._inject_search").click(function(event) {
			searchJBQuestions($('select._inject_select2').val(),$('input._inject_input_questions').val());
		});
		$(document).on('click', '._inject_list_questions ._inject_item_questions input[type=checkbox]', function(event) {
			chooseQuestionToExams(this);
		});
		$(document).on('click', 'i._inject_remove_choosen', function(event) {
			var p = $(this).parent();
			var id = p.attr("data-id");

			var index = QUEST_CHOOSEN.indexOf(id);
			if (index > -1) {
				$("._inject_list_questions_choosen .mCSB_container li[data-id="+id+"]").remove();
				$("._inject_list_questions li[data-id="+id+"]").find("input[type=checkbox]").prop("checked",false);
			    QUEST_CHOOSEN.splice(index, 1);
			}
			fillDataToSend();
		});

		function searchJBQuestions(cate,name){
			$.ajax({
				url: '{{$admincp}}/jbsearch/{{$tableMap}}',
				type: 'POST',
				dataType: 'json',
				data: {cate:cate ,name:name},
			})
			.done(function(json) {
				var str="";
				for (var i = 0; i < json.length; i++) {
					var item = json[i];
					str += '<li data-id="'+item.id+'" class="_inject_item_questions">\
	  					<label  class="_inject_icheck smooth noselect">\
			            <input data-id="'+item.id+'" type="checkbox"><i symbol="✔"></i> Thêm \
			           </label>\
	  					<span class="code">Code: '+item.code+'</span>\
	  					<span class="line"></span>\
						<p class="name">'+$('<div>' + item.name + '</div>').text()+'</p>\
	  				</li>';
				}
				$("._inject_list_questions #mCSB_1_container").html(str);
			});
		}
		function chooseQuestionToExams(_this){
			var id = $(_this).attr("data-id");
			
			if($.inArray(id, QUEST_CHOOSEN)>=0){
				$("._inject_list_questions_choosen .mCSB_container li[data-id="+id+"]").remove();
				var index = QUEST_CHOOSEN.indexOf(id);
				if (index > -1) {
				    QUEST_CHOOSEN.splice(index, 1);
				}
			}
			else{
				var html = $(_this).parents("li._inject_item_questions").clone();
				html.find("label").remove();
				html.prepend('<i class="fa fa-trash _inject_remove_choosen"></i>');
				$("._inject_list_questions_choosen .mCSB_container").append(html);	
				QUEST_CHOOSEN.push(id);
			}
			fillDataToSend();
			
		}
		function fillDataToSend(){
			@if($actionType!='edit'&&$actionType!='copy')
			$('input[name=time]').val(QUEST_CHOOSEN.length*90);
			$('input[name=score]').val(1);
			
			@endif
			$('input[name=num_question]').val(QUEST_CHOOSEN.length);
			$(".count_questions").text(QUEST_CHOOSEN.length);
			$("input[name=_inject_{{$tableMap}}]").val(JSON.stringify(QUEST_CHOOSEN));
		}
	});
</script>
<style type="text/css">
	._inject_input_questions{
	    height: 28px;
	    width: 100%;
	    border: 1px solid #00923f;
	}
	input::-webkit-input-placeholder{
		font-size: 12px;
		font-style: italic;
	}
	button._inject_search{
		background: #00923f;
	    color: #fff;
	    padding: 2px 10px;
	    width: 100%;
	}
	i._inject_remove_choosen{
		color: #ce0808;
	    font-size: 18px;
	    background: #ccc;
	    padding: 2px 20px;
	    cursor: pointer;
	}
	._inject_list_questions_choosen{
		margin-top:8px;
	}
	._inject_list_questions ul,._inject_list_questions_choosen ul{
		max-height: 500px;
		overflow: hidden;
	}
	._inject_list_questions ul li,
	._inject_list_questions_choosen ul li{
	    padding: 5px;
    	border: 1px solid #ccc;
    	margin-bottom: 3px;
	}
	._inject_list_questions ul,._inject_list_questions_choosen ul{
	   	margin: 5px 0px;
	    border: 1px solid #00923f;
	    padding: 2px 5px;
	}
	._inject_icheck{
		cursor: pointer;
		background: #efefef;
    	padding: 2px 6px;
	}
	._inject_icheck input{
	 display: none;
	}
	._inject_icheck i{
	 font-style: normal;
	 display: inline-block;
	 vertical-align: middle;
	 width: 17px;
	 height: 17px;
	    border: solid 1px #00923f;
    color: #00923f;
	 text-align: center;
	 line-height: 16px;
	 margin-top: -2px;
	 margin-right: 3px;
	}
	._inject_icheck input:checked + i:after{
	 content: attr(symbol);
	}
	.noselect {
  	-webkit-touch-callout: none; 
    -webkit-user-select: none; 
     -khtml-user-select: none;
       -moz-user-select: none; 
        -ms-user-select: none;
            user-select: none; 
	}
	._inject_item_questions .code{
	    font-size: 15px;
    	font-weight: bold;
	}
	._inject_item_questions .line{
		height: 1px;
		width:100%;
	    background: #eaeaea;
	    display: block;
	    margin-bottom: 2px;
	}
	._inject_item_questions .name{
		    font-size: 10pt;
	}
	._inject_list_questions .mCSB_inside > .mCSB_container{
	    margin-right: 15px;
	}
</style>
