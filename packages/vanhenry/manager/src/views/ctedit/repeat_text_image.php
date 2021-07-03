<div class="row">
	<p class="des col-xs-12">{{FCHelper::ep(($tableMap=='configs'?$dataItem:$table),'note')}}</p>
	<div class="col-xs-12">
		<div class="ti_item">
			<p>Tiêu đề</p>
			<input type="text" class="ti_text">
			<img style="    margin: 0 auto;max-width: 30%;" src="{{($tableData->get('table_map','')=='transactions'?$value:$img)}}" alt="" class="img-responsive">
			<input placeholder="{{FCHelper::er($table,'note')}}"  type="hidden" value="{{$value}}" name="{{$name}}" id="{{$name}}">
			<div class="form-group textcenter">
				<a href="{{$admincp}}/media/view?istiny={{$name}}" class="browseimage bgmain btn btn-primary iframe-btn" type="button">{{trans('db::choose_img')}}</a>
				<button style="margin-top: 15px;margin-left: 5px;" class="btnchange-{{$name}} bgmain btn btn-primary">{{trans('db::edit')}}</button>
				<button style="margin-top: 15px;margin-left: 5px;" class="btndelete-{{$name}} bgmain btn btn-primary">{{trans('db::delete')}}</button>
			</div>
		</div>
		
	</div>
</div>