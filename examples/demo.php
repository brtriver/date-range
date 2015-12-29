<?php
## if you run demo, `make demo` in root dir.

require __DIR__ . '/../tests/bootstrap.php';

use Brtriver\DateRange\DateRange;

## two args version
$range = new DateRange('2015-12-01', '2015-12-31');
// array version
$range = new DateRange(['2015-12-01', '2015-12-31']);
// DateTime is OK
$start = new DateTime('2012-12-01');
$end = new DateTime('2012-12-31');
$range = new DateRange([$start, $end]);

## check contains that date
var_dump($range->contains('2015-12-10'));
// bool(true)

var_dump($range->contains('2017-01-10'));
// bool(false)


## get DatePeriod for loop
foreach ($range as $d) {
    echo $d->format('Y-m-d') . PHP_EOL;
}

## or
foreach ($range->getDatePeriod() as $d) {
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
// 2015-12-31


## if you want to change interval, use setInterval()
$start = new DateTime('2012-12-01');
$end = new DateTime('2020-12-31');
$range = new DateRange([$start, $end]);
$range->setInterval(new DateInterval('P1Y')); // change from 'P1D' (Default)
foreach ($range->getDatePeriod() as $d) {
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

## try - catch

try {
    $range = new DateRange(['tomorrow', 'today']);
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();
}
