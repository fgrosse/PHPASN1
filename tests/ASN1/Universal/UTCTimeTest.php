<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\ASN1\Universal;

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\UTCTime;

class UTCTimeTest extends ASN1TestCase
{
    private $UTC;

    public function setUp()
    {
        $this->UTC = new \DateTimeZone('UTC');
    }

    public function testGetType()
    {
        $object = new UTCTime();
        $this->assertEquals(Identifier::UTC_TIME, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new UTCTime();
        $this->assertEquals(chr(Identifier::UTC_TIME), $object->getIdentifier());
    }

    public function testGetContent()
    {
        $now = new \DateTime();
        $now->setTimezone($this->UTC);
        $object = new UTCTime();
        $content = $object->getContent();
        $this->assertTrue($content instanceof \DateTime);
        $this->assertEquals($now->format(DATE_RFC3339), $content->format(DATE_RFC3339));

        $timeString = '2012-09-23 20:27';
        $dateTime = new \DateTime($timeString, $this->UTC);
        $object = new UTCTime($timeString);
        $content = $object->getContent();
        $this->assertTrue($content instanceof \DateTime);
        $this->assertEquals($dateTime->format(DATE_RFC3339), $content->format(DATE_RFC3339));
    }

    public function testGetObjectLength()
    {
        $object = new UTCTime();
        $expectedSize = 2 + 13; // Identifier + length + YYMMDDHHmmssZ
        $this->assertEquals($expectedSize, $object->getObjectLength());

        $object = new UTCTime('2012-09-23');
        $this->assertEquals($expectedSize, $object->getObjectLength());

        $object = new UTCTime('1987-01-15 12:12:16');
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::UTC_TIME);
        $expectedLength = chr(13);

        $object = new UTCTime();
        $now = new \DateTime();
        $now->setTimezone($this->UTC);
        $expectedContent  = $now->format('ymdHis').'Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $dateString = '2012-09-23';
        $object = new UTCTime($dateString);
        $date = new \DateTime($dateString, $this->UTC);
        $expectedContent  = $date->format('ymdHis').'Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $dateString = '1987-01-15 12:12';
        $object = new UTCTime($dateString);
        $date = new \DateTime($dateString, $this->UTC);
        $expectedContent  = $date->format('ymdHis').'Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithDEREncoding()
    {
        $dateTime = new \DateTime('2012-09-23 20:23:16', $this->UTC);
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '120923202316Z';
        $parsedObject = UTCTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithBEREncodingWithoutSecondsInUTC()
    {
        $dateTime = new \DateTime('1987-01-15 13:15:00', $this->UTC);
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '8701151315Z';
        $parsedObject = UTCTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithBEREncodingWithoutSecondsInOtherTimeZone()
    {
        $dateTime = new \DateTime('2012-09-23 22:13:00', $this->UTC);
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '1209231613-0600';
        $parsedObject = UTCTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());

        $dateTime = new \DateTime('2012-09-23 22:13:00', $this->UTC);
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '1209240213+0400';
        $parsedObject = UTCTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithBEREncodingWithSecondsInOtherTimeZone()
    {
        $dateTime = new \DateTime('2012-09-23 22:13:32', $this->UTC);
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '120923161332-0600';
        $parsedObject = UTCTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());

        $dateTime = new \DateTime('2012-09-23 22:13:32', $this->UTC);
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '120924021332+0400';
        $parsedObject = UTCTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testFromBinaryWithDEREncoding
     * @depends testFromBinaryWithBEREncodingWithoutSecondsInUTC
     * @depends testFromBinaryWithBEREncodingWithoutSecondsInOtherTimeZone
     * @depends testFromBinaryWithBEREncodingWithSecondsInOtherTimeZone
     */
    public function testFromBinaryWithOffset()
    {
        $binaryData  = chr(Identifier::UTC_TIME);
        $binaryData .= chr(11);
        $binaryData .= '1209231613Z';
        $dateTime1 = new \DateTime('2012-09-23 16:13:00', $this->UTC);
        $binaryData .= chr(Identifier::UTC_TIME);
        $binaryData .= chr(13);
        $binaryData .= '120923180030Z';
        $dateTime2 = new \DateTime('2012-09-23 18:00:30', $this->UTC);
        $binaryData .= chr(Identifier::UTC_TIME);
        $binaryData .= chr(17);
        $binaryData .= '120924021332+0400';
        $dateTime3 = new \DateTime('2012-09-23 22:13:32', $this->UTC);

        $offset = 0;
        $parsedObject = UTCTime::fromBinary($binaryData, $offset);
        $this->assertEquals($dateTime1, $parsedObject->getContent());
        $this->assertEquals(13, $offset);
        $parsedObject = UTCTime::fromBinary($binaryData, $offset);
        $this->assertEquals($dateTime2, $parsedObject->getContent());
        $this->assertEquals(28, $offset);
        $parsedObject = UTCTime::fromBinary($binaryData, $offset);
        $this->assertEquals($dateTime3, $parsedObject->getContent());
        $this->assertEquals(47, $offset);
    }

    public function testToString()
    {
        $object = new UTCTime('2012-09-23');
        $this->assertEquals("2012-09-23\t00:00:00", $object->__toString());

        $object = new UTCTime('2012-09-23 22:13:43');
        $this->assertEquals("2012-09-23\t22:13:43", $object->__toString());
    }
}
