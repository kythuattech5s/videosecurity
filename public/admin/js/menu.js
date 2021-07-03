  $(function() {


    $.fn.extend({


       qcss: function(css) {


          return $(this).queue(function(next) {


             $(this).css(css);


             next();


          });


       }


    });


    // var openMenu = localStorage.getItem('_admin_menu_open');


    // openMenu = openMenu==undefined?'off':openMenu;


    // if(openMenu=='on'){


    //   // $('.site-wrap').animate({'left':'250px'},0);


    // }


    // $('.site-wrap').attr('data-menu',openMenu);


    // $('.navigation').attr('data-menu',openMenu);


    $('.nav-trigger').click(function(){


      


      


      // resizeMainWorker();


      var $o = $('.site-wrap').attr('data-menu');


      $o = $o=='on'?'off':'on';


      $.ajax({


        url: $('base').attr('href')+$('meta[name="admincp"]').attr('content')+"/onoffmenu",


        type: 'GET',


        global:false,


        data: {status: $o}


      });


      if($o=="on"){


       // $('.site-wrap').animate({'left':'250px','width':$(window).width()-250},500);


        


        // $('.flag').animate({'opacity':1}, 300);


        $('span.txt').hide().delay(200).qcss({'display':'inline-block','height':'inherit','width':'inherit'});


        $('.user-more').hide().delay(200).qcss({'display':'inline'});


      }


      else{


        //$('.site-wrap').animate({'left':'58px','width':$(window).width()-58},500);


        // $('.flag').animate({'opacity':0}, 300);


        $('span.txt').css({'display':'block','height':'0px','width':'0px'});


        $('.user-more').hide();


      }


      $('.site-wrap').attr('data-menu',$o);


      $('.navigation').attr('data-menu',$o);


      // localStorage.setItem('_admin_menu_open',$o);


    });


    /*Menu*/


    $('.main-menu .nav-item').each(function(index, el) {


      var l = $(el).find(">ul.sub").length;


      if(l>0){


        $(el).find(">a").addClass('caret_right');


      }


    });





    // $('.main-menu .nav-item >a').click(function(event) {


    //   event.preventDefault();


    //   var _this = $(this).parent();


    //   if($('.navigation').attr('data-menu')=='off') return;


    //   if($(_this).find(">ul.sub").length==0) return;


    //   $('.main-menu .nav-item > a.active').removeClass('active');





    //   var sub = $(_this).find(">ul.sub");


    //   if(sub.is(":visible") && !sub.hasClass('subhover')){


    //     sub.slideUp(500);


    //     $(_this).find(">a").removeClass('caret_down');


    //   }


    //   else{


    //     sub.removeClass('subhover').hide();


    //     $(_this).find(">a").addClass('caret_down active');


    //     sub.slideDown(500);


    //   }


    //   ($(_this).hasClass('clicked')?$(_this).removeClass('clicked'):$(_this).addClass('clicked'));


    // });





    $('.main-menu .nav-item').hover(function() {


      if($(this).find(">ul.sub").length==0) return;


      var sub = $(this).find(">ul.sub");


      if(!$(this).hasClass('clicked')){


        sub.addClass('subhover');


        sub.fadeIn(0);


        sub.find('span.txt').css({'display':'inline-block','height':'inherit','width':'inherit'});


      }


    }, function() {


      var sub = $(this).find(">ul.sub");


      if(!$(this).hasClass('clicked')){


        sub.removeClass('subhover');


        sub.fadeOut(0);


         sub.find('span.txt').css({'display':'block','height':'0px','width':'0px'});


      }


    });


    $('.main-menu .nav-item .sub').hover(function() {


      


        $(this).prev().addClass('active');  


      


    }, function() {


      if(!$(this).parent().hasClass('clicked')){


        $(this).prev().removeClass('active');


      }


    });





    /*Menu user*/


    $('.user-info').click(function(event) {


      if(!$('.user-info-hover').is(':visible')){


        $('.user-info-hover').show(); 


      }


      else{


       $('.user-info-hover').hide();   


      }


      


    });














  });