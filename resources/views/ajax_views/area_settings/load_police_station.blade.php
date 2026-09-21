<div class="select-side">
    <img src="{{asset('../assets/icons/down_arrow.png')}}" alt="">
</div>
{{Form::select('police_station_id',$police_stations,null,['class'=>'form-control','id'=>'police_station_id','required','placeholder'=>'Thana'])}}