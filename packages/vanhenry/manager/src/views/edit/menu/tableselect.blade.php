<?php $locale = \App::getLocale() ?>
<?php 
$tag_id= FCHelper::er($dataItem,'tag_id');
$tag_link= FCHelper::er($dataItem,'link');
$tag_name= FCHelper::er($dataItem,'name');
$exists = $tag_id!=""?true:false;
 ?>
<div class="data-select" data-select="{{$ksm}}">
  <input type="hidden" class="input-tag-id" value="{{$tag_id}}"/>
  <input type="hidden" class="input-tag-link" value="{{$tag_link}}"/>
  <button type="button" class="btn">{{$exists ? $tag_name: trans('db::choose')." ".$vsm[$locale]}}</button>
  <div class="select-search">
    <input type="text" class="input-{{$ksm}}" placeholder="{{trans('db::search')}} {{$vsm[$locale]}}">
    <div class="cssloader none ">
      <span class="cssload-loader"><span class="cssload-loader-inner"></span></span>  
    </div>
    
    <ul class="list-{{$ksm}}">
    </ul>
    <div class="select-pagination select-pagination-{{$ksm}}">
      <a href="#" title="" class="sp-prev"><i class="fa fa-angle-left"></i></a>
      <a href="#" title="" class="sp-next"><i class="fa fa-angle-right"></i></a>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function() {
    @if(is_array($dataItem))
      _VH_MENU_TABLE_SELECT.getDataDefault('{{$ksm}}',"");
    @endif
      _VH_MENU_TABLE_SELECT.initPag('{{$ksm}}');
      
      $('body').on('input', '.input-{{$ksm}}', function(event) {
        event.preventDefault();
        _VH_MENU_TABLE_SELECT.getDataDefault('{{$ksm}}',$(this).val());
      });
  });

</script>