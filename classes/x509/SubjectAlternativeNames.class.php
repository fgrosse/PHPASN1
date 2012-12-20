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

class SubjectAlternativeNames extends ASN_Object implements Parseable {
    
    private $objectIdentifier; 
    private $alternativeNamesSequence;
    
    public function __construct() {
        $this->objectIdentifier = new ASN_ObjectIdentifier(OID::CERT_EXT_SUBJECT_ALT_NAME);
        $this->alternativeNamesSequence = new ASN_Sequence();
    }
    
    protected function calculateContentLength() {
        $SANSubsequenceBinaryLength = $this->alternativeNamesSequence->getObjectLength();
        return $this->objectIdentifier->getObjectLength() + 1 + $this->getNumberOfLengthOctets($SANSubsequenceBinaryLength) + $SANSubsequenceBinaryLength;
    }
    
    public static function getType() {
        return Identifier::SEQUENCE;
    }
    
    public function addDomainName(SAN_DNSName $domainName) {
        $this->alternativeNamesSequence->addChild($domainName);
    }
    
    public function addIP(SAN_IPAddress $ip) {
        $this->alternativeNamesSequence->addChild($ip);
    }
    
    public function getContent() {
        return $this->alternativeNamesSequence->getContent();
    }
    
    protected function getEncodedValue() {
        $SANSubsequenceBinary = $this->alternativeNamesSequence->getBinary();        
        $SANSubsequenceBinaryLength = $this->alternativeNamesSequence->getObjectLength();
        
        $binary  = $this->objectIdentifier->getBinary();
        $binary .= chr(Identifier::OCTETSTRING);
        $binary .= chr($SANSubsequenceBinaryLength);
        $binary .= $SANSubsequenceBinary;
        
        return $binary;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);        
        
        if(ord($binaryData[$offsetIndex]) != Identifier::OBJECT_IDENTIFIER) {
            throw new ASN1ParserException('Can not parse Subject Alternative Names: Expected ASN.1 Object Identifier at the beginning but got ' . Identifier::getName($binaryData[$offsetIndex]), $offsetIndex);
        }
        
        $objectIdentifier = ASN_ObjectIdentifier::fromBinary($binaryData, $offsetIndex);        
        
        if($objectIdentifier->getContent() != OID::CERT_EXT_SUBJECT_ALT_NAME) {
            throw new ASN1ParserException('Can not parse Subject Alternative Names: Expected Object Identifier '.OID::CERT_EXT_SUBJECT_ALT_NAME.' at the beginning but got ' . $objectIdentifier->getContent(), $offsetIndex);
        }
        
        if(ord($binaryData[$offsetIndex]) != Identifier::OCTETSTRING) {
            throw new ASN1ParserException('Can not parse Subject Alternative Names: Expected ASN.1 Octet String after Object Identifier '.OID::CERT_EXT_SUBJECT_ALT_NAME.' but got ' . Identifier::getName($binaryData[$offsetIndex]), $offsetIndex);
        }
        $offsetIndex++;
        $lengthOfSequence = ord($binaryData[$offsetIndex++]);
        
        if($lengthOfSequence < 2) {
            throw new ASN1ParserException('Can not parse Subject Alternative Names: The Sequence within the octet string after the Object identifier '.OID::CERT_EXT_SUBJECT_ALT_NAME." is too short ({$lengthOfSequence} octets)", $offsetIndex);
        }
        
        $offsetOfSequence = $offsetIndex;
        $sequence = ASN_Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;

        if($sequence->getObjectLength() != $lengthOfSequence) {
            throw new ASN1ParserException("Can not parse Subject Alternative Names: The Sequence length does not match the length of the surrounding octet string", $offsetIndex);
        }
        
        $parsedObject = new SubjectAlternativeNames();
        foreach ($sequence as $object) {            
            if($object->getType() == SAN_DNSName::IDENTIFIER) {
                $domainName = SAN_DNSName::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addDomainName($domainName);
            }
            else if($object->getType() == SAN_IPAddress::IDENTIFIER) {
                $ip = SAN_IPAddress::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addIP($ip);
            }
            else {
                throw new ASN1ParserException("Could not parse Subject Alternative Name: Only DNSName and IP SANs are currently supported");
            }            
        }
        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once
        
        return $parsedObject;
    }
}
