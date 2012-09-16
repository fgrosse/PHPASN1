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
        $length = $this->getContentLength();

        if($length <= 127) {
            return chr($length);
        }
        else {
            // the contents size is too big for 7 bit so we need more octets for the length part
            $sizeAsBinaryString = decbin($length);
            $tmpArr = array(); // this array holds the size octets in reversed order
            $nrOfLengthOctets = 1;            
            while(strlen($sizeAsBinaryString) > 8) {
                // take the last 8 bit 
                $last8Bit = substr($sizeAsBinaryString, strlen($sizeAsBinaryString)-8);
                $sizeAsBinaryString = substr($sizeAsBinaryString, 0, strlen($sizeAsBinaryString)-8);
                $tmpArr[] = bindec($last8Bit);
                $nrOfLengthOctets++;
            }
            $tmpArr[] = bindec($sizeAsBinaryString);

            // the first length octet determines the number subsequent length octets
            $firstOctet = decbin($nrOfLengthOctets);

            // add some zeros to fill up all 8 bits
            //TODO What if there are more than 7 subsequent length octets?
            $firstOctet = str_repeat('0', 7-strlen($firstOctet)) . $firstOctet;

            // the first octet must start with a 1 to indicate that the long form is used
            $firstOctet = '1'.$firstOctet;
            $lengthOctets = chr(bindec($firstOctet));

            // append the values from the tmp array in the right order
            for($i=$nrOfLengthOctets-1 ; $i >= 0 ; $i--) {
                $lengthOctets .= chr($tmpArr[$i]);
            }

            return $lengthOctets;
        }
    }

    protected function getContentLength() {
        if(!isset($this->contentLength)) {
            $this->contentLength = $this->calculateContentLength();
        }
        return $this->contentLength;
    }

    protected function setContentLength($newContentLength) {
        $this->contentLength = $newContentLength;
    }

    public function getContent() {
        return $this->value;
    }

    public function __toString() {
        return $this->getContent();
    }
    
    public function getObjectLength() {
        //IDByte ist immer ein Byte lang
        $count = 1;

        //LengthBytes...
        $contentLength = $this->getContentLength();
        if($contentLength <= 127) return $contentLength+2;
        else {//Wenn Size zu groß für 7Bit dann auf mehrere Bytes aufteilen
            $tmpbin = decbin($contentLength);
            $count++;
            while(strlen($tmpbin) > 8) {    
                //Nimm immer von hinten 8 Bit weg
                $tmpbin = substr($tmpbin,0,strlen($tmpbin)-8);
                //Mitzählen wieviel Bytes wir nun hane
                $count++;
            }
            $count++;
        }

        //ContentLength...
        $count += $contentLength;

        return $count;
    }

    protected static function parseIdentifier($identifierOctet, $expectedIdentifier, $offsetForExceptionHandling) {
        if(!is_numeric($identifierOctet)) {
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