<td data-title="{{$show->note}}" @if($tableData->get('table_map','')=='orders') style="text-align: left; vertical-align: top; overflow: hidden;" @endif>







	<div  dt-prop="{{$show->is_prop ?? 0}}" dt-prop-id="{{$show->id}}"  name="{{$show->name}}" title="{{$show->note}}">



		@if($tableData->get('table_map','')!='orders')



		{!!strip_tags(FCHelper::ep($dataItem,$show->name),'<p><br>')!!}



		@else



		{!!FCHelper::ep($dataItem,$show->name)!!}



		@endif



	</div>



</td>