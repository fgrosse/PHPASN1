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

class ASN_Null extends ASN_Object implements Parseable {
        
    public static function getType() {
        return Identifier::NULL;
    }
    
    protected function calculateContentLength() {
        return 0;   
    }
    
    protected function getEncodedValue() {            
        return null;
    }       
    
    public function getContent() {
        return 'NULL';
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::NULL, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        
        if($contentLength != 0) {
            throw new ASN1ParserException("An ASN.1 Null should not have a length other than zero. Extracted length was {$contentLength}", $offsetIndex);
        }        
        
        $parsedObject = new self();
        $parsedObject->setContentLength(0);        
        return $parsedObject;
    }
}
?>