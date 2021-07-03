<tr>
                  <td><span class="arrow fa fa-arrows"></span></td>

                  <td>
                  @foreach($tableDetailData as $dt)
                    <?php 
                      $viewEdit = 'vh::ctedit.'.StringHelper::normal(FCHelper::er($dt,'type_show'));
                      $viewEdit = View::exists($viewEdit)?$viewEdit:"vh::ctedit.base";
                    ?>
                    @include($viewEdit,array('table'=>$dt))
                  @endforeach
                  </td>
                  <td>
                    @include('vh::edit.menu.module',array('showInMenu'=>$showInMenu))
                    @foreach($showInMenu as $ksm =>$vsm)
                      @if($ksm == "home")
                      @elseif($ksm == "out")
                        @include('vh::edit.menu.tableinput',array('ksm'=>$ksm,'vsm'=>$vsm))
                      @else
                        @include('vh::edit.menu.tableselect',array('ksm'=>$ksm,'vsm'=>$vsm))
                      @endif
                    @endforeach
                    
                    
                  </td>
                  <td><button type="button" class="del-menu btn "><i class="fa fa-trash-o"></i></button></td>
                </tr>