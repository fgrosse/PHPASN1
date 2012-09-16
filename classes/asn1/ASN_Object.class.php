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

abstract class ASN_Object {

    protected $value;

    private $contentLength;
    private $nrOfLengthOctets;

    /**
     * Must return the identifier octet of the ASN_Object.
     * All possible values are stored as class constants within
     * the Identifier class. 
     */
    abstract public function getType();

    /**
     * Must return the number of octets of the content part.
     */
    abstract protected function calculateContentLength();

    abstract protected function getEncodedValue();

    public function getBinary() {
        $result  = chr($this->getType());
        $result .= $this->createLengthPart();
        $result .= $this->getEncodedValue();

        return $result;
    }

    private function createLengthPart() {
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);

        if($nrOfLengthOctets == 1) {
            return chr($contentLength);
        }
        else {            
            // the first length octet determines the number subsequent length octets
            $lengthOctets = chr(0x80 | ($nrOfLengthOctets-1));
            for ($shiftLength= 8*($nrOfLengthOctets-2); $shiftLength >= 0 ; $shiftLength-=8) { 
                $lengthOctets .= chr($contentLength >> $shiftLength);                
            }
            
            return $lengthOctets;
        }
    }

    private function getNumberOfLengthOctets($contentLength=null) {        
        if(!isset($this->nrOfLengthOctets)) {
            if($contentLength == null) {
                $contentLength = $this->getContentLength();
            }
            
            $this->nrOfLengthOctets = 1;
            if($contentLength > 127) {            
                do { // long form
                    $this->nrOfLengthOctets++;
                    $contentLength = $contentLength >> 8;
                } while($contentLength > 0);
            }
        }
        
        return $this->nrOfLengthOctets;
    }

    protected function getContentLength() {
        if(!isset($this->contentLength)) {
            $this->contentLength = $this->calculateContentLength();
        }
        return $this->contentLength;
    }

    protected function setContentLength($newContentLength) {
        $this->contentLength = $newContentLength;
        $this->getNumberOfLengthOctets($newContentLength);
    }

    public function getObjectLength() {        
        $nrOfIdentifierOctets = 1; // does not support identifier long form yet        
        $contentLength = $this->getContentLength();        
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);
        
        return $nrOfIdentifierOctets + $nrOfLengthOctets + $contentLength;
    }

    public function getContent() {
        return $this->value;
    }

    public function __toString() {
        return $this->getContent();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        $identifierOctet = ord($binaryData[$offsetIndex]);

        switch ($identifierOctet) {
            case Identifier::BITSTRING:
                return ASN_BitString::fromBinary($binaryData, $offsetIndex);
            case Identifier::BOOLEAN:
                return ASN_Boolean::fromBinary($binaryData, $offsetIndex);
            case Identifier::ENUMERATED:
                return ASN_Enumerated::fromBinary($binaryData, $offsetIndex);
            case Identifier::IA5_STRING:
                return ASN_IA5String::fromBinary($binaryData, $offsetIndex);
            case Identifier::INTEGER:
                return ASN_Integer::fromBinary($binaryData, $offsetIndex);
            case Identifier::NULL:
                return ASN_Null::fromBinary($binaryData, $offsetIndex);
            case Identifier::OBJECT_IDENTIFIER:
                return ASN_ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            case Identifier::PRINTABLE_STRING:
                return ASN_PrintableString::fromBinary($binaryData, $offsetIndex);
            case Identifier::SEQUENCE:
                return ASN_Sequence::fromBinary($binaryData, $offsetIndex);
            case Identifier::SET:
                return ASN_Set::fromBinary($binaryData, $offsetIndex);
            
            default:
                $objectName = Identifier::getName($identifierOctet);
                throw new ASN1ParserException("Sorry but parsing of {$objectName} is not yet supported by PHPASN1.", $offsetIndex);
        }
    }

    protected static function parseIdentifier($identifierOctet, $expectedIdentifier, $offsetForExceptionHandling) {                
        if(is_string($identifierOctet) || is_numeric($identifierOctet) == false) {
            $identifierOctet = ord($identifierOctet);
        }
        
        if($identifierOctet != $expectedIdentifier) {
            $message = 'Can not create an '.Identifier::getName($expectedIdentifier).' from an '.Identifier::getName($identifierOctet); 
            throw new ASN1ParserException($message, $offsetForExceptionHandling);
        }
    }

    protected static function parseContentLength(&$binaryData, &$offsetIndex, $minimumLength=0) {
        $contentLength = ord($binaryData[$offsetIndex++]);

        if( ($contentLength & 0x80) != 0) {
            // bit 8 is set -> this is the long form
            $nrofOfLengthOctets = $contentLength & 0x7F;
            $contentLength = 0x00;
            for ($i=0; $i < $nrofOfLengthOctets; $i++) { 
                $contentLength = ($contentLength << 8) + ord($binaryData[$offsetIndex++]);
            }
        }

        if($contentLength < $minimumLength) {
            throw new ASN1ParserException('A '.get_called_class()." should have a content length of at least {$minimumLength}. Extracted length was {$contentLength}", $offsetIndex);
        }

        return $contentLength;
    }
}
?>