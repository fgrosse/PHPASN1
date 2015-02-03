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
use FG\ASN1\Universal\ObjectDescriptor;

class ObjectDescriptorTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new ObjectDescriptor("NumericString character abstract syntax");
        $this->assertEquals(Identifier::OBJECT_DESCRIPTOR, $object->getType());
    }

    public function testContent()
    {
        $object = new ObjectDescriptor("PrintableString character abstract syntax");
        $this->assertEquals("PrintableString character abstract syntax", $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = "Basic Encoding of a single ASN.1 type";
        $object = new ObjectDescriptor($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = "Basic Encoding of a single ASN.1 type";
        $expectedType = chr(Identifier::OBJECT_DESCRIPTOR);
        $expectedLength = chr(strlen($string));

        $object = new ObjectDescriptor($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new ObjectDescriptor("PrintableString character abstract syntax");
        $binaryData = $originalobject->getBinary();
        $parsedObject = ObjectDescriptor::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new ObjectDescriptor("NumericString character abstract syntax");
        $originalobject2 = new ObjectDescriptor("Basic Encoding of a single ASN.1 type");

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = ObjectDescriptor::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(41, $offset);
        $parsedObject = ObjectDescriptor::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(80, $offset);
    }
}
