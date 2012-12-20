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

class SAN_IPAddress extends ASN_Object implements Parseable {
       
    const IDENTIFIER = 0x87; // not sure yet why this is the identifier used in SAN extensions    
        
    public function __construct($ipAddressString) {
        $this->value = $ipAddressString;
    }
    
    public static function getType() {
        return self::IDENTIFIER;
    }
    
    protected function calculateContentLength() {
        return 4;
    }

    protected function getEncodedValue() {
        $ipParts = explode('.', $this->value);
        $binary  = chr($ipParts[0]);
        $binary .= chr($ipParts[1]);
        $binary .= chr($ipParts[2]);
        $binary .= chr($ipParts[3]);
        return $binary;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], self::IDENTIFIER, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        if($contentLength != 4) {
            throw new ASN1ParserException("A PHPASN1\SAN_IPAddress should have a content length of 4. Extracted length was {$contentLength}", $offsetIndex);
        }

        $ipAddressString = ord($binaryData[$offsetIndex++]) . '.';
        $ipAddressString .= ord($binaryData[$offsetIndex++]). '.';
        $ipAddressString .= ord($binaryData[$offsetIndex++]). '.';
        $ipAddressString .= ord($binaryData[$offsetIndex++]);
        
        $parsedObject = new SAN_IPAddress($ipAddressString);
        $parsedObject->getObjectLength();        
        return $parsedObject;
    }
}
?>
