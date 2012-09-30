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
 
class ASN_NullTest extends PHPASN1TestCase {
    
    public function testGetType() {
        $object = new ASN_Null();
        $this->assertEquals(Identifier::NULL, $object->getType());
    }
    
    public function testContent() {
        $object = new ASN_Null();
        $this->assertEquals('NULL', $object->getContent());
    }
    
    public function testGetObjectLength() {
        $object = new ASN_Null();
        $this->assertEquals(2, $object->getObjectLength());
    }
    
    public function testGetBinary() {
        $object = new ASN_Null();
        $expectedType = chr(Identifier::NULL);
        $expectedLength = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength, $object->getBinary());
    }
    
    /**
     * @depends testGetBinary
     */
    public function testFromBinary() {        
        $originalobject = new ASN_Null();
        $binaryData = $originalobject->getBinary();
        $parsedObject = ASN_Null::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }
    
    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset() {
        $originalobject1 = new ASN_Null();
        $originalobject2 = new ASN_Null();
        
        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();
        
        $offset = 0;        
        $parsedObject = ASN_Null::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(2, $offset);
        $parsedObject = ASN_Null::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(4, $offset);
    }    
    
    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Null should not have a length other than zero. Extracted length was 1
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01() {
        $binaryData  = chr(Identifier::NULL);
        $binaryData .= chr(0x01);
        ASN_Null::fromBinary($binaryData);        
    }
    
}
    