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
use FG\ASN1\Universal\Integer;

class IntegerTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new Integer(123);
        $this->assertEquals(Identifier::INTEGER, $object->getType());
    }

    public function testContent()
    {
        $object = new Integer(1234);
        $this->assertEquals(1234, $object->getContent());

        $object = new Integer(-1234);
        $this->assertEquals(-1234, $object->getContent());

        $object = new Integer(0);
        $this->assertEquals(0, $object->getContent());

        // test with maximum integer value
        $object = new Integer(PHP_INT_MAX);
        $this->assertEquals(PHP_INT_MAX, $object->getContent());

        // test with minimum integer value by negating the max value
        $object = new Integer(~PHP_INT_MAX);
        $this->assertEquals(~PHP_INT_MAX, $object->getContent());
    }

    public function testGetObjectLength()
    {
        $positiveObj = new Integer(0);
        $expectedSize = 2 + 1;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());

        $positiveObj = new Integer(127);
        $negativeObj = new Integer(-127);
        $expectedSize = 2 + 1;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(128);
        $negativeObj = new Integer(-128);
        $expectedSize = 2 + 2;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x7FFF);
        $negativeObj = new Integer(-0x7FFF);
        $expectedSize = 2 + 2;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x8000);
        $negativeObj = new Integer(-0x8000);
        $expectedSize = 2 + 3;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x7FFFFF);
        $negativeObj = new Integer(-0x7FFFFF);
        $expectedSize = 2 + 3;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x800000);
        $negativeObj = new Integer(-0x800000);
        $expectedSize = 2 + 4;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x7FFFFFFF);
        $negativeObj = new Integer(-0x7FFFFFFF);
        $expectedSize = 2 + 4;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::INTEGER);
        $expectedLength = chr(0x01);

        $object = new Integer(0);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(127);
        $expectedContent = chr(0x7F);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(-127);
        $expectedContent = chr(0x81);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(200);
        $expectedLength = chr(0x02);
        $expectedContent = chr(0x00);
        $expectedContent .= chr(0xC8);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(-546);
        $expectedLength = chr(0x02);
        $expectedContent = chr(0xFD);
        $expectedContent .= chr(0xDE);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(7420);
        $expectedLength   = chr(0x02);
        $expectedContent  = chr(0x1C);
        $expectedContent .= chr(0xFC);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(-1891004);
        $expectedLength   = chr(0x03);
        $expectedContent  = chr(0xE3);
        $expectedContent .= chr(0x25);
        $expectedContent .= chr(0x44);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new Integer(200);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Integer::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new Integer(12345);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Integer::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new Integer(-1891004);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Integer::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new Integer(12345);
        $originalobject2 = new Integer(67890);

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Integer::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(4, $offset);
        $parsedObject = Integer::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(9, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: A FG\ASN1\Universal\Integer should have a content length of at least 1. Extracted length was 0
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01()
    {
        $binaryData  = chr(Identifier::INTEGER);
        $binaryData .= chr(0x00);
        $binaryData .= chr(0xA0);
        Integer::fromBinary($binaryData);
    }
}
