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

/**
 * This ASN.1 universal type contains the calendar date and time
 * 
 * The precision is one minute or one second and optionally a
 * local time differential from coordinated universal time.
 * 
 * Decoding of this type will accept the Basic Encoding Rules (BER)
 * The encoding will comply with the Distinguished Encoding Rules (DER).
 */
class ASN_UTCTime extends ASN_Object implements Parseable {
    
    public function __construct($dateTime = null) {
        $UTC = new \DateTimeZone('UTC');
        
        if($dateTime == null || is_string($dateTime)) {
            $dateTime = new \DateTime($dateTime, $UTC);
            if($dateTime == false) {
                $errorMessage = $this->getLastDateTimeErrors();
                throw new \Exception("Could not create ASN.1 UTCTime from date time string '{$dateTimeString}': {$errorMessage}");
            }
        }
        else if (!$dateTime instanceof \DateTime){
            throw new \Exception('Invalid argument or ASN_UTCTIME constructor');
        }
        
        $this->value = $dateTime;
    }
    
    private function getLastDateTimeErrors() {
        $messages = '';
        $lastErrors = \DateTime::getLastErrors();
        foreach ($lastErrors['errors'] as $errorMessage) {
            $messages .= "{$errorMessage}, ";
        }
        return substr($messages, 0, -2);
    }
    
    public function getType() {
        return Identifier::UTC_TIME;
    }
    
    protected function calculateContentLength() {
        // Content is a string o the following format: YYMMDDhhmmssZ (13 octets)
        return 13;
    }
    
    protected function getEncodedValue() {
        return $this->value->format('ymdHis').'Z';
    }
    
    public function __toString() {
        return $this->value->format("Y-m-d\tH:i:s");
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::UTC_TIME, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 11);
           
        $format = 'ymdGi';
        $dateTimeString = substr($binaryData, $offsetIndex, 10);        
        $offsetIndex += 10; 
        
        // extract optional seconds part       
        if($binaryData[$offsetIndex] != 'Z'
        && $binaryData[$offsetIndex] != '+'
        && $binaryData[$offsetIndex] != '-') {            
            $dateTimeString .= substr($binaryData, $offsetIndex, 2);
            $offsetIndex += 2;
            $format .= 's';
        }
        
        $dateTime = \DateTime::createFromFormat($format, $dateTimeString, new \DateTimeZone('UTC'));
        
        // extract time zone settings
        if($binaryData[$offsetIndex] == '+'
        || $binaryData[$offsetIndex] == '-') {
            $sign = $binaryData[$offsetIndex++];
            $timeOffsetHours   = intval(substr($binaryData, $offsetIndex, 2));
            $timeOffsetMinutes = intval(substr($binaryData, $offsetIndex+2, 2));
            $offsetIndex += 4;            

            $intervall = new \DateInterval("PT{$timeOffsetHours}H{$timeOffsetMinutes}M");
            if($sign == '+') {
                $dateTime->sub($intervall);
            }
            else {             
                $dateTime->add($intervall);           
            }
        }
        else {
            if($binaryData[$offsetIndex++] != 'Z') {
                throw new ASN1ParserException('Invalid UTC String', $offsetIndex);
            }
        }
        
        $parsedObject = new ASN_UTCTime($dateTime);
        $parsedObject->setContentLength($contentLength);        
        
        return $parsedObject;
    }
}
?>