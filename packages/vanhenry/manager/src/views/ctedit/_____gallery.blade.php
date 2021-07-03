<?php 

$name = FCHelper::er($table,'name');

$value ="";

if($actionType=='edit'||$actionType=='copy'){

  $value = FCHelper::ep($dataItem,$name);

}

?>

<div class="form-group">

  <p class="form-title" for="">{{FCHelper::er($table,'note')}} <span class="count"></span></p>

  <div class="container-fluid" >

    <input placeholder="{{FCHelper::er($table,'note')}}" {{FCHelper::ep($table,'require')==1?'required':''}}  type="hidden" name="{{$name}}" value="{{$value}}" dt-type="{{FCHelper::er($table,'type_show')}}">

    <div class="scrollbar" style="max-height:250px;overflow:hidden;">

  	<ul class="gallery aclr row gallery-{{$name}}">

      <?php 

      $arrValues = json_decode($value,true);

       ?>

      @if($arrValues!=null)

        @foreach($arrValues as $itemValue)

          <li class="col-sm-3 col-xs-12 gallery-item gallery-item-{{$name}}">

            <div>

              <img class="img-responsive" dt-value="{{json_encode($itemValue)}}" src="{{FCHelper::ep($itemValue,'img',0)}}" title="{{FCHelper::ep($itemValue,'name',1)}}" alt="{{FCHelper::ep($itemValue,'description',1)}}">

              <p>{{FCHelper::ep($itemValue,'name',1)}}</p>  

              <i class="fa fa-times-circle gallery-close"></i>

              <button type="button">Thay ảnh</button>

            </div>

          </li>

        @endforeach

      @endif

  		

      

  		

  	</ul>



    </div>

    <div class="textcenter">

    <button type="button" class="createItem createItem{{$name}}  bgmain viewsite"><i class="fa fa-file-o"></i> <span class="clfff">Thêm mới</span></button>

    </div>

  </div>

</div>

<script type="text/javascript">

	 $(function() {

        createContextMenu{{$name}}();

        $(".gallery-{{$name}}").sortable({

          tolerance: 'pointer',

          items: "> li",

          start: function( event, ui ) {

              clone = $(ui.item[0].outerHTML).clone();

          },

          helper: function(event, ui){

            var $clone =  $(ui).clone();

            $clone .css('position','absolute');

            return $clone.get(0);

          },

          cursor: "move",

          placeholder: {

              element: function(clone, ui) {

                  return $('<li class="selected col-sm-3 col-xs-12 gallery-item gallery-item-{{$name}} ">'+clone[0].innerHTML+'</li>');

              },

              update: function() {

                  return;

              }

          },

          stop:function(event,ui){

            getValueChange{{$name}}();

          }

    });   

        $( ".gallery" ).disableSelection();

        $(document).on('click', '.gallery-item .gallery-close', function(event) {

          event.preventDefault();

          $(this).parent().parent().remove();

          getValueChange{{$name}}();

        });

        $('.createItem{{$name}}').click(function(event) {

          var str = '<li class="col-sm-3 col-xs-12 gallery-item gallery-item-{{$name}}">';

              str+='<div>';

              str +='<img class="img-responsive" dt-value=\'{"img": "admin/images/noimage.png","name": "Hình ảnh","description": "Hình ảnh"}\' src="admin/images/noimage.png" title="Hình ảnh" alt="Hình ảnh">';

              str +='<p>Hình ảnh</p>';

              str +='<i class="fa fa-times-circle gallery-close"></i>';

              str +='<button type="button">Thay ảnh</button>';

              str +='</div>';

              str +='</li>';

          $('.gallery-{{$name}}').append(str);

          getValueChange{{$name}}();

        });

    });

   function createContextMenu{{$name}}(){

    $.contextMenu({

        selector: '.gallery-{{$name}} .gallery-item', 

        callback: function(key, options) {

          switch(key){

            case 'delete':

              $(this).remove();

            break;

            case 'duplicate':

              var clone = $(this).clone(true, true);

              clone.removeClass('context-menu-active');

              clone.insertAfter(this);

            break;

            case 'edit':

              createEditDialog(this);

            break;

          }

          getValueChange{{$name}}();

        },

        items: {

            "edit": {name: "Edit", icon: function(){

              return "fa fa-pencil";

            }},

            "duplicate": {name: "Duplicate", icon: function(){

              return "fa fa-clone";

            }},

            "delete": {name: "Delete", icon: function(){

              return "fa fa-trash";

            }},

            "sep1": "---------",

            "close": {name: "Close", icon: function(){

                return 'fa fa-times';

            }}

        }

    });

   }

    function getValueChange{{$name}}(){

      var arr = $('.gallery-{{$name}}').find('>li');

      var ret =[];

      for (var i = 0; i < arr.length; i++) {

        var item = $(arr[i]);

        var img = item.find('img');

        var obj =JSON.parse(img.attr('dt-value'));

        ret.push(obj);

      }

      $('input[name={{$name}}]').val(JSON.stringify(ret));

    }

    function createEditDialog(_this){

      var obj = $(_this).find('img').attr('dt-value');

      obj = JSON.parse(obj);

      var str ='<div class="form-group gallery-popup">';

      for(var key in obj) {

          if(key=='img') continue;

          var value = obj[key];

          str +=' <div class="form-group">';

          str +='<label for="'+key+'">'+key+':</label>';

          str +='<input type="text" key="'+key+'" value="'+value+'" class="form-control">';

          str +='</div>';

      }

      str +='</form>';

      bootbox.dialog({

            title: "Chỉnh sửa thông tin hình ảnh.",

            message: str,

            buttons: {

                success: {

                    label: "Save",

                    className: "btn-success",

                    callback: function () {

                      var ps =$('.gallery-popup input');

                      for (var i = 0; i < ps.length; i++) {

                        var item = $(ps[i]);

                        obj[item.attr('key')] = item.val();

                        $(_this).find('img').attr('dt-value',JSON.stringify(obj));

                      }

                    }

                }

            }

        }

    );

    }

</script>

