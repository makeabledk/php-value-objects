<?php

namespace Makeable\ValueObjects\Tests\Amount;

use Makeable\ValueObjects\Amount\Amount;
use Makeable\ValueObjects\Tests\TestCase;
use Makeable\ValueObjects\Tests\Amount\TestCurrency as Currency;

class AmountTestCase extends TestCase
{
    public function setUp()
    {
        Amount::baseCurrency(Currency::fromCode('EUR'));
    }

    protected function amount($amount, $currency=null)
    {
        if(is_string($currency)) {
            $currency = Currency::fromCode($currency);
        }

        return new Amount($amount, $currency);
    }
}