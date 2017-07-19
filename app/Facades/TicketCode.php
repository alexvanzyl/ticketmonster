<?php

namespace App\Facades;

use App\TicketCodeGenerator;
use Illuminate\Support\Facades\Facade;

class TicketCode extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return TicketCodeGenerator
     */
    protected static function getFacadeAccessor()
    {
        return TicketCodeGenerator::class;
    }

    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}