<?php

namespace Makeable\ValueObjects\Tests\Duration;

use Makeable\ValueObjects\Duration\Duration;
use Makeable\ValueObjects\Tests\TestCase;

class DurationOperationsTest extends TestCase
{
    public function setUp(): void
    {
        Duration::$format = 'hh:mm:ss';
        parent::setUp();
    }

    public function test_it_can_add_other_durations()
    {
        $this->assertEquals('01:40:10', Duration::create(1, 30)->add(Duration::create(0, 10, 10))->toFormat());
    }

    public function test_it_can_subtract_other_durations()
    {
        $this->assertEquals('01:19:50', Duration::create(1, 30)->subtract(Duration::create(0, 10, 10))->toFormat());
    }

    public function test_it_can_sum_an_array_of_durations()
    {
        $this->assertEquals('03:00:00', Duration::sum([Duration::create(1, 30), Duration::create(1, 30)])->toFormat());
    }

    public function test_it_can_sum_an_multidimensional_array_containing_durations_using_a_key()
    {
        $sum = Duration::sum([
            ['duration' => Duration::create(1, 30)],
            ['duration' => Duration::create(1, 30)],
            ['duration' => null], // should be skipped
        ], 'duration');

        $this->assertEquals('03:00:00', $sum->toFormat());
    }

    public function test_it_can_sum_an_multidimensional_array_containing_durations_using_a_callback()
    {
        $sum = Duration::sum([
            ['duration' => Duration::create(1, 30)],
            ['duration' => Duration::create(1, 30)],
        ], function ($item) {
            return $item['duration'];
        });

        $this->assertEquals('03:00:00', $sum->toFormat());
    }
}
