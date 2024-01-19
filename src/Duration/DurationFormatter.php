<?php

namespace Makeable\ValueObjects\Duration;

use Illuminate\Support\Collection;

class DurationFormatter
{
    /**
     * @var Duration
     */
    protected $duration;

    /**
     * @var Collection
     */
    protected $units;

    /**
     * DurationFormatter constructor.
     *
     * @param  Duration  $duration
     */
    public function __construct(Duration $duration)
    {
        $this->duration = $duration;
        $this->units = collect([
            ['placeholder' => 'h', 'value' => 0, 'size' => 60 * 60],
            ['placeholder' => 'm', 'value' => 0, 'size' => 60],
            ['placeholder' => 's', 'value' => 0, 'size' => 1],
        ]);
    }

    /**
     * @param  $format
     * @return mixed
     */
    public function get($format)
    {
        $remaining = $this->duration->getSeconds();

        // Distribute seconds to selected units
        $units = $this->units
            ->reject(function ($unit) use ($format) {
                return strpos($format, $unit['placeholder']) === false;
            })
            ->map(function ($unit) use (&$remaining) {
                $unit['value'] = floor($remaining / $unit['size']);
                $remaining -= $unit['value'] * $unit['size'];

                return $unit;
            });

        // Apply remaining seconds to smallest unit
        $smallestUnit = $units->pop();
        $smallestUnit['value'] += round($remaining / $smallestUnit['size']);
        $units->push($smallestUnit);

        // Ensure no unit exceeds its own size. For instance
        // you can't have 02:30:60. Normalize to 02:31:00
        $units = $this->normalizeUnitValues($units);

        // Apply format. If ie. 'hh', then pad with a zero
        return $this->applyFormat($units, $format);
    }

    /**
     * @param  Collection  $units
     * @param  $format
     * @return mixed
     */
    protected function applyFormat(Collection $units, $format)
    {
        return $units->reduce(function ($format, $unit) {
            return str_replace(
                [$unit['placeholder'].$unit['placeholder'], $unit['placeholder']],
                [str_pad((string) $unit['value'], 2, '0', STR_PAD_LEFT), $unit['value']],
                $format
            );
        }, $format);
    }

    /**
     * @param  Collection  $units
     * @return Collection
     */
    protected function normalizeUnitValues(Collection $units)
    {
        $carry = 0;
        $units = $units->reverse()->values();

        return $units->map(function ($unit, $index) use ($units, &$carry) {
            // Fetch the next unit size
            $next = $units->get($index + 1, ['size' => INF]);

            // Apply and reset carry
            $unit['value'] += round($carry / $unit['size']);
            $carry = 0;

            // Check for overflow and assign that to carry
            if ($unit['value'] * $unit['size'] >= $next['size']) {
                $modulus = ($unit['value'] * $unit['size']) % $next['size'];
                $carry = ($unit['value'] * $unit['size']) - $modulus;
                $unit['value'] = $modulus;
            }

            return $unit;
        });
    }
}
