<?php

namespace NawrasBukhari\Restricted\Facades;

use Illuminate\Support\Facades\Facade;

class Restricted extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'restricted';
    }
}
