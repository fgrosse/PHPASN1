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
use FG\ASN1\Universal\Enumerated;

class EnumeratedTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new Enumerated(1);
        $this->assertEquals(Identifier::ENUMERATED, $object->getType());
    }

    public function testContent()
    {
        $object = new Enumerated(0);
        $this->assertEquals(0, $object->getContent());

        $object = new Enumerated(1);
        $this->assertEquals(1, $object->getContent());

        $object = new Enumerated(512);
        $this->assertEquals(512, $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new Enumerated(0);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Enumerated(127);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Enumerated(128);
        $this->assertEquals(4, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::ENUMERATED);
        $expectedLength = chr(0x01);

        $object = new Enumerated(0);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Enumerated(127);
        $expectedContent = chr(0x7F);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Enumerated(7420);
        $expectedLength   = chr(0x02);
        $expectedContent  = chr(0x1C);
        $expectedContent .= chr(0xFC);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new Enumerated(0);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Enumerated::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new Enumerated(127);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Enumerated::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new Enumerated(200);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Enumerated::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new Enumerated(1);
        $originalobject2 = new Enumerated(2);

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Enumerated::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = Enumerated::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(6, $offset);
    }
}
