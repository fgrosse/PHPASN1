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
 
class ASN_BooleanTest extends PHPASN1TestCase {
    
    public function testGetType() {
        $object = new ASN_Boolean(true);
        $this->assertEquals(Identifier::BOOLEAN, $object->getType());
    }
    
    public function testContent() {
        $object = new ASN_Boolean(true);
        $this->assertEquals('TRUE', $object->getContent());
        
        $object = new ASN_Boolean(false);
        $this->assertEquals('FALSE', $object->getContent());
    }
    
    public function testGetObjectLength() {
        $object = new ASN_Boolean(true);
        $this->assertEquals(3, $object->getObjectLength());
        
        $object = new ASN_Boolean(false);
        $this->assertEquals(3, $object->getObjectLength());
    }
    
    public function testGetBinary() {
        $expectedType = chr(Identifier::BOOLEAN);
        $expectedLength = chr(0x01);
       
        $object = new ASN_Boolean(true);
        $expectedContent = chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
        
        $object = new ASN_Boolean(false);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }
    
    /**
     * @depends testGetBinary
     */
    public function testFromBinary() {        
        $originalobject = new ASN_Boolean(true);
        $binaryData = $originalobject->getBinary();
        $parsedObject = ASN_Boolean::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
        
        $originalobject = new ASN_Boolean(false);
        $binaryData = $originalobject->getBinary();
        $parsedObject = ASN_Boolean::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);         
    }
    
    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset() {
        $originalobject1 = new ASN_Boolean(true);
        $originalobject2 = new ASN_Boolean(false);
        
        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();
        
        $offset = 0;        
        $parsedObject = ASN_Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = ASN_Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(6, $offset);
    }
    
    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Boolean should not have a length other than one. Extracted length was 2
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01() {
        $binaryData  = chr(Identifier::BOOLEAN);
        $binaryData .= chr(0x02);
        $binaryData .= chr(0xFF);        
        ASN_Boolean::fromBinary($binaryData);        
    }
    
    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Boolean should not have a length other than one. Extracted length was 0
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength02() {
        $binaryData  = chr(Identifier::BOOLEAN);
        $binaryData .= chr(0x00);
        $binaryData .= chr(0xFF);        
        ASN_Boolean::fromBinary($binaryData);    
    }
    
}
    