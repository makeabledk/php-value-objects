# Laravel Value Objects

[![Latest Version on Packagist](https://img.shields.io/packagist/v/makeabledk/php-value-objects.svg?style=flat-square)](https://packagist.org/packages/makeabledk/php-value-objects)
[![Build Status](https://img.shields.io/github/workflow/status/makeabledk/php-value-objects/Run%20tests?label=Tests)](https://github.com/makeabledk/php-value-objects/actions)
[![StyleCI](https://styleci.io/repos/95552761/shield?branch=master)](https://styleci.io/repos/95552761)

This package provides a set of handy value objects:

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