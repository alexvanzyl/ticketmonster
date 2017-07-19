<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\OrderConfirmationNumberGenerator;

class OrderConfirmationNumber extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return OrderConfirmationNumberGenerator
     */
    protected static function getFacadeAccessor()
    {
        return OrderConfirmationNumberGenerator::class;
    }
}