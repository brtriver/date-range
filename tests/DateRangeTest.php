<?php
namespace Brtriver\DateRange\Test;

use Brtriver\DateRange\DateRange;
use DateTime;
use DateInterval;
use DatePeriod;

class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    private $start;
    private $end;
    public function setUp()
    {
        $this->start = new DateTime('2015-12-01');
        $this->end = new DateTime('2015-12-31');
    }

    /** @test */
    public function acceptArguments()
    {
        // as two date parameters.
        $range = new DateRange($this->start, $this->end);
        $this->assertSame($this->start, $range->getStart());
        $this->assertSame($this->end, $range->getEnd());

        // as one array of date
        $range = new DateRange([$this->start, $this->end]);
        $this->assertSame($this->start, $range->getStart());
        $this->assertSame($this->end, $range->getEnd());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function throwErrorWhenNumberOfArgumentsIsOver3()
    {
        new DateRange('2015-12-01', '2015-12-10', '2015-12-31');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function throwErrorWhenArrayArgumentIsInvalid()
    {
        new DateRange(['2015-12-01', '2015-12-10', '2015-12-31']);
    }


    /** @test */
    public function convertStringToDateTimeObject()
    {
        $this->assertEquals(new DateTime('2015-12-31 00:00:00'), DateRange::convertToDateTime('2015-12-31'));
        $param = new DateTime('2015-12-31 00:00:00');
        $this->assertSame($param, DateRange::convertToDateTime($param));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function throwErrorWhenInvalidDatetimeString()
    {
        DateRange::convertToDateTime('2015-33-33');
    }

    /** @test */
    public function defaultInterval()
    {
        $range = new DateRange([$this->start, $this->end]);
        $this->assertEquals(new DateInterval('P1D'), $range->getInterval());
    }

    /** @test */
    public function changeInterval()
    {
        $range = new DateRange([$this->start, $this->end]);
        $range->setInterval(new DateInterval('P1Y'));
        $this->assertEquals(new DateInterval('P1Y'), $range->getInterval());
    }

    /** @test */
    public function getDatePeriod()
    {
        $range = new DateRange([$this->start, $this->end]);
        $periodEnd = clone $this->end;
        $periodEnd->modify('+1 sec');
        $expected = new DatePeriod($this->start, new DateInterval('P1D'), $periodEnd);
        $this->assertEquals($expected, $range->getDatePeriod());
    }

    /** @test */
    public function getIterator()
    {
        $range = new DateRange(['2015-12-01', '2015-12-04']);
        $results = [];
        foreach ($range as $d) {
            $results[] = $d;
        }
        $expected = [
            new DateTime('2015-12-01'),
            new DateTime('2015-12-02'),
            new DateTime('2015-12-03'),
            new DateTime('2015-12-04'),
        ];
        $this->assertEquals($expected, $results);
    }


    /** @test */
    public function testContains()
    {
        $range = new DateRange([$this->start, $this->end]);
        $this->assertFalse($range->contains('2015-11-30'));
        $this->assertTrue($range->contains('2015-12-01'));
        $this->assertTrue($range->contains('2015-12-31'));
        $this->assertFalse($range->contains('2016-01-01'));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function constructInvalidArrayArgument()
    {
        // construct parameter should have an array with two values.
        new DateRange(['today']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldHaveEndDateIsAfterThanStartDate()
    {
        new DateRange(['tomorrow', 'today']);
    }

    /** @test */
    public function excludeStartDate()
    {
        $range = new DateRange([$this->start, $this->end]);
        $range->excludeStartDate();
        $this->assertFalse($range->contains('2015-12-01'));
        $this->assertTrue($range->contains('2015-12-02'));

        // dirty test .. (check the first value only)
        foreach ($range as $d) {
            $this->assertEquals(new DateTime('2015-12-02'), $d);
            break;
        }
    }

    /** @test */
    public function excludeEndDate()
    {
        $range = new DateRange([$this->start, $this->end]);
        $range->excludeEndDate();
        $this->assertFalse($range->contains('2015-12-31'));
        $this->assertTrue($range->contains('2015-12-30'));

        $last = null;
        // dirty test..  (check the last value only)
        foreach ($range as $d) {
            $last = $d;
        }
        $this->assertEquals(new DateTime('2015-12-30'), $last);
    }

    /** @test */
    public function diff()
    {
        $range = new DateRange('2015-12-01', '2015-12-03');
        $this->assertEquals('+2 days', $range->diff()->format('%R%a days'));
    }

    /** @test */
    public function toString()
    {
        $range = new DateRange('2015-12-01', '2015-12-03');
        $this->assertEquals('2015-12-01 ~ 2015-12-03', $range);
        $this->assertEquals('2015/12/01 - 2015/12/03', $range->toString('Y/m/d', '-'));
    }
}
