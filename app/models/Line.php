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

	public function dasharray() {
		if ($this->dash) {
			return 'stroke-dasharray:' . $this->dash . ';';
		}
	}

	public function color() {
		return '#' . ($this->color ? trim($this->color, '# ') : 'black');
	}
	public function color_kml() {
		if ($this->color) {
			return implode('', array_reverse(str_split(trim($this->color, '# '), 2)));
		}
		else {
			return '000000';
		}

	}

}