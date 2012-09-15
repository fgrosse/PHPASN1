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

class ASN_Integer extends ASN_Object implements Parseable{
    
    private $contentLength;
    
    public function __construct($value) {
        if(is_numeric($value) == false) {
            throw new Exception("Invalid VALUE [{$value}] for ASN1_INTEGER");
        }
        $this->value = $value;
    }
    
    public function getType() {
        return Identifier::INTEGER;
    }
    
    protected function calculateContentLength() {        
        $nrOfOctets = 1; // we need at least one octet
        $tmpValue = abs($this->value);
        while($tmpValue > 127) {
            $tmpValue = $tmpValue >> 8;
            $nrOfOctets++;
        }        
        return $nrOfOctets;    
    }
    
    protected function getEncodedValue() {
        //TODO make this supported huge numbers
        
        $value = $this->value;
        
        if($value < 0) {            
            $value = abs($value);            
            $value = ~$value & (pow(2, 8 * $this->getContentLength()) - 1);
            $value += 1;
        }
        
        $value = dechex($value);
        $result = '';
        if(strlen($value) %2 != 0) {
            // transform values like 1F2 to 01F2
            $value = "0".$value;
        }
         
        while(strlen($value) >= 2) {            
            // get the hex value byte by byte from the string and and add it to binary result
            $result .= chr(hexdec(substr($value,0,2)));
            $value = substr($value,2);
        }
        return $result;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex++], Identifier::INTEGER, $offsetIndex);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);        
        
        if($contentLength < 1) {
            throw new ASN1ParserException("An ASN.1 Integer should have a length of at least one. Extracted length was {$contentLength}", $offsetIndex);
        }
        
        //TODO this will get in trouble with big numbers: rewrite with gmp
        
        $isNegative = (ord($binaryData[$offsetIndex]) & 0x80) != 0x00;        
        $number = ord($binaryData[$offsetIndex++]) & 0x7F;
        
        for ($i=0; $i<$contentLength-1; $i++) {
            $number = ($number << 8) + ord($binaryData[$offsetIndex++]);
        }
        
        if($isNegative) {
            $number -= pow(2, 8*$contentLength-1);            
        }                
        
        $newObject = new self($number);
        $newObject->setContentLength($contentLength);
        
        return $newObject;
    }
}
?>