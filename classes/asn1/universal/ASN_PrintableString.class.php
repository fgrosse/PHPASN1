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

class ASN_PrintableString extends ASN_Object implements Parseable {
        
    public function __construct($value) {        
        $this->value = $value;
    }
    
    public function getType() {
        return Identifier::PRINTABLE_STRING;
    }
    
    protected function calculateContentLength() {
        return strlen($this->value);
    }
    
    protected function getEncodedValue() {
        return $this->value;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex++], Identifier::PRINTABLE_STRING, $offsetIndex);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);        
        $string = substr($binaryData, $offsetIndex, $contentLength);
        $offsetIndex += $contentLength;
        
        $parsedObject = new self($string);
        $parsedObject->setContentLength($contentLength);        
        return $parsedObject;
    }
           
}
?>