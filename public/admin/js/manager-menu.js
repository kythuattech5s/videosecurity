jQuery(function($) {
	
    $( ".menu-manage table tbody" ).sortable({
        handle: "td>span.arrow",
        placeholder: "menu-sortable",
    });
    $( ".menu-manage table tbody" ).disableSelection();

    $('#add-menu').on('click',function(){
        var tr = $(this).parents('.menu-manage').find('table.hidden.tablemenu tbody').children('tr').first().clone();
        $(this).parents('.menu-manage').find('table.tablemain').append(tr);
    });

    $('.menu-manage').on('click','td .del-menu',function() {
        if($(this).parents('tbody').children('tr').length <=1){
            $(this).parents('table').hide();
            $('.no-menu-table').show();
        }
        $(this).parent().parent('tr').remove();      
    });



    /*...............................*/
    $('.menu-manage').on('change','.menu-select',function(){
        var dt_sl = $(this).val();
        $(this).nextAll('div').hide();
        $(this).nextAll('div').children('input[type="hidden"]').val('');
        $(this).nextAll('div').each(function(){
            if($(this).attr('data-select') == dt_sl){
                $(this).css('display', 'inline-block');
            }  
        });
    });
    $('.menu-select').each(function(index, el) {
       var selected = $(el).find('option:selected').val(); 
       $(el).parent().find('[data-select='+selected+']').css('display','inline-block');
    });
    
    $('.menu-manage').on('click','div[data-select] button',function(){
        $(this).next('.select-search').toggle();
    })
    $('.menu-manage').on('click','div[data-select] li',function(){
        $(this).parents('.select-search').hide();
        $(this).parents('div[data-select]').children('button').text($(this).text());
        $(this).parents('div[data-select]').children('input.input-tag-id[type="hidden"]').val($(this).attr('data-value'));
        $(this).parents('div[data-select]').children('input.input-tag-link[type="hidden"]').val($(this).attr('data-link'));
    });
    $(window).click(function(event) {
        $('.data-select').each(function() {
            if($(this).has(event.target).length == 0 && !$(this).is(event.target)){
                $(this).children('.select-search').hide();
            }
        });
        
    });

    /*...............................*/




    $('.update-view input').change(function() {
        if($(this).is(":checked")) $('.update-view-input').show();
        else $('.update-view-input').hide();
    });

    $('.permision-table input[data-value="6"]').change(function() {
        if($(this).is(":checked")) $(this).parents('tr').find('input').prop('checked', true);
        else $(this).parents('tr').find('input').prop('checked', false);
    });

    /*..............*/
    $('.permision-panel .panel-heading h4').click(function() {
        $(this).parent().nextAll('.panel-body').slideToggle(200);
    });

    jQuery(function($) {
        $('.menu-box-ct ul li').each(function() {
          if($(this).find('ul>li').length>0){
            $(this).prepend('<span class="plus"></span>');
          }
        });
        $('.menu-box-ct ul li span').click(function() {
            var sub=$(this).nextAll('ul');
            if(sub.is(":hidden")===true){
              sub.slideDown();
              $(this).removeClass('plus').addClass('minus');
            }
            else{
              sub.slideUp();
              $(this).removeClass('minus').addClass('plus');
            }
          });

        $('.menu-box-head .collapse-btn').click(function(e) {
          e.preventDefault();
          var mBox = $(this).parent('.menu-box-head').nextAll('.menu-box-ct');
          if($(this).hasClass('open')){
            mBox.find('ul ul').slideDown();
            mBox.find('li>span').removeClass('plus').addClass('minus');

            $(this).removeClass('open').text('Hide all');
          }
          else{
            mBox.find('ul ul').slideUp();
            mBox.find('li>span').removeClass('minus').addClass('plus');
            $(this).addClass('open').text('Show all');
          }
        });
      })

});

