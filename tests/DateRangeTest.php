<?php
namespace Brtriver\DateRange\Test;

use Brtriver\DateRange\DateRange;
use Datetime;
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
    public function acceptStartEndParams()
    {
        $range = new DateRange($this->start, $this->end);

        $this->assertSame($this->start, $range->getStart());
        $this->assertSame($this->end, $range->getEnd());
    }

    /** @test */
    public function acceptStartEndArray()
    {
        $range = new DateRange([$this->start, $this->end]);

        $this->assertSame($this->start, $range->getStart());
        $this->assertSame($this->end, $range->getEnd());
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
        $expected = new DatePeriod($this->start, new DateInterval('P1D'), $this->end);
        $this->assertEquals($expected, $range->getDatePeriod());
    }

    /** @test */
    public function getIterator()
    {
        $range = new DateRange(['2015-12-01', '2015-12-04']);
        $results = [];
        foreach($range as $d) {
            $results[] = $d;
        }
        $expected = [
            new DateTime('2015-12-01'),
            new DateTime('2015-12-02'),
            new DateTime('2015-12-03'),
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
}
