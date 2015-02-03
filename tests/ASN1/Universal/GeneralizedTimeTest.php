<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2012
 *
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace FG\Test\ASN1\Universal;

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\GeneralizedTime;

class GeneralizedTimeTest extends ASN1TestCase
{

    private $UTC;

    public function setUp()
    {
        $this->UTC = new \DateTimeZone('UTC');
    }

    public function testGetType()
    {
        $object = new GeneralizedTime();
        $this->assertEquals(Identifier::GENERALIZED_TIME, $object->getType());
    }

    public function testGetContent()
    {
        $now = new \DateTime();
        $now->setTimezone($this->UTC);
        $object = new GeneralizedTime();
        $content = $object->getContent();
        $this->assertTrue($content instanceof \DateTime);
        $this->assertEquals($now->format(DATE_RFC3339), $content->format(DATE_RFC3339));

        $timeString = '2012-09-23 20:27';
        $dateTime = new \DateTime($timeString, $this->UTC);
        $object = new GeneralizedTime($timeString);
        $content = $object->getContent();
        $this->assertTrue($content instanceof \DateTime);
        $this->assertEquals($dateTime->format(DATE_RFC3339), $content->format(DATE_RFC3339));
    }

    public function testGetObjectLength()
    {
        $object = new GeneralizedTime();
        $expectedSize = 2 + 15; // Identifier + length + YYYYMMDDHHmmSSZ
        $this->assertEquals($expectedSize, $object->getObjectLength());

        // without specified daytime
        $object = new GeneralizedTime('2012-09-23');
        $this->assertEquals($expectedSize, $object->getObjectLength());

        // with fractional-seconds elements
        $object = new GeneralizedTime('2012-09-23 22:21:03.5435440');
        $this->assertEquals($expectedSize + 7, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::GENERALIZED_TIME);
        $expectedLength = chr(15); // YYYYMMDDHHmmSSZ

        $object = new GeneralizedTime();
        $now = new \DateTime();
        $now->setTimezone($this->UTC);
        $expectedContent  = $now->format('YmdHis').'Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $dateString = '2012-09-23';
        $object = new GeneralizedTime($dateString);
        $date = new \DateTime($dateString, $this->UTC);
        $expectedContent  = '20120923000000Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $dateString = '1987-01-15 12:12';
        $object = new GeneralizedTime($dateString);
        $date = new \DateTime($dateString, $this->UTC);
        $expectedContent  = '19870115121200Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $dateString = '2008-07-01 22:35:17.02';
        $expectedLength = chr(18);
        $object = new GeneralizedTime($dateString);
        $date = new \DateTime($dateString, $this->UTC);
        $expectedContent  = '20080701223517.02Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $dateString = '2008-07-01 22:35:17.024540';
        $expectedLength = chr(21);
        $object = new GeneralizedTime($dateString);
        $date = new \DateTime($dateString, $this->UTC);
        $expectedContent  = '20080701223517.02454Z';
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithDEREncoding()
    {
        $dateTime = new \DateTime("2012-09-23 20:23:16", $this->UTC);
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(15);
        $binaryData .= '20120923202316Z';
        $parsedObject = GeneralizedTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithDEREncodingAndFractionalSecondsPart()
    {
        $dateTime = new \DateTime("2012-09-23 22:21:03.5435440", $this->UTC);
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(22);
        $binaryData .= '20120923222103.543544Z';
        $parsedObject = GeneralizedTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithBEREncodingWithLocalTimeZone()
    {
        $dateTime = new \DateTime("2012-09-23 20:23:16");
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(14);
        $binaryData .= '20120923202316';
        $parsedObject = GeneralizedTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinaryWithBEREncodingWithOtherTimeZone()
    {
        $dateTime = new \DateTime("2012-09-23 22:13:59", $this->UTC);
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(19);
        $binaryData .= '20120923161359-0600';
        $parsedObject = GeneralizedTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());

        $dateTime = new \DateTime("2012-09-23 22:13:59", $this->UTC);
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(19);
        $binaryData .= '20120924021359+0400';
        $parsedObject = GeneralizedTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }

    /**
     * @depends testFromBinaryWithDEREncodingAndFractionalSecondsPart
     */
    public function testFromBinaryWithBEREncodingWithFractionalSecondsPartAndOtherTimeZone()
    {
        $dateTime = new \DateTime("2012-09-23 22:13:59.525", $this->UTC);
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(23);
        $binaryData .= '20120923161359.525-0600';
        $parsedObject = GeneralizedTime::fromBinary($binaryData);
        $this->assertEquals($dateTime, $parsedObject->getContent());
    }
}
