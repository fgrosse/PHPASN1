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
 
class ASN_ObjectTest extends PHPASN1TestCase {
    
   public function testCalculateNumberOfLengthOctets() {
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');       
       $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 32);
       $this->assertEquals(1, $calculatedNrOfLengthOctets);
       
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
       $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 0);
       $this->assertEquals(1, $calculatedNrOfLengthOctets);
       
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
       $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 127);
       $this->assertEquals(1, $calculatedNrOfLengthOctets);
       
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
       $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 128);
       $this->assertEquals(2, $calculatedNrOfLengthOctets);
       
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
       $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 255);
       $this->assertEquals(2, $calculatedNrOfLengthOctets);
       
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
       $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 1025);
       $this->assertEquals(3, $calculatedNrOfLengthOctets);
    }
    
    /**
     * For the real parsing tests look in the test cases of each single ASn object.
     */
    public function testFromBinary() {
       // Bit String
       $binaryData  = chr(Identifier::BITSTRING);
       $binaryData .= chr(0x03);
       $binaryData .= chr(0x05);
       $binaryData .= chr(0xFF);
       $binaryData .= chr(0xA0);

       $expectedObject = new ASN_BitString(0xFFA0, 5);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_BitString);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       $this->assertEquals($expectedObject->getNumberOfUnusedBits(), $parsedObject->getNumberOfUnusedBits());
       
       // Octet String
       $binaryData  = chr(Identifier::OCTETSTRING);
       $binaryData .= chr(0x02);
       $binaryData .= chr(0xFF);
       $binaryData .= chr(0xA0);

       $expectedObject = new ASN_OctetString(0xFFA0);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_OctetString);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Boolean
       $binaryData  = chr(Identifier::BOOLEAN);
       $binaryData .= chr(0x01);
       $binaryData .= chr(0xFF);
       
       $expectedObject = new ASN_Boolean(true);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_Boolean);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Enumerated
       $binaryData  = chr(Identifier::ENUMERATED);
       $binaryData .= chr(0x01);
       $binaryData .= chr(0x03);
       
       $expectedObject = new ASN_Enumerated(3);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_Enumerated);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // IA5 String
       $string = 'Hello Foo World!!!11EinsEins!1';
       $binaryData  = chr(Identifier::IA5_STRING);
       $binaryData .= chr(strlen($string));
       $binaryData .= $string;
       
       $expectedObject = new ASN_IA5String($string);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_IA5String);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Integer       
       $binaryData  = chr(Identifier::INTEGER);
       $binaryData .= chr(0x01);
       $binaryData .= chr(123);
       
       $expectedObject = new ASN_Integer(123);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_Integer);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Null       
       $binaryData  = chr(Identifier::NULL);
       $binaryData .= chr(0x00);
       
       $expectedObject = new ASN_Null();
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_Null);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Object Identifier       
       $binaryData  = chr(Identifier::OBJECT_IDENTIFIER);
       $binaryData .= chr(0x02);
       $binaryData .= chr(1 * 40 + 2);
       $binaryData .= chr(3);
       
       $expectedObject = new ASN_ObjectIdentifier('1.2.3');
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_ObjectIdentifier);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Printable String
       $string = 'This is a test string. #?!%&""';
       $binaryData  = chr(Identifier::PRINTABLE_STRING);
       $binaryData .= chr(strlen($string));
       $binaryData .= $string;
       
       $expectedObject = new ASN_PrintableString($string);
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_PrintableString);
       $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
       
       // Sequence       
       $binaryData  = chr(Identifier::SEQUENCE);
       $binaryData .= chr(0x06);
       $binaryData .= chr(Identifier::BOOLEAN);
       $binaryData .= chr(0x01);
       $binaryData .= chr(0x00);
       $binaryData .= chr(Identifier::INTEGER);
       $binaryData .= chr(0x01);
       $binaryData .= chr(0x03);
       
       $expectedChild1 = new ASN_Boolean(false);
       $expectedChild2 = new ASN_Integer(0x03); 
       
       $expectedObject = new ASN_Sequence(
           $expectedChild1,
           $expectedChild2
       );
       $parsedObject = ASN_Object::fromBinary($binaryData);
       $this->assertTrue($parsedObject instanceof ASN_Sequence);
       $this->assertEquals(2, $parsedObject->getNumberofChildren());
       
       $children = $parsedObject->getChildren();
       $child1 = $children[0];
       $child2 = $children[1];
       $this->assertEquals($expectedChild1->getContent(), $child1->getContent());
       $this->assertEquals($expectedChild2->getContent(), $child2->getContent());
    }

    public function testGetTypeName() {
        for ($i=0x0; $i < 0x1E; $i++) { 
            $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
            $object->expects($this->any())
                   ->method('getType')
                   ->will($this->returnValue($i));
            $this->assertEquals(Identifier::getName($i), $object->getTypeName());
        }
    }
}
    