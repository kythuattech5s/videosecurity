<?php 

$name = FCHelper::er($table,'name');

$defaultData = FCHelper::er($table,'default_data');

$arrKey = json_decode($defaultData,true);

$arrKey = FCHelper::er($arrKey,'data');

$_dbTable = $arrKey['table'];

$_dbField = explode(',',$arrKey['select']);

$__dbWhere = $arrKey['where'];

$_dbWhere = array();

foreach ($__dbWhere as $key => $value) {

	$tmp = array();

	foreach ($value as $k => $v) {

		array_push($tmp,$k);

		array_push($tmp,$v);

	}

	array_push($_dbWhere, $tmp);

}

/*giá trị trong DB*/

$value="";

$arrTags=vanhenry\manager\helpers\DetailTableHelper::getSomeFielDataOfTable($_dbTable,$_dbField,$_dbWhere);

$intersectData1 =array();

if($actionType=='edit'||$actionType=='copy'){

	$value=FCHelper::er($dataItem,$name);

	$_value = explode(',',$value);

	$intersectData1 = array_intersect($_value,array_keys($arrTags));

}

?>

<div class="form-group">

      <div class="col-xs-12">

        <ul class="list-tags list-tags-{{$name}} selected aclr">

        	@foreach($intersectData1 as $id1 =>$vid1)

        	<li><a dt-id="{{FCHelper::er($arrTags[$vid1],'id')}}" >{{FCHelper::er($arrTags[$vid1],'name')}}<i onclick="return removeTag{{$name}}(this);" class="fa fa-close"></i></a></li>

        	@endforeach

        </ul>

        <input {{FCHelper::ep($table,'require')==1?'required':''}}  type="hidden" value="{{$value}}" name="{{$name}}" placeholder="{{FCHelper::er($table,'note')}}">

      </div>

      <div class="col-xs-12 old-tags">

        <p class="">Thêm các nhãn đã có</p>

        <ul class="list-tags list-tags-{{$name}} aclr scrollbar" style="height:100px">



		 @foreach($arrTags as $tag)

          <li class=""><a class="item-tag"  dt-id="{{$tag->id}}" href="#">{{$tag->name}}</a></li>

         @endforeach



        </ul>

      </div>

</div>

<script type="text/javascript">

	function getValueTag{{$name}}(){

		var arr = $('.list-tags-{{$name}}.selected li a');

		var ret = [];

		for (var i = 0; i < arr.length; i++) {

			var item = $(arr[i]);

			ret.push(item.attr('dt-id'));

		}

		$('input[name={{$name}}]').val(JSON.stringify(ret));

	}

	function  removeTag{{$name}}(_this){

		$(_this).parent().parent().remove();

		$('.list-tags .item-tag[dt-id='+$(_this).parent().attr('dt-id')+']').parent().removeClass('choosen');

		getValueTag{{$name}}()

		return false;

	}

	$(function() {

		$('.list-tags-{{$name}} .item-tag').click(function(event) {

			event.preventDefault();

			if($(this).parent().hasClass('choosen')){

				showToast("Thông báo","Thẻ {{FCHelper::er($table,'note')}} đã được chọn!","warning");

			}

			else{

				$(this).parent().addClass('choosen');

				$('.list-tags-{{$name}}.selected').append('<li><a dt-id="'+$(this).attr('dt-id')+'" >'+$(this).text()+' <i onclick="return removeTag{{$name}}(this);" class="fa fa-close"></i></a></li>');		

				getValueTag{{$name}}();

			}



		});

	});

</script>

