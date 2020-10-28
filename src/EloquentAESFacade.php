<?php

namespace RichardStyles\EloquentAES;

use Illuminate\Support\Facades\Facade;

class EloquentAESFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'eloquentaes';
    }
}