<?php

namespace Makeable\ValueObjects\Tests\Whitelist;

use BadMethodCallException;
use Makeable\ValueObjects\Tests\TestCase;

class WhitelistTest extends TestCase
{
    public function test_it_instantiates_with_valid_value()
    {
        $this->assertEquals('accepted', (new OrderStatus('accepted'))->get());
    }

    public function test_it_throws_validation_exception()
    {
        $this->expectException(\Exception::class);
        new OrderStatus('invalid status');
    }

    public function test_exception_class_can_be_customized()
    {
        OrderStatus::$exceptionClass = BadMethodCallException::class;

        $this->expectException(BadMethodCallException::class);
        new OrderStatus('invalid status');
    }

    public function test_it_casts_to_string()
    {
        $this->assertEquals('accepted', (string) new OrderStatus('accepted'));
    }

    public function test_it_can_validate_a_string()
    {
        $this->assertTrue(OrderStatus::isValid('accepted'));
        $this->assertFalse(OrderStatus::isValid('foobar'));
    }
}
