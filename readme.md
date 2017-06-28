

# Laravel Value Objects

[![Latest Version on Packagist](https://img.shields.io/packagist/v/makeabledk/php-value-objects.svg?style=flat-square)](https://packagist.org/packages/makeabledk/php-value-objects)
[![Build Status](https://img.shields.io/travis/makeabledk/php-value-objects/master.svg?style=flat-square)](https://travis-ci.org/makeabledk/php-value-objects)
[![StyleCI](https://styleci.io/repos/95552761/shield?branch=master)](https://styleci.io/repos/95552761)

This package provides a set of handy value objects:

- Amount
- Duration
- Period
- Whitelist

Makeable is web- and mobile app agency located in Aarhus, Denmark.

## Install

You can install this package via composer:

``` bash
composer require makeabledk/php-value-objects
```

## Usage

### Amount

Amount provides a powerful way of interacting with amounts in different currencies.

#### Initial setup

The amount object requires a base currency to function. If you use Laravel that can be done from AppServiceProvider@boot:

```php
public function boot() {
    Amount::baseCurrency(Currency::fromCode('EUR'));
}
```

For that to work, you will need a Currency model that implements CurrencyContract. Consider the following implementation (Laravel):

```php
class Currency extends Eloquent implements \Makeable\ValueObjects\Amount\CurrencyContract
{
    public static function fromCode($code)
    {
        return static::where('code', $code)->firstOrFail();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getExchangeRate()
    {
        return $this->exchange_rate;
    }
}
```
Database table:
```
| id | code | exchange_rate |
|----|------|---------------|
| 1  | EUR  | 100.00        |
| 2  | USD  | 111.00        |
| 3  | DKK  | 750.00        |
```

### Example usages
Quickly create an amount
```php
new Amount(100); // EUR since that's our default
new Amount(100, Currency::fromCode('DKK')); 
```

Convert between currencies
```php
$eur = new Amount(100);
$dkk = $eur->convertTo(Currency::fromCode('DKK')); // 750 
```

Perform simple calculations - even between currencies!
```php
$amount = new Amount(100, Currency::fromCode('EUR'));
$amount->subtract(new Amount(50)); // 50 eur
$amount->subtract(new Amount(375, Currency::fromCode('DKK'))); // 50 eur
```

If you are using Laravel and have a Product@getPriceAttribute() accessor that returns an Amount object, you can even do this:
```php
$products = Product::all();
$productsTotalSum = Amount::sum($products, 'price'); 
```

Use the fluent modifiers for easy manipulation
```php
$amount = new Amount(110);
$amount->minimum(new Amount(80)); // 110 EUR
$amount->minimum(new Amount(120)); // 120 EUR
$amount->maximum(new Amount(80)); // 80 EUR
$amount->maximum(new Amount(750, Currency::fromCode('DKK')); // 100 EUR (eq. 750 DKK)
```

Easily export as an array, and re-instantiate if needed. Great for serving client API*.
```php
$exported = (new Amount(100))->toArray(); // ['amount' => 100, 'currency' => 'EUR', 'formatted' => 'EUR 100']
$imported = Amount::fromArray($exported);
```
*Note it implements illuminate/support Arrayable contract, so it automatically casts to an array for eloquent models.


### Duration

Duration provides an easy way to interact with and manipulate durations of time.

#### Example usages

Create a duration and display formatted
```php
Duration::create(1,30)->toFormat(); // 01:30:00
```

You can also specify a custom format. Valid placeholders are: h,hh,m,mm,s,ss
```php
Duration::$format = 'h:mm';
Duration::create(1,30)->toFormat(); // 1:30
```

Perform simple add/subtract calculations
```php
Duration::create(1,30)->add(Duration::create(1,30))->toFormat(); // 03:00:00
Duration::create(1,30)->subtract(Duration::create(0,20))->toFormat(); // 01:10:00
```

If you are using Laravel and have a Events@getDurationAttribute() accessor that converts to Duration::class, you can even do this:
```php
$events = Events::all();
$eventsTotalDuration = Duration::sum($events, 'duration'); 
```

Easily export as an array, and re-instantiate if needed. Great for serving client API*.
```php
$exported = Duration::create(1,30)->toArray(); // ['seconds' => 5400, 'minutes' => 90, 'hours' => 1.5, 'formatted' => '01:30:00']
$imported = Duration::fromArray($exported);
```
*Note it implements illuminate/support Arrayable contract, so it automatically casts to an array for eloquent models.

## Period

The Period object is great when you need to query data within a given period.

#### Example usages

Creating a period. Note that both start and end is optional.
```php
$today = new Period(Carbon::today(), Carbon::tomorrow());
$future = new Period(Carbon::now());
$past = new Period(null, Carbon::now());
```

Manipulate on the fly
```php
$thisWeek = new Period(
    Carbon::today()->previous(Carbon::MONDAY)
    Carbon::today()->next(Carbon::SUNDAY)
);
$thisWeek->earliest(Carbon::today())->getStart(); // carbon of today
$thisWeek->latest(Carbon::tomorrow())->getEnd(); // carbon of tomorrow
```

Easily export as an array, and re-instantiate if needed. Great for serving client API*.
```php
$exported = (new Period(Carbon::today(), Carbon::tomorrow()))->toArray(); // ['start' => '2017-06-27 00:00:00', 'end' => '2017-06-28 00:00:00']
$imported = Duration::fromArray($exported);
```
*Note it implements illuminate/support Arrayable contract, so it automatically casts to an array for eloquent models.

## Whitelist

Whitelist is an abstract class that you can extend to specify a certain sets of whitelisted values. 

It is great to quickly whip up a Status Object that ensures you are always working with a valid status.

### Example usages

Creating an OrderStatus class
```php
class OrderStatus extends Whitelist 
{
    const VALUES = ['pending', 'accepted', 'cancelled'];
}
```

Now you would only be able to instantiate OrderStatus with a valid status:
```php
$accepted = new OrderStatus('accepted');
$invalid = new OrderStatus('foobar'); // throws exception
```

You can customize the exception thrown. For instance you could swap it for the default Symfony/Laravel '422 UnprocessableEntityExceptions'.
```php
OrderStatus::$exceptionClass = \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException::class;
```
Now you have error handling out of the box for forms and wildcard controller methods (ie. '/orders/{status}') !


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

You can run the tests with:

```bash
composer test
```

## Contributing

We are happy to receive pull requests for additional functionality. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Rasmus Christoffer Nielsen](https://github.com/rasmuscnielsen)
- [All Contributors](../../contributors)

## License

Attribution-ShareAlike 4.0 International. Please see [License File](LICENSE.md) for more information.