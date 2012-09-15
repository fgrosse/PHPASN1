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
    
}
    