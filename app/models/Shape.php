<?php

class Shape extends Eloquent {
	protected $table = 'metroid_shapes';

	public function cschema()
	{
		return $this->belongsTo('CSchema', 'schema_id');
	}
	public function line()
	{
		return $this->belongsTo('Line');
	}
	public function points() {
		$points = explode(' ', $this->shape);

		foreach ($points as &$point) {
			$point = array_combine(array('x', 'y'), explode(',', $point));
		}

		return $points;
	}
}