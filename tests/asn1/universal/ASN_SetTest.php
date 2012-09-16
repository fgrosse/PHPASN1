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
 
namespace PHPASN1;

require_once(dirname(__FILE__) . '/../../PHPASN1TestCase.class.php');

class ASN_SetTest extends PHPASN1TestCase {

    public function testGetType() {
        $object = new ASN_Set();
        $this->assertEquals(Identifier::SET, $object->getType());
    }

    public function testContent() {
        $child1 = new ASN_Integer(123);
        $child2 = new ASN_PrintableString("Hello Wold");
        $child3 = new ASN_Boolean(true);
        $object = new ASN_Set($child1, $child2, $child3);
        
        $this->assertEquals(array($child1, $child2, $child3), $object->getContent());
    }

    public function testGetObjectLength() {
        $child1 = new ASN_Boolean(true);
        $object = new ASN_Set($child1);
        $this->assertEquals(5, $object->getObjectLength());
        
        $child1 = new ASN_Integer(123);        
        $child2 = new ASN_Boolean(true);
        $object = new ASN_Set($child1, $child2);
        $this->assertEquals(8, $object->getObjectLength());
        
        $child1 = new ASN_Integer(123);
        $child2 = new ASN_PrintableString("Hello Wold");
        $child3 = new ASN_Boolean(true);
        $object = new ASN_Set($child1, $child2, $child3);        
        $this->assertEquals(20, $object->getObjectLength());
    }

    public function testGetBinary() {
        $child1 = new ASN_Boolean(true);
        $object = new ASN_Set($child1);
        
        $expectedType = chr(Identifier::SET);
        $expectedLength = chr(0x03);
        $expectedContent = $child1->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
        
        $child1 = new ASN_Integer(123);        
        $child2 = new ASN_Boolean(true);
        $object = new ASN_Set($child1, $child2);
        $expectedLength = chr(0x06);
        $expectedContent  = $child1->getBinary();
        $expectedContent .= $child2->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary() {        
        $originalobject = new ASN_Set(
            new ASN_Boolean(true),
            new ASN_Integer(1234567)
        );
        $binaryData = $originalobject->getBinary();
        $parsedObject = ASN_Set::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
/*    public function testFromBinaryWithOffset() {
        $originalobject1 = new ASN_Set(
            new ASN_Boolean(true),
            new ASN_Integer(123)
        );
        $originalobject2 = new ASN_Set(
            new ASN_Integer(64),
            new ASN_Boolean(false)
        );
        
        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();
        
        $offset = 0;        
        $parsedObject = ASN_Set::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = ASN_Set::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }*/
    
}
