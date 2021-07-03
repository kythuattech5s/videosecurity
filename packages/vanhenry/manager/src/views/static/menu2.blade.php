<div class="top_menu">
     <div class="nav-top aclr">
          <a class="show pull-left" href="{{$admincp}}">
              <img class="imglogo" src="{Ilogo_admin.imgI}"> 
              <img class="smalllogo none" src="{Ilogo_admin.imgI}">
          </a>
         {{-- <ul class="pull-right">
            <li class="flag pull-left"><a href="{{$admincp}}/changelang/vi"><img src="public/images/flag_vi.png" alt="Việt Nam"></a></li>
            <li class="flag pull-left"><a href="{{$admincp}}/changelang/en"><img src="public/images/flag_en.png" alt="English"></a></li>
          </ul> --}}
     </div>
     <div class="header-top aclr">

          <div class="breadc pull-left">

               <?php $exs = \Event::dispatch('vanhenry.manager.headertop.view',[]); ?>
               @foreach ($exs as $exk => $exvs)
               @if(is_array($exvs))
               @foreach($exvs as $exvv)
               @include($exvv)
               @endforeach
               @endif
               @endforeach
               {{-- <a class="pull-right bgmain1 viewsite" target="_blank" href="{{asset('/')}}">
                    <i class="fa fa-external-link" aria-hidden="true"></i>
                    <span  class="clfff">{{trans('db::see_website')}}</span> 
               </a> --}}
               <a class="pull-right btn-func bottom" href="{{$admincp}}">
                    <i class="fa fa-home pull-left"></i>
                    <span>{{trans('db::home')}}</span>
               </a>
               <a class="pull-right btn-func bottom" href="{{$admincp}}/deleteCache">
                    <i class="fa fa-trash-o pull-left" aria-hidden="true"></i>
                    <span>{{trans('db::delete_cache')}}</span>
               </a>
               <a class="pull-right btn-func bottom" href="{{$admincp}}/editSitemap">
                    <i class="fa fa-sitemap pull-left" aria-hidden="true"></i>
                    <span>Sitemap</span>
               </a>
               <a class="pull-right btn-func bottom" href="{{$admincp}}/editRobot">
                    <i class="fa fa-android pull-left" aria-hidden="true"></i>
                    <span>Robots.txt</span>
               </a>
               <a class="pull-right btn-func bottom"  target="_blank" href="">
                    <i class="fa fa-external-link pull-left" aria-hidden="true"></i>
                    <span >{{trans('db::see_website')}}</span> 
               </a>
          </div>
          <div class="right_header_admin">
               
               {{-- <img class="pull-left" src="admin/images/user.png" alt=""> --}}
               <div class="r_h_t_admin">
                    <small>
                         <i class="fa fa-user" aria-hidden="true"></i>
                         <span>Admin</span>
                         <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </small>
                    <ul>
                         <li><a href="{{asset('/')}}">{{trans('db::see_website')}}</a></li>
                         {{-- <li><a href="">{{trans('db::my_account')}}</a></li> --}}
                         <li><a href="" data-toggle="modal" data-target="#changepass">{{trans('db::change_pass')}}</a></li>
                         {{-- <li><a href="{{$admincp}}/terms">{{trans('db::term')}}</a></li> --}}
                         <li><a href="{{$admincp}}/logout">{{trans('db::logout')}}</a></li>
                    </ul>
               </div>
          </div>
     </div>
     
</div>

<div class="navigation" data-menu = "<?php echo session('menu_status', 'off') ?>">
     
     <ul class="main-menu">
       {{-- <li class="search">
         <form action="">
           <input type="text" class="q" placeholder="Tìm kiếm sản phẩm">
         </form>
       </li> --}}
       @foreach($userglobal['menu'] ?: []   as $pmenu)
       <li class="nav-item"><a href="{{$admincp.'/'}}{{FCHelper::ep($pmenu,'link')}}"><i class="{{$pmenu->icon}}"></i><span style="<?php session("menu_status",'off')=='on'?'display:inline-block;height:inherit;width:inherit;':'display:block;height:0px;width:0px'; ?>" class="txt">{{FCHelper::ep($pmenu,'name')}}</span></a>
         <ul class="sub none" >
         @foreach($pmenu->childs as $cmenu)
           <li><a href="{{$admincp}}/{{$cmenu->link}}" class="show"><span class="txt">{{FCHelper::ep($cmenu,'name')}}</span></a></li>
         @endforeach
         </ul>
       </li>
       @endforeach
     </ul>
</div>