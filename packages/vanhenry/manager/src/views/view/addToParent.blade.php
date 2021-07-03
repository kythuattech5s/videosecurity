<div id="addToParent" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Thêm vào danh mục cha</h4>
      </div>
      <div class="modal-body">
        <form action="{{$admincp}}/addAllToParent/{{$tableData->get('table_map','')}}" dt-add="{{$admincp}}/addAllToParent/{{$tableData->get('table_map','')}}" dt-remove="{{$admincp}}/removeFromParent/{{$tableData->get('table_map','')}}" method="post" class="addtoparent">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
          <?php 
          foreach($tableDetailData as $_table){
            if($_table->name=='parent'){
              ?>
              <input type="hidden" name="type" value="{{FCHelper::ep($_table,'type_show')}}" >
              @include('vh::ctedit.select',array('table'=>$_table,'actionType'=>'none'))
          <?php break;  }
          }
          ?>
        </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick="$('.addtoparent').submit();" data-dismiss="modal">{{trans('db::edit')}}</button>
      </div>
    </div>

  </div>
</div>

