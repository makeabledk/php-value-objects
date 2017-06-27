<?php

namespace Makeable\ValueObjects\Tests\Duration;

use Makeable\ValueObjects\Duration\Duration;
use Makeable\ValueObjects\Duration\DurationFormatter;
use Makeable\ValueObjects\Tests\TestCase;

class DurationTest extends TestCase
{
    public function test_it_can_get_the_hours_in_decimal()
    {
        $this->assertEquals(3, Duration::create(2.5, 30)->getHours());
        $this->assertEquals(2.75, Duration::create(2, 44, 60)->getHours());
    }

    public function test_it_can_get_the_minutes_in_decimal()
    {
        $this->assertEquals(105, Duration::create(1, 45)->getMinutes());
    }

    public function test_it_can_get_the_seconds_in_decimal()
    {
        $this->assertEquals(620, Duration::create(0, 10, 20)->getSeconds());
    }

    public function test_it_can_import_and_export()
    {
        $exported = Duration::create(1, 30)->toArray();
        $imported = Duration::fromArray($exported);
        $missingAttributes = array_diff_key(array_flip(['seconds', 'minutes', 'hours', 'formatted']), $exported);

        $this->assertEmpty($missingAttributes, 'Missing export attributes: '. implode(', ', $exported));
        $this->assertEquals(3600 + 1800, $imported->getSeconds());
    }

    public function test_it_casts_to_string()
    {
        Duration::$format = 'hh:mm:ss';
        $this->assertEquals('02:00:00', (string) Duration::create(2));
    }
}
