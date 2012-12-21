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

class CertificateExtensions extends ASN_Set implements Parseable {
    
    private $innerSequence;
    private $extensions = array();
    
    public function __construct() {
        $this->innerSequence = new ASN_Sequence();
        $this->addChild($this->innerSequence);
    }
    
    public function addSubjectAlternativeNames(SubjectAlternativeNames $sans) {
        $this->addExtension(OID::CERT_EXT_SUBJECT_ALT_NAME, $sans);
    }
    
    private function addExtension($oidString, ASN_Object $extension) {
        $sequence = new ASN_Sequence();
        $sequence->addChild(new ASN_ObjectIdentifier($oidString));
        $sequence->addChild($extension);
        
        $this->innerSequence->addChild($sequence);
        $this->extensions[] = $extension;
    }
    
    public function getContent() {
        return $this->extensions;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SET, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);                
        
        $tmpOffset = $offsetIndex;
        $extensions = ASN_Sequence::fromBinary($binaryData, $offsetIndex);
        $tmpOffset += 1 + $extensions->getNumberOfLengthOctets();
        
        $parsedObject = new CertificateExtensions();
        foreach ($extensions as $extension) {
            if($extension->getType() != Identifier::SEQUENCE) {
                //FIXME wrong offset index
                throw new ASN1ParserException('Could not parse Certificate Extensions: Expected ASN.1 Sequence but got ' . $extension->getTypeName(), $offsetIndex);
            }
            
            $tmpOffset += 1 + $extension->getNumberOfLengthOctets();
            $children = $extension->getChildren();
            if(count($children) < 2) {                
                throw new ASN1ParserException('Could not parse Certificate Extensions: Needs at least two child elements per extension sequence (object identifier and octet string)', $tmpOffset);
            }
            $objectIdentifier = $children[0];
            $octetString = $children[1];
            
            if($objectIdentifier->getType() != Identifier::OBJECT_IDENTIFIER) {
                throw new ASN1ParserException('Could not parse Certificate Extensions: Expected ASN.1 Object Identifier but got ' . $extension->getTypeName(), $tmpOffset);
            }
            
            $tmpOffset += $objectIdentifier->getObjectLength();
            
            if($objectIdentifier->getContent() == OID::CERT_EXT_SUBJECT_ALT_NAME) {
                $sans = SubjectAlternativeNames::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addSubjectAlternativeNames($sans);
            }
            else {
                // can now only parse SANs. There might be more in the future
                $tmpOffset += $octetString->getObjectSize();
            }
        }
        
        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once (just to let the equality unit tests pass :/ )
        return $parsedObject;
    }
}
