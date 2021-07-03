<script type="text/javascript">
  function close_window() {
      parent.$.fancybox.close();
    }
   function hungvtApplyCallbackFile(arrItem,field_id){
    if(arrItem.length==0) return;
    var nxt = $('#'+field_id).prev();
    if($(nxt).prop('tagName').toLowerCase()=='img'){
      var item = arrItem[0];
      var def = $("[name=name]").val();
      if(def!=undefined){
        item.alt = def;
        item.title = def;
        item.caption = def;
        item.description = def;
      }
      $('#'+field_id).val(JSON.stringify(item)).trigger('change').trigger("input");
      $(nxt).attr('src', item.path+item.file_name);
    }
    else{
      var item = arrItem[0];
      var def = $("[name=name]").val();
      if(def!=undefined){
        item.alt = def;
        item.title = def;
        item.caption = def;
        item.description = def;
      }
      $('img#'+field_id).attr('src', item.path+item.file_name);
      $('img#'+field_id).attr('dt-value', JSON.stringify(item));

      var name_gallery = $('img#'+field_id).attr('rel');

      var arr = $('.gallery-'+name_gallery).find('>li');
      var ret =[];
      for (var i = 0; i < arr.length; i++) {
        var item = $(arr[i]);
        var img = item.find('img');
        var obj =JSON.parse(img.attr('dt-value'));
        ret.push(obj);
      }
      $('input[name='+name_gallery+']').val(JSON.stringify(ret));
    }
  }
  function changeListImageV2(_that,inputarget){
    var arr = $(_that).find('img');
    var str =new Array();
    for (var i = 0; i < arr.length; i++) {
      var item = arr[i];
      var tmp = JSON.parse($(item).attr('data-file'));
      str.push(tmp);
    };
    str = JSON.stringify(str);
    $('input[name='+inputarget+']').val(str);
  }
  
</script>