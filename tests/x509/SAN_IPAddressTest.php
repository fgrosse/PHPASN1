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

require_once(dirname(__FILE__) . '/../PHPASN1TestCase.class.php');

class SAN_IPAddressTest extends PHPASN1TestCase {
    
    public function testGetType() {
        $object = new SAN_IPAddress("192.168.0.1");
        $this->assertEquals(0x87, $object->getType());
    }
    
    public function testGetContent() {
        $object = new SAN_IPAddress("192.168.0.1");
        $this->assertEquals("192.168.0.1", $object->getContent());        
    }
    
    
    public function testGetObjectLength() {
        $object = new SAN_IPAddress("192.168.0.1");
        $expectedSize = 6; // Type + Length + 4 Byte
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }
    
    public function testGetBinary() {
        $expectedType = chr(0x87);
        $expectedLength = chr(4);
        $ipAddress  = chr(192);
        $ipAddress .= chr(168);
        $ipAddress .= chr(0);
        $ipAddress .= chr(1);
        $object = new SAN_IPAddress("192.168.0.1");
        $this->assertEquals($expectedType.$expectedLength.$ipAddress, $object->getBinary());        
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary() {
        $originalobject = new SAN_IPAddress("192.168.0.1");
        $binaryData = $originalobject->getBinary();
        $parsedObject = SAN_IPAddress::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }
    
    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: A PHPASN1\SAN_IPAddress should have a content length of 4. Extracted length was 2
     * @depends testFromBinary
     */
    public function testFromBinaryWithWrongLengthThrowsException() {
        $binaryData  = chr(0x87);
        $binaryData .= chr(2);
        $binaryData .= chr(192);
        $binaryData .= chr(168);                
        $parsedObject = SAN_IPAddress::fromBinary($binaryData);
        
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset() {
        $originalobject1 = new SAN_IPAddress("192.168.0.1");
        $originalobject2 = new SAN_IPAddress("10.65.32.123");
        
        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();
        
        $offset = 0;
        $parsedObject = SAN_IPAddress::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(6, $offset);
        $parsedObject = SAN_IPAddress::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(12, $offset);
    }    
}
    