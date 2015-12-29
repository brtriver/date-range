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
        $this->parseArguments(func_get_args());

        if (!($this->start instanceof DateTime) || !($this->end instanceof DateTime)) {
            throw new \InvalidArgumentException('cannot parse start and end date');
        }

        if ($this->start->getTimestamp() > $this->end->getTimestamp()) {
            throw new \InvalidArgumentException('end date is the day before than start date');
        }

        $this->interval = new DateInterval(self::INTERVAL);
    }

    private function parseArguments($args)
    {
        if (count($args) === 1) {
            list($this->start, $this->end) = self::getDateFromArray($args[0]);
        } elseif (count($args) === 2) {
            $this->start = self::convertToDateTime($args[0]);
            $this->end = self::convertToDateTime($args[1]);
        } else {
            throw new \InvalidArgumentException('Invalid number of arguments');
        }
    }

    private static function getDateFromArray($startEndArray)
    {
        $start = $end = null;

        if (is_array($startEndArray) && count($startEndArray) === 2) {
            $values = array_values($startEndArray);
            $start = self::convertToDateTime($values[0]);
            $end = self::convertToDateTime($values[1]);
        }

        return [$start, $end];
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

        if ($this->excludeStartDate) {
            $isAfterThanStart = $this->start < $date;
        } else {
            $isAfterThanStart = $this->start <= $date;
        }

        if ($this->excludeEndDate) {
            $isBeforeThanEnd = $date < $this->end;
        } else {
            $isBeforeThanEnd = $date <= $this->end;
        }


        return $isAfterThanStart && $isBeforeThanEnd;
    }
}
