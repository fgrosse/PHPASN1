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

namespace FG\Test\X509;

use FG\Test\ASN1TestCase;
use FG\X509\SAN\DNSName;

class DNSNameTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new DNSName("test.corvespace.de");
        $this->assertEquals(0x82, $object->getType());
    }

    public function testGetContent()
    {
        $object = new DNSName("test.corvespace.de");
        $this->assertEquals("test.corvespace.de", $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = "test.corvespace.de";
        $object = new DNSName($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = "test.corvespace.de";
        $expectedType = chr(0x82);
        $expectedLength = chr(strlen($string));

        $object = new DNSName($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new DNSName("test.corvespace.de");
        $binaryData = $originalobject->getBinary();
        $parsedObject = DNSName::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new DNSName("test.corvespace.de");
        $originalobject2 = new DNSName("superdomain.com");

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = DNSName::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(20, $offset);
        $parsedObject = DNSName::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(37, $offset);
    }
}
