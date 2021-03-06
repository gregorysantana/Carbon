<?php

/*
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Carbon;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use DateTimeZone;
use Tests\AbstractTestCase;

class InstanceTest extends AbstractTestCase
{
    public function testInstanceFromDateTime()
    {
        $dating = Carbon::instance(DateTime::createFromFormat('Y-m-d H:i:s', '1975-05-21 22:32:11'));
        $this->assertCarbon($dating, 1975, 5, 21, 22, 32, 11);
        $dating = Carbon::parse(DateTime::createFromFormat('Y-m-d H:i:s', '1975-05-21 22:32:11'));
        $this->assertCarbon($dating, 1975, 5, 21, 22, 32, 11);
    }

    public function testInstanceFromCarbon()
    {
        $dating = Carbon::instance(Carbon::create(1975, 5, 21, 22, 32, 11));
        $this->assertCarbon($dating, 1975, 5, 21, 22, 32, 11);
    }

    public function testInstanceFromDateTimeKeepsTimezoneName()
    {
        $dating = Carbon::instance(DateTime::createFromFormat('Y-m-d H:i:s', '1975-05-21 22:32:11')->setTimezone(new DateTimeZone('America/Vancouver')));
        $this->assertSame('America/Vancouver', $dating->tzName);
    }

    public function testInstanceFromCarbonKeepsTimezoneName()
    {
        $dating = Carbon::instance(Carbon::create(1975, 5, 21, 22, 32, 11)->setTimezone(new \DateTimeZone('America/Vancouver')));
        $this->assertSame('America/Vancouver', $dating->tzName);
    }

    public function testInstanceFromDateTimeKeepsMicros()
    {
        $micro = 254687;
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s.u', '2014-02-01 03:45:27.'.$micro);
        $carbon = Carbon::instance($datetime);
        $this->assertSame($micro, $carbon->micro);
    }

    public function testInstanceFromCarbonKeepsMicros()
    {
        $micro = 254687;
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s.u', '2014-02-01 03:45:27.'.$micro);
        $carbon = Carbon::instance($carbon);
        $this->assertSame($micro, $carbon->micro);
    }

    public function testInstanceStateSetBySetStateMethod()
    {
        $carbon = Carbon::__set_state([
            'date' => '2017-05-18 13:02:15.273420',
            'timezone_type' => 3,
            'timezone' => 'UTC',
        ]);
        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame('2017-05-18 13:02:15.273420', $carbon->format('Y-m-d H:i:s.u'));
    }

    public function testInstanceStateSetBySetStateString()
    {
        $carbon = Carbon::__set_state('2017-05-18 13:02:15.273420');
        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertSame('2017-05-18 13:02:15.273420', $carbon->format('Y-m-d H:i:s.u'));
    }

    public function testDeserializationOccursCorrectly()
    {
        $carbon = new Carbon('2017-06-27 13:14:15.000000');
        $serialized = 'return '.var_export($carbon, true).';';
        $deserialized = eval($serialized);

        $this->assertInstanceOf(Carbon::class, $deserialized);
    }

    public function testMutableConversions()
    {
        $carbon = new Carbon('2017-06-27 13:14:15.123456', 'Europe/Paris');
        $copy = $carbon->toImmutable();

        self::assertEquals($copy, $carbon);
        self::assertNotSame($copy, $carbon);
        self::assertTrue($copy->isImmutable());
        self::assertFalse($copy->isMutable());
        self::assertSame('2017-06-27 13:14:15.123456', $copy->format(CarbonInterface::MOCK_DATETIME_FORMAT));
        self::assertSame('Europe/Paris', $copy->tzName);
        self::assertNotSame($copy, $copy->modify('+1 day'));

        $copy = $carbon->toMutable();

        self::assertEquals($copy, $carbon);
        self::assertNotSame($copy, $carbon);
        self::assertFalse($copy->isImmutable());
        self::assertTrue($copy->isMutable());
        self::assertSame('2017-06-27 13:14:15.123456', $copy->format(CarbonInterface::MOCK_DATETIME_FORMAT));
        self::assertSame('Europe/Paris', $copy->tzName);
        self::assertSame($copy, $copy->modify('+1 day'));
    }
}
