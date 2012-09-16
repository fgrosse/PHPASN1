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
    /*
    public function testCreateLengthPart() {
       $contentLength = 123;
       $nrofLengthOctets = 1;
       $object = $this->getMockForAbstractClass('\PHPASN1\ASN_Object');
       $object->expects($this->any())->method('getContentLength')->will($this->returnValue($contentLength));
       $object->expects($this->any())->method('getNumberOfLengthOctets')->will($this->returnValue($nrofLengthOctets));
       $lengthPart = $this->callMethod($object, 'createLengthPart');
       
       $expectedLengthPart = chr($contentLength);
       $this->assertEquals($expectedLengthPart, $lengthPart);
    }
    */
}
    