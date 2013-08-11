<?php

class CSchema extends Eloquent {
	protected $table = 'metroid_schemas';

	public function coordinates()
	{
		return $this->hasMany('Coordinates');
	}

}