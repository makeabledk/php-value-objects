<?php

namespace Makeable\ValueObjects\Tests\Amount;

use Makeable\ValueObjects\Amount\Amount;
use Makeable\ValueObjects\Tests\TestCase;

class AmountTestCase extends TestCase
{
    public function setUp()
    {
        Amount::test();
    }

    protected function amount($amount, $currency = null)
    {
        return new Amount($amount, $currency);
    }
}
