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
use FG\ASN1\Universal\OctetString;

class OctetStringTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new OctetString('30 14 06 08 2B 06 01 05 05 07 03 01 06 08 2B 06 01 05 05 07 03 02');
        $this->assertEquals(Identifier::OCTETSTRING, $object->getType());
    }

    public function testContent()
    {
        $object = new OctetString('A01200C3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString('A0 12 00 C3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString('a0 12 00 c3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString('A0 12 00 c3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString(0xA01200C3);
        $this->assertEquals('A01200C3', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new OctetString(0x00);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new OctetString(0xFF);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new OctetString(0xA000);
        $this->assertEquals(4, $object->getObjectLength());

        $object = new OctetString(0x3F2001);
        $this->assertEquals(5, $object->getObjectLength());
    }

    public function testGetObjectLengthWithVeryLongOctetString()
    {
        $hexString = '0x'.str_repeat('FF', 1024);
        $object = new OctetString($hexString);
        $this->assertEquals(1+3+1024, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::OCTETSTRING);
        $expectedLength = chr(0x01);

        $object = new OctetString(0xFF);
        $expectedContent = chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new OctetString(0xFFA034);
        $expectedLength = chr(0x03);
        $expectedContent  = chr(0xFF);
        $expectedContent .= chr(0xA0);
        $expectedContent .= chr(0x34);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    public function testGetBinaryForLargeOctetStrings()
    {
        $nrOfBytes = 1024;
        $hexString = '0x'.str_repeat('FF', $nrOfBytes);
        $object = new OctetString($hexString);

        $expectedType = chr(Identifier::OCTETSTRING);
        $expectedLength = chr(0x80 | 0x02);  // long length form: 2 length octets
        $expectedLength .= chr(1024 >> 8);   // first 8 bit of 1025
        $expectedLength .= chr(1024 & 0xFF); // last 8 bit of 1025
        $expectedContent = '';
        for ($i = 0; $i < $nrOfBytes; $i++) {
            $expectedContent .= chr(0xFF);   // content
        }

        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new OctetString(0x12);
        $binaryData = $originalobject->getBinary();
        $parsedObject = OctetString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new OctetString(0x010203A0);
        $binaryData = $originalobject->getBinary();
        $parsedObject = OctetString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new OctetString(0xA0);
        $originalobject2 = new OctetString(0x314510);

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = OctetString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = OctetString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(8, $offset);
    }
}
