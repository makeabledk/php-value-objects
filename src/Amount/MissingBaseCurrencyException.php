<?php

namespace Makeable\ValueObjects\Amount;

class MissingBaseCurrencyException extends \Exception
{
    protected $message = 'Amount requires a base currency to run';
}
