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

class ASN1Parser {        
    
    public function parse($binaryData, $offset=0) {
        $dataIndex = $offset;
        $typeIdentifier = ord($binaryData[$dataIndex++]);
        $objectLength = $this->extractObjectLength($binaryData, $dataIndex);

        switch($typeIdentifier) {
            case ASN_Object::ASN1_BITSTRING:
                return $this->createASNBitString($binaryData, $dataIndex, $objectLength);
            case ASN_Object::ASN1_BOOLEAN:
                return $this->createASNBoolean($binaryData, $dataIndex, $objectLength);
        }
    }
    
    private function extractObjectLength(&$binaryData, &$dataIndex) {
        $contentLength = ord($binaryData[$dataIndex++]);

        if( ($contentLength & 0x80) != 0) {
            // bit 8 is set -> this is the long form
            $nrofOfLengthOctets = $contentLength & 0x7F;
            $contentLength = 0x00;
            for ($i=0; $i < $nrofOfLengthOctets; $i++) { 
                $contentLength = ($contentLength << 8) + ord($binaryData[$dataIndex++]);
            }
        }
        
        return $contentLength;
    }

    private function createASNBitString(&$binaryData, &$dataIndex, $objectLength) {
        $nrOfUnusedBits = ord($binaryData[$dataIndex]);
        $value = substr($binaryData, $dataIndex+1, $objectLength-1);
        $dataIndex += $objectLength;
        return new ASN_BitString(bin2hex($value));
    }
    
    private function createASNBoolean(&$binaryData, &$dataIndex, $objectLength) {
        if($objectLength != 1) {
            throw new ASN1ParserException("An ASN.1 Boolean should not have a length other than one. Extracted length was {$objectLength}", $binaryData, $dataIndex, $objectLength);
        }
        $value = ord($binaryData[$dataIndex++]);
        return new ASN_Boolean($value==0xFF ? true : false);
    }
}
?>
    