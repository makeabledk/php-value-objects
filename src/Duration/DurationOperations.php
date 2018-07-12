<?php

namespace Makeable\ValueObjects\Duration;

trait DurationOperations
{
    /**
     * @param Duration $duration
     *
     * @return static
     */
    public function add(Duration $duration)
    {
        return new static($this->seconds + $duration->getSeconds());
    }

    /**
     * @param Duration $duration
     *
     * @return static
     */
    public function subtract(Duration $duration)
    {
        return new static($this->seconds - $duration->getSeconds());
    }

    /**
     * Retrieve the sum of an array.
     *
     * @param $items
     * @param null $callback
     *
     * @return Duration
     *
     * @throws \Exception
     */
    public static function sum($items, $callback = null)
    {
        $callback = static::valueRetriever($callback);
        $sum = new static();

        foreach ($items as $item) {
            if (($amount = $callback($item)) !== null) {
                $sum = $sum ? $sum->add($amount) : $amount;
            }
        }

        return $sum;
    }
}
