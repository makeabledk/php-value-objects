<?php

namespace Makeable\ValueObjects\Amount;

use Makeable\ValueObjects\Amount\CurrencyContract as Currency;

trait HasBaseCurrency
{
    /**
     * @var Currency
     */
    protected static $baseCurrency;

    /**
     * @var string
     */
    protected static $defaultCurrencyImplementation = \Exception::class;

    /**
     * @param Currency|null $currency
     *
     * @return Currency|void
     */
    public static function baseCurrency(Currency $currency = null)
    {
        if ($currency === null) {
            return clone static::$baseCurrency;
        }
        static::$baseCurrency = $currency;
        static::$defaultCurrencyImplementation = get_class($currency);
    }
}
