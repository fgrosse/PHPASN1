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
use FG\ASN1\Universal\VisibleString;

class VisibleStringTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new VisibleString("Hello World");
        $this->assertEquals(Identifier::VISIBLE_STRING, $object->getType());
    }

    public function testContent()
    {
        $object = new VisibleString("Hello World");
        $this->assertEquals("Hello World", $object->getContent());

        $object = new VisibleString("");
        $this->assertEquals("", $object->getContent());

        $object = new VisibleString("             ");
        $this->assertEquals("             ", $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = "Hello World";
        $object = new VisibleString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = "Hello World";
        $expectedType = chr(Identifier::VISIBLE_STRING);
        $expectedLength = chr(strlen($string));

        $object = new VisibleString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new VisibleString("Hello World");
        $binaryData = $originalobject->getBinary();
        $parsedObject = VisibleString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new VisibleString("Hello ");
        $originalobject2 = new VisibleString(" World");

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = VisibleString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = VisibleString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}
