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
 
class ASN_Integer extends ASN_Object {
        
    function __construct($value) {
        if(!is_numeric($value)) throw new Exception("Invalid VALUE [".$value."] for ASN1_INTEGER");
        $this->type = ASN1_INTEGER;
        $this->value = $value;
    }
    
    function getEncodedValue() {
        //Value von Int in Hex umwandeln
        $value = dechex($this->value);
        $result = "";
        if(strlen($value) %2 != 0) $value = "0".$value; //1F2 auf Wert álá 01F2 bringen 
        while(strlen($value) >= 2) {
            //Hexwerte Byte für Byte aus dem String parsen und in chr umwandeln
            $result .= chr(hexdec(substr($value,0,2)));
            $value = substr($value,2);
        }
        return $result;
    }
    
    function getContentLength() {
        $value = dechex($this->value);
        return ceil((strlen($value)/2));    
    }
}
?>