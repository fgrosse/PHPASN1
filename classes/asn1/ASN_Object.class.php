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

abstract class ASN_Object {
    
    const ASN1_BOOLEAN           = 0x01;
    const ASN1_INTEGER           = 0x02;
    const ASN1_BITSTRING         = 0x03;
    const ASN1_OCTETSTRING       = 0x04;
    const ASN1_NULL              = 0x05;
    const ASN1_OBJECTIDENTIFIER  = 0x06;
    const ASN1_OBJECT_DESCRIPTOR = 0x07;
    const ASN1_UTF8String        = 0x0c;
    const ASN1_SEQUENCE          = 0x30;
    const ASN1_SET               = 0x31;
    const ASN1_NUMERIC_STRING    = 0x12;
    const ASN1_PRINTABLE_STRING  = 0x13;
    const ASN1_TELETEXT_STRING   = 0x14;
    const ASN1_IA5_STRING        = 0x16;
    const ASN1_UTCTime           = 0x17;
    const ASN1_GENERALIZED_TIME  = 0x18;
    
	protected $value;
	
    /**
     * Must return the identifier octet of the ASN_Object.
     * All possible values are stored as class constants within
     * the ASN_Object base class. 
     */
    abstract public function getType();
	abstract protected function getContentLength();
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
    
}
?>