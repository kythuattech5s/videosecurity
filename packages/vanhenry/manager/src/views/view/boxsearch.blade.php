<div class="advancefilter pull-left">
                      <button type="button" class="robo  clmain btnfilter">{{trans('db::CONDITION_FILTER')}}<span class="caret"></span></button>
                      <div class="row setfilter">
                      <h3>{{trans('db::show')}} {{$tableData->get('name','')}} {{trans('db::as')}} </h3>
                        
                        {%FILTER.advanceSearchs.filterAdvanceSearch.tableDetailData%}
                        <select name="keychoose" class="select2">
                          <option value="-1">{{trans('db::choose_condition_filter')}}</option>
                          @foreach(@$advanceSearchs as $c)
                          <option dt-type="{{$c->type}}" value="{{$c->name}}">{{$c->note}}</option>
                          @endforeach
                        </select>
                        <span class="show">là</span>
                        <div class="add">
                          @foreach(@$advanceSearchs as $c)
                          <?php 
                            $viewSearch = 'vh::search.'.strtolower(FCHelper::er($c,'type_show'));
                            $viewSearch = View::exists($viewSearch)?$viewSearch:"vh::search.text";
                          ?>
                            @include($viewSearch,array('item'=>$c))
                          @endforeach
                        </div>
                        <button type="button" class="btnadd">{{trans('db::add_condition_filter')}}</button>
                        <button type="button" class="btnclose">{{trans('db::close')}}</button>
                      </div>
                    </div>
                    <form method="post" action="{{$admincp}}/search/{{$tableData->get('table_map','')}}" class="">
                    <div class="boxsearch">
                      <i class="fa fa-search"></i>
                      
                      {%FILTER.simpleSearch.filterSimpleSearch.tableDetailData%}
                      <input type="text" name="raw_{{$simpleSearch->name}}" placeholder="{{trans('db::search')}} {{trans('db::as')}} {{$simpleSearch->note}}">
                    </div>
                    <button type="submit">{{trans('db::search')}}</button>
                  
                  </form>
                  <div class="listfilter">
                    <ul class="aclr">
                    
                    </ul>
                  </div>