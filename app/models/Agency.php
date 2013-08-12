<?php

class Agency extends Eloquent {
	protected $table = 'metroid_agency';

/*
|--------------------------------------------------------------------------
| Relationships
|--------------------------------------------------------------------------
*/

public function lines($schema = false)
{
	return $this->hasMany('Agency', 'agency_id');
}

public function svg()
{
	return $this->morphMany('SVG', 'imageable');
}

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

}