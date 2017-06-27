<?php

namespace Makeable\ValueObjects\Whitelist;

abstract class Whitelist
{
    const VALUES = [];

    /**
     * @var string
     */
    public static $exceptionClass = \Exception::class;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        if (!static::isValid($value)) {
            throw new static::$exceptionClass('Invalid value '.$value);
        }
        $this->status = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->status;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, static::VALUES);
    }
}
