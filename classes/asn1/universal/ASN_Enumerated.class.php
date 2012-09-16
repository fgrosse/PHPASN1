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

class ASN_Enumerated extends ASN_Integer {
            
    public function getType() {
        return Identifier::ENUMERATED;
    }    
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::ENUMERATED, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);        
                
        $isNegative = (ord($binaryData[$offsetIndex]) & 0x80) != 0x00;        
        $number = ord($binaryData[$offsetIndex++]) & 0x7F;
        
        for ($i=0; $i<$contentLength-1; $i++) {
            $number = ($number << 8) + ord($binaryData[$offsetIndex++]);
        }
        
        if($isNegative) {
            $number -= pow(2, 8*$contentLength-1);            
        }                
        
        $parsedObject = new self($number);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
}
?>