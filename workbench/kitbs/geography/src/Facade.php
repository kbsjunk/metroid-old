<?php

namespace Kitbs\Geography;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'geography'; }

    /**
     * Serve a collection or number of collections.
     * 
     * @return string
     */
    // public static function show()
    // {
    // 	return basset_assets(func_get_args());
    // }

}