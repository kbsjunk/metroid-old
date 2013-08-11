<?php

class Agency extends Eloquent {
	protected $table = 'metroid_agency';

	public function svg()
	{
		return $this->morphMany('SVG', 'imageable');
	}

}