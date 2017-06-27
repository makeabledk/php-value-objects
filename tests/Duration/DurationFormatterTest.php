<?php

namespace Makeable\ValueObjects\Tests\Duration;

use Makeable\ValueObjects\Duration\Duration;
use Makeable\ValueObjects\Duration\DurationFormatter;
use Makeable\ValueObjects\Tests\TestCase;

class DurationFormatterTest extends TestCase
{
    public function setUp()
    {
        Duration::$format = 'hh:mm:ss';
    }

    public function test_it_accepts_a_duration_and_can_give_a_format()
    {
        $this->assertEquals('02:30:00', (new DurationFormatter(Duration::create(2,30)))->get('hh:mm:ss'));
    }

    public function test_it_can_output_a_format()
    {
        $this->assertEquals('02:30:00', Duration::create(2,30)->toFormat());
    }

    public function test_it_rounds_correctly_when_changing_format()
    {
        Duration::$format = 'hh:mm';
        $this->assertEquals('02:00', Duration::create(1,59,59)->toFormat());

        // Even support weird formats like this
        Duration::$format = 'hh.ss';
        $this->assertEquals('01.3599', Duration::create(1,59,59)->toFormat());
    }

    public function test_it_formats_both_padded_and_non_padded_numbers()
    {
        Duration::$format = 'h:mm';
        $this->assertEquals('2:00', Duration::create(2,0,1)->toFormat());

        Duration::$format = 'h:m:s';
        $this->assertEquals('1:0:1', Duration::create(1,0,1)->toFormat());
    }
}
