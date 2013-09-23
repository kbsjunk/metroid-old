<?php

class Shape extends Eloquent {
	protected $table = 'metroid_shapes';
	private $_points = array();
	private $_simple = array();

	public function cschema()
	{
		return $this->belongsTo('CSchema', 'schema_id');
	}
	public function line()
	{
		return $this->belongsTo('Line');
	}
	public function points() {
		if (!$this->_points) {
			$points = preg_split('/\s+/imx', trim($this->shape));

			foreach ($points as &$point) {
				$point = array_combine(array('x', 'y'), explode(',', $point));
			}
			$this->_points = $points;
		}

		return $this->_points;
	}

	public function simple() {
		if (!$this->_points) {
			$simple = Geography::simplifyPolyline(
				$this->points(),
				0.00025);
			$this->_simple = $simple;
		}
		return $this->_simple;
	}
	
	public function label_at($start = false) {
		$points = $this->points();
		return array(array_shift($points), array_pop($points));
	}
	// public function label_at($start = false) {
	// 	$point = $this->points();
	// 	return $start ? array_shift($point) : array_pop($point);
	// }
}