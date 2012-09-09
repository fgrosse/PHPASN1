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

class ASN_BitString extends ASN_Object {
        
    private $nrOfUnusedBits;
        
    public function __construct($value, $nrOfUnusedBits=0) {      
        if(!is_string($value) && !is_numeric($value)) {
            throw new Exception("ASN_BitString: unrecognized input type!");
        }
        if(!is_numeric($nrOfUnusedBits) || $nrOfUnusedBits < 0) {
            throw new Exception("ASN_BitString: second parameter needs to be a positive number (or zero)!");
        }
        
        if(is_numeric($value)) {
            $value = dechex($value);                
        }
        
        $this->value = $value;
        $this->nrOfUnusedBits = $nrOfUnusedBits;
    }
    
    public function getType() {
        return self::ASN1_BITSTRING;
    }
    
    protected function getContentLength() {
        $value = $this->value;
        if(strlen($value) %2 != 0) {
            // transform values like 1F2 to 01F2
            $value = "0".$value;
        } 
        $length = 1 + ceil((strlen($value)/2));
        return $length;
    }
    
    protected function getEncodedValue() {
        $value = $this->value;               
        
        if(strlen($value) %2 != 0) {
            // transform values like 1F2 to 01F2
            $value = "0".$value;
        } 
        
        // the first octet determines the number of unused bits
        $result = chr($this->nrOfUnusedBits);
        
        //Actual content
        while(strlen($value) >= 2) {
            // get the hex value byte by byte from the string and and add it to binary result
            $result .= chr(hexdec(substr($value,0,2)));
            $value = substr($value,2);
        }
        
        return $result;
    }
           
}
?>