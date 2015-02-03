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

namespace FG\Test\ASN1\Composite;

use FG\Test\ASN1TestCase;
use FG\ASN1\OID;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\UTF8String;
use FG\ASN1\Composite\RelativeDistinguishedName;

class RelativeDistinguishedNameTest extends ASN1TestCase
{

   public function testGetType()
   {
       $object = new RelativeDistinguishedName(OID::COMMON_NAME, new UTF8String("Friedrich Große"));
       $this->assertEquals(Identifier::SET, $object->getType());
   }

    public function testContent()
    {
        $oid = OID::COMMON_NAME;
        $string = 'Friedrich Große';
        $object = new RelativeDistinguishedName($oid, new UTF8String($string));

        $this->assertEquals(OID::getName($oid).": {$string}", $object->getContent());
    }
    /*
    public function testGetObjectLength() {
        $object = new Boolean(true);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Boolean(false);
        $this->assertEquals(3, $object->getObjectLength());
    }

    public function testGetBinary() {
        $expectedType = chr(Identifier::BOOLEAN);
        $expectedLength = chr(0x01);

        $object = new Boolean(true);
        $expectedContent = chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Boolean(false);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    /*public function testFromBinary() {
        $originalobject = new Boolean(true);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Boolean::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new Boolean(false);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Boolean::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }*/

    /**
     * @depends testFromBinary
     */
    /*public function testFromBinaryWithOffset() {
        $originalobject1 = new Boolean(true);
        $originalobject2 = new Boolean(false);

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(6, $offset);
    }*/
}
