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
use FG\ASN1\Universal\RelativeObjectIdentifier;

class RelativeObjectIdentifierTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new RelativeObjectIdentifier('8751.3.2');
        $this->assertEquals(Identifier::RELATIVE_OID, $object->getType());
    }

    public function testContent()
    {
        $object = new RelativeObjectIdentifier('8751.3.2');
        $this->assertEquals('8751.3.2', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new RelativeObjectIdentifier('1.2.3');
        $this->assertEquals(2+3, $object->getObjectLength());

        $object = new RelativeObjectIdentifier('1.2.250.1.16.9');
        $this->assertEquals(2+7, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new RelativeObjectIdentifier('8571.3.2');
        $expectedType     = chr(Identifier::RELATIVE_OID);
        $expectedLength   = chr(0x04);
        $expectedContent  = chr(0xC2);
        $expectedContent .= chr(0x7B);
        $expectedContent .= chr(0x03);
        $expectedContent .= chr(0x02);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new RelativeObjectIdentifier('8571.3.2');
        $binaryData = $originalobject->getBinary();
        $parsedObject = RelativeObjectIdentifier::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new RelativeObjectIdentifier('8571.3.2');
        $originalobject2 = new RelativeObjectIdentifier('45.2.3455.1');

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = RelativeObjectIdentifier::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(6, $offset);
        $parsedObject = RelativeObjectIdentifier::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(13, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 4: Malformed ASN.1 Relative Object Identifier
     * @depends testFromBinary
     */
    public function testFromBinaryWithMalformedOID()
    {
        $binaryData  = chr(Identifier::RELATIVE_OID);
        $binaryData .= chr(0x03);
        $binaryData .= chr(42);
        $binaryData .= chr(128 | 1);
        $binaryData .= chr(128 | 1);
        RelativeObjectIdentifier::fromBinary($binaryData);
    }
}
