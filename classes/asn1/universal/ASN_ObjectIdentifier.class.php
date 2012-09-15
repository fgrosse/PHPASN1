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

class ASN_ObjectIdentifier extends ASN_Object {		   
        
	public function __construct($value) {		
		$subIdentifiers = explode('.', $value);
		foreach($subIdentifiers as $subIdentifier) {
			if(is_numeric($subIdentifier) == false) {
				throw new Exception("[{$value}] is no valid object identifier (some sub identifier is not numeric)!");
            }
        }
				
		$this->value = $value;
	}
	
    public function getType() {
        return self::ASN1_OBJECTIDENTIFIER;
    }
    
    protected function getContentLength() {
        $this->getEncodedValue();
        return $this->length;
    }
    
	protected function getEncodedValue() {
		$result = '';
		$value = $this->value;
		$subIdents = explode(".", $value);
		$subIdentHexArr = array();
		
		//Umwandlung in int
		for($i=0 ; $i < count($subIdents) ; $i++)
			$subIdents[$i] = intval($subIdents[$i]);

		//Ersten und zweiten Subidentifier zusammenführen (per ASN Definition!)
		if(count($subIdents) >= 2)
			$subIdents[1] = ($subIdents[0] * 40) + $subIdents[1];
		
		//Alle Subidentifier nun durchlaufen um Hexwerte dafür zu bilden
		for($i=1 ; $i < count($subIdents) ; $i++) {
			
			//Die jeweilige Zahl des Subidentifier als Binärzahl holen
			$tmpbin = decbin($subIdents[$i]);
			
			//Wenn Binärzahl zu lang ist (1. Bit reserviert für diesen Fall)...
			if(strlen($tmpbin) > 7) {
				//...muss der Binärwert auf mehrere Bytes verteilt werden
				$isLastPart = true;
				$tmpArr = array();	//in diesem Array werden die Bytes in umgedrehter Reihenfolge gespeichert
				while(strlen($tmpbin) > 7) {	
					//Nimm immer von hinten 7 Bit 
					$part = substr($tmpbin,strlen($tmpbin)-7);
					$tmpbin = substr($tmpbin,0,strlen($tmpbin)-7);
					
					if($isLastPart) {
						$part = "0".$part;
						$isLastPart = false;
					}
					else $part = "1".$part;
					
					$tmpArr[] = dechex(bindec($part));
				}
				
				//ggf mit 0len auffüllen
				$len = strlen($tmpbin);
				for($j=0 ; $j < (7-$len) ; $j++) 
					$tmpbin = "0".$tmpbin;
					
				$tmpbin="1".$tmpbin;
				$subIdentHexArr[] = dechex(bindec($tmpbin));
				
				//Werte in Richtiger Reihenfolge aus dem tmpArr lesen
				for($j=count($tmpArr) ; $j > 0 ; $j--)
					$subIdentHexArr[] = $tmpArr[$j-1];
			}
			else {
				//ggf mit 0len auffüllen
				$len = strlen($tmpbin);
				for($j=0 ; $j < (8-$len) ; $j++) 
					$tmpbin = "0".$tmpbin;
				
				//Ergebnis wird in array geschrieben
				$subIdentHexArr[] = dechex(bindec($tmpbin));
			}
		}
		
		//erstelltes $subIdentHexArr[]-Einträge in Integer umwandeln
		for($i=0 ; $i < count($subIdentHexArr) ; $i++)
			$subIdentHexArr[$i] = intval(hexdec($subIdentHexArr[$i]));
	
		//erstelltes $subIdentHexArr[] auswerten
		$this->length=0;
		for($i=0 ; $i < count($subIdentHexArr) ; $i++)
		{
			$result .= chr($subIdentHexArr[$i]);
			$this->length++;
		}
		
		return $result;
	}	
	
	function getHexValue() {
		$result="";
		$value = $this->value;
		$subIdents = explode(".", $value);
		$subIdentHexArr = array();
		
		//Umwandlung in int
		for($i=0 ; $i < count($subIdents) ; $i++)
			$subIdents[$i] = intval($subIdents[$i]);

		//Ersten und zweiten Subidentifier zusammenführen (per ASN Definition!)
		if(count($subIdents) >= 2)
			$subIdents[1] = ($subIdents[0] * 40) + $subIdents[1];
		
		//Alle Subidentifier nun durchlaufen um Hexwerte dafür zu bilden
		for($i=1 ; $i < count($subIdents) ; $i++) {
			
			//Die jeweilige Zahl des Subidentifier als Binärzahl holen
			$tmpbin = decbin($subIdents[$i]);
			
			//Wenn Binärzahl zu lang ist (1. Bit reserviert für diesen Fall)...
			if(strlen($tmpbin) > 7) {
				//...muss der Binärwert auf mehrere Bytes verteilt werden
				$isLastPart = true;
				$tmpArr = array();	//in diesem Array werden die Bytes in umgedrehter Reihenfolge gespeichert
				while(strlen($tmpbin) > 7) {	
					//Nimm immer von hinten 7 Bit 
					$part = substr($tmpbin,strlen($tmpbin)-7);
					$tmpbin = substr($tmpbin,0,strlen($tmpbin)-7);
					
					if($isLastPart) {
						$part = "0".$part;
						$isLastPart = false;
					}
					else $part = "1".$part;
					
					$tmpArr[] = dechex(bindec($part));
				}
				
				//ggf mit 0len auffüllen
				$len = strlen($tmpbin);
				for($j=0 ; $j < (7-$len) ; $j++) 
					$tmpbin = "0".$tmpbin;
					
				$tmpbin="1".$tmpbin;
				$subIdentHexArr[] = dechex(bindec($tmpbin));
				
				//Werte in Richtiger Reihenfolge aus dem tmpArr lesen
				for($j=count($tmpArr)-1 ; $j >= 0 ; $j--)
					$subIdentHexArr[] = $tmpArr[$j];
			}
			else {
				//ggf mit 0len auffüllen
				$len = strlen($tmpbin);
				for($j=0 ; $j < (8-$len) ; $j++) 
					$tmpbin = "0".$tmpbin;
				
				//Ergebnis wird in array geschrieben
				$subIdentHexArr[] = dechex(bindec($tmpbin));
			}
		}
		
		//erstelltes $subIdentHexArr[] auswerten
		for($i=0 ; $i < count($subIdentHexArr) ; $i++) {
			if(strlen($subIdentHexArr[$i]) % 2 != 0)
				$subIdentHexArr[$i] = "0".$subIdentHexArr[$i];
			$result .= $subIdentHexArr[$i];
		}
		
		return $result;
	}
}
?>