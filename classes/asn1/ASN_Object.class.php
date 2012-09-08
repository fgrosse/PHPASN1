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
	
    abstract public function getType();
	abstract public function getContentLength();
	abstract protected function getEncodedValue();
	
	public function getBinary() {
		$size = $this->getContentLength();
		
		$result  = chr($this->getType());
					
		//Create the Length octet(s)
		if($size <= 127) $result .= chr($size);
		else {//Wenn Size zu groß für 7Bit dann auf mehrere Bytes aufteilen
			$tmpbin = decbin($size);
			$tmpArr = array(); //in diesem Array werden die Bytes in umgedrehter Reihenfolge gespeichert
			$count = 1;
			while(strlen($tmpbin) > 8) {	
				//Nimm immer von hinten 8 Bit 
				$part = substr($tmpbin,strlen($tmpbin)-8);
				$tmpbin = substr($tmpbin,0,strlen($tmpbin)-8);
				
				//Füge diese dem tempArr hinzu
				$tmpArr[] = bindec($part);
				//Mizählen wieviel Bytes wir nun hane
				$count++;
			}
			$tmpArr[] = bindec($tmpbin);
			
			
			//Das Erste Byte gibt an, wieviele Length-Bytes (Octets es giebt)
			$firstByte = decbin($count);
			//ggf. mit 0len auffüllen
			while(strlen($firstByte) < 7)
				$firstByte = "0".$firstByte;
			//firstbyte muss mit 1 beginnen
			$firstByte = "1".$firstByte;
			
			$result .= chr(bindec($firstByte));
			//Werte in Richtiger Reihenfolge aus dem tmpArr lesen
			for($i=count($tmpArr)-1 ; $i >= 0 ; $i--)
				$result .= chr($tmpArr[$i]);
		}
		
		//Create the Content octet(s)
		$result .= $this->getEncodedValue();	//VALUE
		return $result;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getHexValue() {
		if(is_string($this->value)) {
		    return "not Supported";
		}
		else {
            return dechex($this->value);
        }
	}

	public function __toString() {
		return $this->getValue();
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