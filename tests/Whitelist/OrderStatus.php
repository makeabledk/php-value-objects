<?php

namespace Makeable\ValueObjects\Tests\Whitelist;

use Makeable\ValueObjects\Whitelist\Whitelist;

class OrderStatus extends Whitelist
{
    const VALUES = ['pending', 'accepted', 'cancelled'];
}
