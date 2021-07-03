<?php 
$name = FCHelper::er($table,'name');
$default_code = FCHelper::er($table,'default_code');
$default_code = json_decode($default_code,true);
$default_code = @$default_code?$default_code:array();
$lang = \Session::get('_table_lang');
$value ="";
if($actionType=='edit'||$actionType=='copy'){
	$value = FCHelper::er($dataItem,$name);
}
?>
<div class="form-group">
	<p class="form-title" for="">{{FCHelper::ep(($tableMap=='configs'?$dataItem:$table),'note')}} <span class="count"></span></p>
	
	<input  {{FCHelper::ep($table,'require')==1?'required':''}} type="text" name="{{$name}}" placeholder="{{FCHelper::ep($table,'note')}}"  class="form-control" dt-type="{{FCHelper::ep($table,'type_show')}}" value="{{$value}}" />
</div>
<div class="form-group " style="    border: 1px solid #e96a0c;
    " >
    <div class="row" style="margin:10px;">
	<?php $listObjectMenus = \vanhenry\helpers\helpers\AdminMenuHelper::getAllTableHasSlug(); ?>
	@foreach($listObjectMenus as $itemMenu)
		<div class="col-md-3 col-xs-12">
			<select onchange="chooseLink_{{$name}}(this);" class="select2">
				<option value="">Chọn {{$itemMenu->note}}</option>
				@foreach($itemMenu->children as $child)
					<option value="{{$child->slug}}">{{$child->name}}</option>
				@endforeach
			</select>
		</div>
	@endforeach
	</div>
</div>
<script type="text/javascript">
	function chooseLink_{{$name}}(_this){
		$('input[name="{{$name}}"]').val($(_this).val());
	}
</script>