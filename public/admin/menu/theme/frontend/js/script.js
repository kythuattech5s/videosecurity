jQuery(function($) {

    var menu = $('.menu-manage');
    var list = menu.find('.nav-list.first');
    var item = list.children('.nav-item').first().html();
    var noMenu = $('.no-menu-table');

    var nav_sortable = function(){
        menu.find(".nav-list" ).sortable({
            handle: "span.arrow",
            placeholder: "menu-sortable",
            connectWith: ".nav-list",
        }).disableSelection();
    }
       
    nav_sortable();

    $('#add-menu').on('click',function(){
        list.append('<li class="nav-item">'+item+'</li>');
        noMenu.hide();
        list.show();

        nav_sortable();
    });

    menu.on('click','.plus',function() {
        var item_pa = $(this).parent().parent('.nav-item');
        item_pa.append('<ul class="nav-list"><li class="nav-item">'+item+'</li></ul>');
        nav_sortable();
    })
    menu.on('click','.del-menu',function() {
        if(list.children('.nav-item').length <=1){
            list.hide();
            noMenu.show();
        }
        $(this).parent().parent('.nav-item').remove();
    });

    /*...............................*/
    menu.on('change','.menu-select',function(){
        var dt_sl = $(this).val();
        $(this).nextAll('div').hide();
        $(this).nextAll('div').children('input[type="hidden"]').val('');
        $(this).nextAll('div').each(function(){
            if($(this).attr('data-select') == dt_sl){
                $(this).css('display', 'inline-block');
            }  
        });
    });
    menu.on('click','div[data-select] button',function(){
        $(this).next('.select-search').toggle();
    })
    menu.on('click','div[data-select] li',function(){
        $(this).parents('.select-search').hide();
        $(this).parents('div[data-select]').children('button').text($(this).text());
        $(this).parents('div[data-select]').children('input[type="hidden"]').val($(this).attr('data-value'));
    });
    $(window).click(function(event) {
        $('.data-select').each(function() {
            if($(this).has(event.target).length == 0 && !$(this).is(event.target)){
                $(this).children('.select-search').hide();
            }
        });    
    });

});