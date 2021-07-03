<div class="orderby aclr">

    <h4 class="pull-left">{{trans('db::orderby')}} </h4>

    <select name="orderkey" class="select2 pull-left">

      

      {%FILTER.simpleSort.filterSimpleSort.tableDetailData%}



      @foreach($simpleSort as $ss)

      <option {{$ss->type_show == "PRIMARY_KEY"?"selected":""}} value="{{$ss->name}}">{{$ss->note}}</option>

      @endforeach

    </select>

    <select name="ordervalue" class="select2 pull-left">

      <option value="asc">{{trans('db::from')}} A->Z</option>

      <option selected value="desc">{{trans('db::from')}} Z->A</option>

    </select>

    <h4 class="pull-left">{{trans('db::show')}}</h4>

    <select name="limit" class="select2 pull-left">

      <option value="10">10</option>

      <option value="20">20</option>

      <option value="50">50</option>

      <option value="100">100</option>

    </select>

</div>