/** xử lý ajax **/
var _VH_MENU_MAIN = {};
    _VH_MENU_MAIN.saveMenu= function(){
      var trs = $('.tablemain tbody tr');
      var ret = [];
      for (var i = 0; i < trs.length; i++) {
        var item = $(trs[i]);
        var obj = {};
        var tds = item.find('td:nth-child(2) input.form-control');
        for (var j = 0; j < tds.length; j++) {
          $_i = $(tds[j]);
          obj[$_i.attr('name')] = $_i.val();
        }
        obj.table = item.find('.menu-select').val();
        var dtsl = item.find('.data-select:visible');
        if(dtsl.length>0){
          var tag_id = dtsl.find('input.input-tag-id');
          if(tag_id.length>0){
            obj.tag_id = tag_id.val();  
          }
          
          obj.link = dtsl.find('input.input-tag-link').val();
        }
        ret.push(obj);
      }
      return ret;
    }
    _VH_MENU_MAIN.submitMenu = function(url,issave){
      var ret = _VH_MENU_MAIN.saveMenu();
      if(issave){
         $('<form action="'+url+'" method="POST"></form>')
            .append('<input type="hidden" name="_token" value="{{csrf_token()}}"/><input name="data" value=\'' + JSON.stringify(ret) + '\' />')
            .submit().remove();
      }
      else{
        $.ajax({
          url: url,
          type: 'POST',
          data: {data: JSON.stringify(ret)},
        });
        
      }
    }
    if(typeof _VH_MENU_TABLE_SELECT=="undefined"){
      _VH_MENU_TABLE_SELECT = {};
    }
    if(typeof _VH_MENU_TABLE_SELECT.cssLoader !== 'function'){
      _VH_MENU_TABLE_SELECT.cssLoader= function(show){
        if(show){
          $('.cssloader').show();
        }
        else{
          $('.cssloader').hide();
        }
      }
    }
    if(typeof _VH_MENU_TABLE_SELECT.processResponse !=='function'){
      _VH_MENU_TABLE_SELECT.processResponse = function(json,table){
        try{
            var str ="";
            for (var i = 0; i < json.data.length; i++) {
              var item = json.data[i];
              str += "<li data-link='"+item.slug+"' data-value='"+item.id+"'>"+item.name+"</li>";
            }
            $('.list-'+table).html(str);
            var pag = $('.list-'+table).next();
            if(json.prev_page_url!=null){
              pag.find('.sp-prev').removeClass('disable').attr('href',json.prev_page_url);
            }
            else{
              pag.find('.sp-prev').addClass('disable');
            }
            if(json.next_page_url!=null){
              pag.find('.sp-next').removeClass('disable').attr('href',json.next_page_url);
            }
            else{
              pag.find('.sp-next').addClass('disable');
            }
          }
          catch(ex){}
      }
    }
    if(typeof _VH_MENU_TABLE_SELECT.getDataDefault !== 'function'){
      _VH_MENU_TABLE_SELECT.getDataDefault = function (table,key){
        _VH_MENU_TABLE_SELECT.cssLoader(true);
        $.ajax({
          url: $('meta[name=admincp]').attr('content')+'/getDataMenu/'+table,
          type: 'POST',
          global:false,
          data:{keyword:key}
        })
        .done(function(json) {
          _VH_MENU_TABLE_SELECT.processResponse(json,table);
        }).always(function(){_VH_MENU_TABLE_SELECT.cssLoader(false);});
        
      }
    }
    if(typeof _VH_MENU_TABLE_SELECT.initPag !=='function'){
      _VH_MENU_TABLE_SELECT.initPag = function(table){
        $('.select-pagination-'+table+' .sp-next,.select-pagination-'+table+' .sp-prev').off('click').on('click', function(event) {
          event.preventDefault();
          _VH_MENU_TABLE_SELECT.cssLoader(true);
          $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            global:false
          })
          .done(function(json) {
            _VH_MENU_TABLE_SELECT.processResponse(json,table);
          }).always(function(){_VH_MENU_TABLE_SELECT.cssLoader(false);});
        });
      }
    }