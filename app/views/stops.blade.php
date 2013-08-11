@extends('layout')

@section('content')

<ul style="position:absolute;width:800px;height:800px;list-style:none;margin:0;">
	@foreach($stops as $stop)
	<li style="position:absolute;top:{{ $dim->convert($stop->stop_lat, true, 800) }}px;left:{{ $dim->convert($stop->stop_lon, false, 800) }}px;" title="{{ $stop->stop_name }}">.</li>
	@endforeach
</ul>

@stop