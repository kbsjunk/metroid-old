<?php

class Stop extends Eloquent {
	protected $table = 'metroid_stops';

	// public function osm_stops()
	// {
	// 	return $this->belongsToMany('OSM_Stops', 'metroid_osm_stops', 'stop_id', 'osm_stop_id');
	// }

	public function coordinates($schema = false)
	{
		return $this->hasMany('Coordinates', 'stop_id');//->where('schema_id', $schema->id);
	}

	public function rough_osm_stops()
	{
		// return $this->hasMany('OSM_Stop', 'stop_name', 	'name');
		return OSM_Stop::where('stop_name', $this->name)->get();
	}

	public function pos() {
		return $this->coordinates()->first();
	}

	public function subname() {
		if ($this->subname) return ' <small>('.$this->subname.')</small>';
	}

}