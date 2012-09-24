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

class ASN_NumericStringTest extends PHPASN1TestCase {
    
    public function testGetType() {
        $object = new ASN_NumericString("1234");
        $this->assertEquals(Identifier::NUMERIC_STRING, $object::getType());
    }
    
    public function testContent() {
        $object = new ASN_NumericString("123 45 67890");
        $this->assertEquals("123 45 67890", $object->getContent());
        
        $object = new ASN_NumericString("             ");
        $this->assertEquals("             ", $object->getContent());
    }
    
    
    public function testGetObjectLength() {
        $string = "123  4 55677 0987";
        $object = new ASN_NumericString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }
    
    public function testGetBinary() {
        $string = "123  4 55677 0987";
        $expectedType = chr(Identifier::NUMERIC_STRING);
        $expectedLength = chr(strlen($string));
       
        $object = new ASN_NumericString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());        
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary() {
        $originalobject = new ASN_NumericString("123 45  5322");
        $binaryData = $originalobject->getBinary();
        $parsedObject = ASN_NumericString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset() {
        $originalobject1 = new ASN_NumericString("1324 0");
        $originalobject2 = new ASN_NumericString("1 2 3 ");
        
        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();
        
        $offset = 0;
        $parsedObject = ASN_NumericString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = ASN_NumericString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
    
    public function testCreateStringWithValidCharacters() {
        $object = new ASN_NumericString('1234');
        $object->getBinary();
        $object = new ASN_NumericString('321 98 76');
        $object->getBinary();        
    }
    
    public function testCreateStringWithInvalidCharacters() {
        $invalidString = "Hello World"; 
        try {
            $object = new ASN_NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }
        
        $invalidString = "123,456"; 
        try {
            $object = new ASN_NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }
        
        $invalidString = "+123456"; 
        try {
            $object = new ASN_NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }
        
        $invalidString = "-123456"; 
        try {
            $object = new ASN_NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }
    }
}
    