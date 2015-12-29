<?php
namespace Brtriver\DateRange;

use DateTime;
use DateInterval;
use DatePeriod;
use IteratorAggregate;

class DateRange implements IteratorAggregate
{
    private $start;
    private $end;
    private $interval;
    private $excludeStartDate = false;
    private $excludeEndDate = false;
    const INTERVAL = 'P1D';

    public function __construct() {
        $num = func_num_args();
        if ($num === 2) {
            $this->start = self::convertToDateTime(func_get_arg(0));
            $this->end = self::convertToDateTime(func_get_arg(1));
        } elseif ($num === 1 && is_array(func_get_arg(0)) && count(func_get_arg(0)) === 2) {
            $startEndArray = func_get_arg(0);
            if (is_array($startEndArray) && count($startEndArray) === 2) {
                $values = array_values($startEndArray);
                $this->start = self::convertToDateTime($values[0]);
                $this->end = self::convertToDateTime($values[1]);
            }
        } else {
            throw new \InvalidArgumentException('Invalid argument number or format');
        }

        if ($this->start->getTimestamp() > $this->end->getTimestamp()) {
            throw new \InvalidArgumentException('end date is the day before than start date');
        }

        $this->interval = new DateInterval(self::INTERVAL);
    }

    public function excludeStartDate()
    {
        $this->excludeStartDate = true;
    }

    public function excludeEndDate()
    {
        $this->excludeEndDate = true;
    }

    public function setInterval(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function getDatePeriod(DateInterval $interval = null)
    {
        if (!$this->excludeEndDate) {
            $end = clone $this->end;
            // DatePeriod does not include end date so, plus 1 sec to end date.
            $end->modify('+1 sec');
        } else {
            $end = $this->end;
        }

        $option = null;
        if ($this->excludeStartDate) {
            $option = DatePeriod::EXCLUDE_START_DATE;
        }

        return new DatePeriod($this->start, ($interval) ?: $this->interval, $end, $option);
    }

    public function getIterator()
    {
        return $this->getDatePeriod();
    }

    public static function convertToDateTime($param)
    {
        if ($param instanceOf DateTime) {
            return $param;
        }
        if (strtotime($param) === false) {
            throw new \InvalidArgumentException('Invalid datetime string');
        }

        return new DateTime($param);
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function contains($dateString)
    {
        $date = self::convertToDateTime($dateString);
        $timestamp = $date->getTimestamp();

        if ($this->excludeStartDate) {
            $isAfterThanStart = $this->start->getTimestamp() < $timestamp;
        } else {
            $isAfterThanStart = $this->start->getTimestamp() <= $timestamp;
        }

        if ($this->excludeEndDate) {
            $isBeforeThanEnd = $timestamp < $this->end->getTimestamp();
        } else {
            $isBeforeThanEnd = $timestamp <= $this->end->getTimestamp();
        }


        return $isAfterThanStart && $isBeforeThanEnd;
    }
}
