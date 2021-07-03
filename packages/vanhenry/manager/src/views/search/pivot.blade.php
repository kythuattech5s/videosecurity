<div class="search-item search-{{$item->name}} search-type-text flex">
	<?php
	$defaultData = json_decode($item->default_data, true);
	$target_table = $defaultData['target_table'];
	$target_select = $defaultData['target_select'];
	?>
	<select style="width:100%" data-placeholder="Vui lòng nhập tên {{$item->note}}" class=" ajx_search_{{$item->name}}" name="{{$item->name}}">
		<?php
		if (isset($dataSearch[$item->name])) {
			$langChoose = FCHelper::langChooseOfTable($tableData->get('table_map'));
			$transTable = FCHelper::getTranslationTable($tableData->get('table_map'));
			if ($transTable != null) {
				$pivot = \DB::table($tableData->get('table_map'))->select('id', 'name as text')->join($transTable->table_map, 'id', '=', 'map_id')->where('language_code', $langChoose)->where('id', $dataSearch[$item->name])->first();
			}
			else{
				$pivot = \DB::table($tableData->get('table_map'))->select('id', 'name as text')->where('id', $dataSearch[$item->name])->first();	
			}
			if ($pivot != null) {
				echo '<option value="'.$pivot->id.'">'.$pivot->text.'</option>';
			}
		}
		?>
	</select>
</div>
<script type="text/javascript">
	$(function() {
		$('.ajx_search_{{$item->name}}').select2({
			minimumInputLength: 2,
			ajax: {
				delay: 1000,
				url: "{{$admincp}}/getDataPivot/{{$target_table}}",
				dataType: 'json',
				cache: true,
				data: function (params) {
			      	return {
	                  	q: params.term || '',
	                  	page: params.page || 1,
	                  	target_select: '{!!json_encode($target_select)!!}',
						origin_table: '{{$tableData->get('table_map')}}'
	              	}
				}
			}
		});
	});
</script>