DateRange is Simpe Date Range Object for PHP
==============================================

DateRange is a simple representation of date range for PHP.

Requirements
------------

Dbup works with PHP 5.4.0 or later.

Install
--------

...

Usage
------

If you show demo, you have only to run `make demo`.

### Instance

Pass start and end date args version :

```php
use Brtriver\DateRange\DateRange;
$range = new DateRange('2015-12-01', '2015-12-31');
```
or Pass array of dates :

```php
$range = new DateRange(['2015-12-01', '2015-12-31']);
```

DateTime is also accepted :

```php
$start = new DateTime('2012-12-01');
$end = new DateTime('2012-12-31');
$range = new DateRange([$start, $end]);
```

### contains

```php
## check contains that date
var_dump($range->contains('2015-12-10'));
// bool(true)

var_dump($range->contains('2017-01-10'));
// bool(false)

```

### Period
If you use DateRange in foreach:

```php
foreach($range->getDatePeriod() as $d) {
    echo $d->format('Y-m-d') . PHP_EOL;
}
// 2015-12-01
// 2015-12-02
// 2015-12-03
// 2015-12-04
// ...
// 2015-12-28
// 2015-12-29
// 2015-12-30
```

If you want to change interval, call `setInterval`

```php
## if you want to change interval, use setInterval()
$start = new DateTime('2012-12-01');
$end = new DateTime('2020-12-31');
$range = new DateRange([$start, $end]);
$range->setInterval(new DateInterval('P1Y')); // change from 'P1D' (Default)
foreach($range->getDatePeriod() as $d) {
    echo $d->format('Y-m-d') . PHP_EOL;
}
// 2012-12-01
// 2013-12-01
// 2014-12-01
// 2015-12-01
// 2016-12-01
// 2017-12-01
// 2018-12-01
// 2019-12-01
// 2020-12-01
```

License
-------

DateRange is licensed under the MIT license.


