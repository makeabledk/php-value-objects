<?php

namespace Makeable\ValueObjects\Shared;

trait RetrievesValues
{
    /**
     * Returns a closure function to retrieve a value from an $item parameter.
     *
     * @param $callback
     * @return \Closure
     */
    private static function valueRetriever($callback)
    {
        if (is_callable($callback)) {
            return $callback;
        }

        if ($callback === null) {
            return function ($item) {
                return $item;
            };
        }

        return function ($item) use ($callback) {
            if (is_array($item)) {
                return $item[$callback];
            }
            if (is_object($item)) {
                return $item->$callback;
            }
            throw new \BadMethodCallException('Invalid arguments');
        };
    }
}
