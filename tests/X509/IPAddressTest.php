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
use FG\X509\SAN\IPAddress;

class IPAddressTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new IPAddress("192.168.0.1");
        $this->assertEquals(0x87, $object->getType());
    }

    public function testGetContent()
    {
        $object = new IPAddress("192.168.0.1");
        $this->assertEquals("192.168.0.1", $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new IPAddress("192.168.0.1");
        $expectedSize = 6; // Type + Length + 4 Byte
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(0x87);
        $expectedLength = chr(4);
        $ipAddress  = chr(192);
        $ipAddress .= chr(168);
        $ipAddress .= chr(0);
        $ipAddress .= chr(1);
        $object = new IPAddress("192.168.0.1");
        $this->assertEquals($expectedType.$expectedLength.$ipAddress, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new IPAddress("192.168.0.1");
        $binaryData = $originalobject->getBinary();
        $parsedObject = IPAddress::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: A FG\X509\SAN\IPAddress should have a content length of 4. Extracted length was 2
     * @depends testFromBinary
     */
    public function testFromBinaryWithWrongLengthThrowsException()
    {
        $binaryData  = chr(0x87);
        $binaryData .= chr(2);
        $binaryData .= chr(192);
        $binaryData .= chr(168);
        $parsedObject = IPAddress::fromBinary($binaryData);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new IPAddress("192.168.0.1");
        $originalobject2 = new IPAddress("10.65.32.123");

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = IPAddress::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(6, $offset);
        $parsedObject = IPAddress::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(12, $offset);
    }
}
