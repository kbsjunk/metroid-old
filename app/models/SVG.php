<?php

class SVG extends Eloquent {
	protected $table = 'metroid_svg';

	public function imageable()
	{
		return $this->morphTo();
	}

}