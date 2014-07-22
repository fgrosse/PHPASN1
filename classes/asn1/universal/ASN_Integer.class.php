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

class ASN_Integer extends ASN_Object implements Parseable{
    
    private $contentLength;
    
    public function __construct($value) {
        if(is_numeric($value) == false) {
            throw new Exception("Invalid VALUE [{$value}] for ASN1_INTEGER");
        }
        $this->value = $value;
    }
    
    public static function getType() {
        return Identifier::INTEGER;
    }
    
    protected function calculateContentLength() {        
        $nrOfOctets = 1; // we need at least one octet
        $tmpValue = abs($this->value);
        while($tmpValue > 127) {
            $tmpValue = $tmpValue >> 8;
            $nrOfOctets++;
        }        
        return $nrOfOctets;    
    }
    
    protected function getEncodedValue() {
        //TODO make this supported huge numbers
        
        $numericValue = $this->value;
        $contentLength = $this->getContentLength();
        
        if($numericValue < 0) {            
            $numericValue = abs($numericValue);            
            $numericValue = ~$numericValue & (pow(2, 8 * $contentLength) - 1);
            $numericValue += 1;
        }
        
        $result = '';
        for ($shiftLength=($contentLength-1)*8; $shiftLength >= 0; $shiftLength-=8) {
            $octet = $numericValue >> $shiftLength;
            $result .= chr($octet);
        }
        
        return $result;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {
        self::parseIdentifier($binaryData[$offsetIndex], static::getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);

        $isNegative = (ord($binaryData[$offsetIndex]) & 0x80) != 0x00;

        $number = gmp_init(ord($binaryData[$offsetIndex++]) & 0x7F);

        for ($i=0; $i<$contentLength-1; $i++) {
            $number = gmp_or(gmp_mul($number, 0x100), ord($binaryData[$offsetIndex++]));
        }

        if($isNegative) {
            $number = gmp_sub($number, pow(2, 8*$contentLength-1));
        }

        $parsedObject = new static(gmp_strval($number));
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
}
?>
