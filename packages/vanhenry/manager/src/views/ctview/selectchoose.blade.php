<td data-title="{{$show->note}}" style="text-align: center;">
	<?php $statuses= \App\Status::where("act",1)->orderBy("ord","asc")->get();$value = FCHelper::ep($dataItem,$show->name); ?>
	<select style="padding: 3px;border: 1px solid #00923f; {{$value=='7'?'background:#00923f;color:#fff;':($value==1?'background:#e96a0c;color:#fff;':'')}}"  dt-prop-id="{{$show->id}}" class="{{$show->editable==1?'editable':''}} select2{{$show->name}}"  name="{{$show->name}}" title="{{$show->note}}"  style="width: 150px;">
		@foreach($statuses as $status)
			<option style="background:#fff;color:#000" {{$value==$status->id?'selected':''}} value="{{$status->id}}">{{$status->name}}</option>
		@endforeach
	</select>
	<script type="text/javascript">
		$(function() {
			$('.select2{{$show->name}}').change(function(event) {
				var val = $(this).val();
				if(val == 7){
					$(this).css({'background':'#00923f','color':'#fff'});
					$(this).find("option").css({'background':'#fff','color':'#000'});
				}
				else if(val == 1){
					$(this).css({'background':'#e96a0c','color':'#fff'});
					$(this).find("option").css({'background':'#fff','color':'#000'});
				}
				else{
					$(this).css({'background':'#fff','color':'#000'});
				}
			});
		});
	</script>
</td>