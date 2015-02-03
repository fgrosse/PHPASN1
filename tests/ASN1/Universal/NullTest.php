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
use FG\ASN1\Universal\Null;

class NullTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new Null();
        $this->assertEquals(Identifier::NULL, $object->getType());
    }

    public function testContent()
    {
        $object = new Null();
        $this->assertEquals('NULL', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new Null();
        $this->assertEquals(2, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new Null();
        $expectedType = chr(Identifier::NULL);
        $expectedLength = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new Null();
        $binaryData = $originalobject->getBinary();
        $parsedObject = Null::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new Null();
        $originalobject2 = new Null();

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Null::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(2, $offset);
        $parsedObject = Null::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(4, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Null should not have a length other than zero. Extracted length was 1
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01()
    {
        $binaryData  = chr(Identifier::NULL);
        $binaryData .= chr(0x01);
        Null::fromBinary($binaryData);
    }
}
