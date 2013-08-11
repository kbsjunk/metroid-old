<?php

class Coordinates extends Eloquent {
	protected $table = 'metroid_coordinates';

	public function cschema()
	{
		return $this->belongsTo('CSchema', 'schema_id');
	}
	public function stop()
	{
		return $this->belongsTo('Stop');
	}
}