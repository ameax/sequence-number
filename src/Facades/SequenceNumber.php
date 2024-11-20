<?php

namespace Ameax\SequenceNumber\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ameax\SequenceNumber\SequenceNumber
 */
class SequenceNumber extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ameax\SequenceNumber\SequenceNumber::class;
    }
}
