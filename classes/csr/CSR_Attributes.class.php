<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 * 
 * Copyright © Friedrich Große, Berlin 2013
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

class CSR_Attributes extends ASN_Construct implements Parseable {    
    
    public static function getType() {
        return 0xA0;
    }

    public function addAttribute($objectIdentifier, ASN_Set $attribute) {
        if(is_string($objectIdentifier)) {
            $objectIdentifier = new ASN_ObjectIdentifier($objectIdentifier);
        }
        $attributeSequence = new ASN_Sequence($objectIdentifier, $attribute);
        $attributeSequence->getNumberOfLengthOctets();  // length and number of length octets is calculated
        $this->addChild($attributeSequence);
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {
        self::parseIdentifier($binaryData[$offsetIndex], 0xA0, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $octetsToRead = $contentLength;
        
        $parsedObject = new CSR_Attributes();        
        while($octetsToRead > 0) {
            $initialOffset = $offsetIndex; // used to calculate how much bits have been read
            self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
            $sequenceContentLength = self::parseContentLength($binaryData, $offsetIndex);
            
            $objectIDentifier = ASN_ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            $oidString = $objectIDentifier->getContent();
            if($oidString == OID::PKCS9_EXTENSION_REQUEST) {                
                $attribute = CertificateExtensions::fromBinary($binaryData, $offsetIndex);
            }
            else {
                $attribute = ASN_Object::fromBinary($binaryData, $offsetIndex);
            }

            $parsedObject->addAttribute($objectIDentifier, $attribute);
            $octetsToRead -= ($offsetIndex - $initialOffset);            
        }

        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
}
?>