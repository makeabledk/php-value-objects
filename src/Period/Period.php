<?php

namespace Makeable\ValueObjects\Period;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Makeable\ValueObjects\Shared\ValidatesArrays;

class Period implements Arrayable, JsonSerializable
{
    use ValidatesArrays;

    /**
     * @var Carbon|null
     */
    protected $start;

    /**
     * @var Carbon|null
     */
    protected $end;

    /**
     * TimeSpan constructor.
     *
     * @param  $start
     * @param  $end
     */
    public function __construct($start = null, $end = null)
    {
        $this->start = $this->normalizeInput($start);
        $this->end = $this->normalizeInput($end);

        $this->validate($this->start, $this->end);
    }

    /**
     * @param  array  $exported
     * @return Period
     *
     * @throws \Exception
     */
    public static function fromArray(array $exported)
    {
        static::requiresProperties(['start', 'end'], $exported);

        return new static($exported['start'], $exported['end']);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'start' => $this->getStart() ? $this->getStart()->toDateTimeString() : null,
            'end' => $this->getEnd() ? $this->getEnd()->toDateTimeString() : null,
        ];
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return Carbon
     */
    public function getStart()
    {
        return $this->normalizeOutput($this->start);
    }

    /**
     * @return Carbon
     */
    public function getEnd()
    {
        return $this->normalizeOutput($this->end);
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param  Carbon  $time
     * @return Period
     */
    public function earliest(Carbon $time)
    {
        return new static(
            $this->getStart() ? $this->getStart()->max($time) : $time,
            $this->getEnd() ? $this->getEnd()->max($time) : null
        );
    }

    /**
     * @param  Carbon  $time
     * @return Period
     */
    public function latest(Carbon $time)
    {
        return new static(
            $this->getStart() ? $this->getStart()->min($time) : null,
            $this->getEnd() ? $this->getEnd()->min($time) : $time
        );
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param  $time
     * @return Carbon|null
     */
    protected function normalizeInput($time)
    {
        if ($time instanceof Carbon) {
            return $time;
        }
        if ($time === null) {
            return;
        }

        return Carbon::parse($time);
    }

    /**
     * @param  $time
     * @return Carbon|null
     */
    protected function normalizeOutput($time)
    {
        return $time === null ? null : $time->copy();
    }

    /**
     * @param  Carbon  $start
     * @param  Carbon  $end
     *
     * @throws \Exception
     */
    protected function validate($start, $end)
    {
        if ($start && $end && $start->greaterThan($end)) {
            throw new \Exception('End date cannot be before start date');
        }
    }
}
