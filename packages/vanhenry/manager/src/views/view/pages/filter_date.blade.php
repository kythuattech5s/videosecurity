<div class="form-top-comparess mb-4">
    <div class="time-comparess">
        <input url="{{url('/').'/'.$admincp.'/statistic?type='.$type}}" start-date="{{$startDate}}" end-date="{{$endDate}}" class="date-comparess" max-date="{{date('d/m/Y')}}" type="text" name="daterange" value="{{$startDate}} - {{$endDate}}" />
    </div>
</div>