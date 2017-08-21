<?php

namespace Makeable\ValueObjects\Amount;

use Illuminate\Contracts\Support\Arrayable;
use Makeable\ValueObjects\Amount\CurrencyContract as Currency;
use Makeable\ValueObjects\Shared\RetrievesValues;
use Makeable\ValueObjects\Shared\ValidatesArrays;

class Amount implements Arrayable
{
    use AmountOperations,
        ConvertsCurrencies,
        ComparesAmounts,
        HasBaseCurrency,
        RetrievesValues,
        ValidatesArrays;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * Amount constructor.
     *
     * @param $amount
     * @param Currency $currency
     *
     * @throws \Exception
     */
    public function __construct($amount, Currency $currency = null)
    {
        if (! static::$baseCurrency) {
            throw new MissingBaseCurrencyException();
        }

        $this->amount = $amount;
        $this->currency = $currency ?: static::baseCurrency();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toFormat();
    }

    /**
     * @return Currency
     */
    public function currency()
    {
        return clone $this->currency;
    }

    /**
     * @param array $exported
     *
     * @return Amount
     *
     * @throws \Exception
     */
    public static function fromArray($exported)
    {
        static::requiresProperties(['amount', 'currency'], $exported);

        return new static(
            $exported['amount'],
            call_user_func([static::$defaultCurrencyImplementation, 'fromCode'], $exported['currency'])
        );
    }

    /**
     * @return float
     */
    public function get()
    {
        return round($this->amount, 2);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'amount' => $this->get(),
            'currency' => $this->currency()->getCode(),
            'formatted' => $this->toFormat(),
        ];
    }

    /**
     * @return string
     */
    public function toFormat()
    {
        return $this->currency()->getCode().' '.number_format($this->get(), 0, ',', '.');
    }

    /**
     * @return Amount
     */
    public static function zero()
    {
        return new static(0);
    }
}
