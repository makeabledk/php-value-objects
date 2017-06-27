<?php

namespace Makeable\ValueObjects\Tests\Period;

use Carbon\Carbon;
use Makeable\ValueObjects\Period\Period;
use Makeable\ValueObjects\Tests\TestCase;

class PeriodTest extends TestCase
{
    public function test_start_and_end_is_optional()
    {
        $empty = new Period();
        $this->assertNull($empty->getStart());
        $this->assertNull($empty->getEnd());
        $this->assertNotNull((new Period(Carbon::now()))->getStart());
        $this->assertNotNull((new Period(null, Carbon::now()))->getEnd());
    }

    public function test_it_sets_dates()
    {
        $period = new Period(Carbon::today(), Carbon::tomorrow());
        $this->assertEquals(Carbon::today()->toDateTimeString(), $period->getStart()->toDateTimeString());
        $this->assertEquals(Carbon::tomorrow()->toDateTimeString(), $period->getEnd()->toDateTimeString());
    }

    public function test_end_cannot_be_before_start()
    {
        $this->expectException(\Exception::class);
        new Period(Carbon::now(), Carbon::yesterday());
    }

    public function test_it_can_import_and_export()
    {
        $exported = (new Period(Carbon::today(), Carbon::tomorrow()))->toArray();
        $imported = Period::fromArray($exported);
        $missingAttributes = array_diff_key(array_flip(['start', 'end']), $exported);

        $this->assertEmpty($missingAttributes, 'Missing export attributes: ' . implode(', ', $exported));
        $this->assertEquals($imported->getStart()->toDateTimeString(), Carbon::today()->toDateTimeString());
        $this->assertEquals($imported->getEnd()->toDateTimeString(), Carbon::tomorrow()->toDateTimeString());
    }

    public function test_it_can_set_an_earliest_date()
    {
        list($nextMonday, $nextWednesday, $nextSunday) = [
            Carbon::today()->next(Carbon::MONDAY),
            Carbon::today()->next(Carbon::MONDAY)->next(Carbon::WEDNESDAY),
            Carbon::today()->next(Carbon::MONDAY)->next(Carbon::SUNDAY),
        ];

        $nextWeek = new Period($nextMonday, $nextSunday);
        $this->assertEquals(CARBON::MONDAY, $nextWeek->getStart()->dayOfWeek);
        $this->assertEquals(CARBON::SUNDAY, $nextWeek->getEnd()->dayOfWeek);

        // Earliest date is before start date
        $this->assertEquals(CARBON::MONDAY, $nextWeek->earliest(Carbon::today()->previous(Carbon::FRIDAY))->getStart()->dayOfWeek);

        // Earliest date is after start date
        $this->assertEquals(CARBON::WEDNESDAY, $nextWeek->earliest($nextWednesday)->getStart()->dayOfWeek);
        $this->assertEquals(CARBON::SUNDAY, $nextWeek->earliest($nextWednesday)->getEnd()->dayOfWeek);

        // Earliest date is after both start and end date
        $this->assertEquals(CARBON::MONDAY, $nextWeek->earliest($nextMonday->addWeek())->getStart()->dayOfWeek);
        $this->assertEquals(CARBON::MONDAY, $nextWeek->earliest($nextMonday->addWeek())->getEnd()->dayOfWeek);

        // If no end date is specified, it should stay that way
        $this->assertNull((new Period($nextMonday))->earliest($nextWednesday)->getEnd());
    }

    public function test_it_can_set_an_latest_date()
    {
        list($nextMonday, $nextWednesday, $nextSunday) = [
            Carbon::today()->next(Carbon::MONDAY),
            Carbon::today()->next(Carbon::MONDAY)->next(Carbon::WEDNESDAY),
            Carbon::today()->next(Carbon::MONDAY)->next(Carbon::SUNDAY),
        ];

        $nextWeek = new Period($nextMonday, $nextSunday);

        // Latest date is before end date
        $this->assertEquals(CARBON::MONDAY, $nextWeek->latest($nextWednesday)->getStart()->dayOfWeek);
        $this->assertEquals(CARBON::WEDNESDAY, $nextWeek->latest($nextWednesday)->getEnd()->dayOfWeek);

        // Latest date is before both start and end date
        $this->assertEquals(CARBON::WEDNESDAY, $nextWeek->latest($nextWednesday->subWeek())->getStart()->dayOfWeek);
        $this->assertEquals(CARBON::WEDNESDAY, $nextWeek->latest($nextWednesday->subWeek())->getEnd()->dayOfWeek);

        // If no start date is specified, it should stay that way
        $this->assertNull((new Period(null, $nextWednesday))->latest($nextMonday)->getStart());
    }
}
