<?php

class Line extends Eloquent {
	protected $table = 'metroid_lines';

	public function shapes($schema = false)
	{
		return $this->hasMany('Shape', 'line_id');
	}

	public function agency()
	{
		return $this->belongsTo('Agency');
	}

	public function subname() {
		if ($this->subname) return ' <small>('.$this->subname.')</small>';
	}

}