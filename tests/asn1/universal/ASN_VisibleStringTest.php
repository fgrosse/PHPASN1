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

class ASN_VisibleStringTest extends PHPASN1TestCase {
    
    public function testGetType() {
        $object = new ASN_VisibleString("Hello World");
        $this->assertEquals(Identifier::VISIBLE_STRING, $object->getType());
    }
    
    public function testContent() {
        $object = new ASN_VisibleString("Hello World");
        $this->assertEquals("Hello World", $object->getContent());
        
        $object = new ASN_VisibleString("");
        $this->assertEquals("", $object->getContent());
        
        $object = new ASN_VisibleString("             ");
        $this->assertEquals("             ", $object->getContent());
    }
    
    
    public function testGetObjectLength() {
        $string = "Hello World";
        $object = new ASN_VisibleString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }
    
    public function testGetBinary() {
        $string = "Hello World";
        $expectedType = chr(Identifier::VISIBLE_STRING);
        $expectedLength = chr(strlen($string));
       
        $object = new ASN_VisibleString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());        
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary() {
        $originalobject = new ASN_VisibleString("Hello World");
        $binaryData = $originalobject->getBinary();
        $parsedObject = ASN_VisibleString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset() {
        $originalobject1 = new ASN_VisibleString("Hello ");
        $originalobject2 = new ASN_VisibleString(" World");
        
        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();
        
        $offset = 0;
        $parsedObject = ASN_VisibleString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = ASN_VisibleString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}
    