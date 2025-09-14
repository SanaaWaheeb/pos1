<?php

namespace alalm3i\EdfaPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \alalm3i\EdfaPay\EdfaPay
 */
class EdfaPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \alalm3i\EdfaPay\EdfaPay::class;
    }
}
