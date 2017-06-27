<?php

namespace Makeable\ValueObjects\Duration;

use Illuminate\Contracts\Support\Arrayable;
use Makeable\ValueObjects\Shared\RetrievesValues;
use Makeable\ValueObjects\Shared\ValidatesArrays;

class Duration implements Arrayable
{
    use DurationOperations,
        RetrievesValues,
        ValidatesArrays;

    /**
     * Valid placeholders: hh, mm, ss.
     *
     * @var string
     */
    public static $format = 'hh:mm:ss';

    /**
     * @var int
     */
    protected $seconds;

    /**
     * Duration constructor.
     *
     * @param $seconds
     */
    public function __construct($seconds = 0)
    {
        $this->seconds = (int) $seconds;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toFormat();
    }

    /**
     * @return float
     */
    public function getHours()
    {
        return round($this->seconds / 3600, 2);
    }

    /**
     * @return float
     */
    public function getMinutes()
    {
        return round($this->seconds / 60, 2);
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @return string
     */
    public function toFormat()
    {
        return (new DurationFormatter($this))->get(static::$format);
    }

    /**
     * @param float $hours
     * @param int   $minutes
     * @param int   $seconds
     *
     * @return static
     */
    public static function create($hours, $minutes = 0, $seconds = 0)
    {
        return new static($hours * 3600 + $minutes * 60 + $seconds);
    }

    /**
     * @param array $exported
     *
     * @return Duration
     */
    public static function fromArray(array $exported)
    {
        self::requiresProperties('seconds', $exported);

        return new static($exported['seconds']);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'seconds' => $this->getSeconds(),
            'minutes' => $this->getMinutes(),
            'hours' => $this->getHours(),
            'formatted' => $this->toFormat(),
        ];
    }
}
