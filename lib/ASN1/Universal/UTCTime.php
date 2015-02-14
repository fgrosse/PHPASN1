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

namespace FG\ASN1\Universal;

use FG\ASN1\AbstractTime;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Exception\ParserException;

/**
 * This ASN.1 universal type contains the calendar date and time
 *
 * The precision is one minute or one second and optionally a
 * local time differential from coordinated universal time.
 *
 * Decoding of this type will accept the Basic Encoding Rules (BER)
 * The encoding will comply with the Distinguished Encoding Rules (DER).
 */
class UTCTime extends AbstractTime implements Parsable
{
    public function getType()
    {
        return Identifier::UTC_TIME;
    }

    protected function calculateContentLength()
    {
        return 13; // Content is a string o the following format: YYMMDDhhmmssZ (13 octets)
    }

    protected function getEncodedValue()
    {
        return $this->value->format('ymdHis').'Z';
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::UTC_TIME, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 11);

        $format = 'ymdGi';
        $dateTimeString = substr($binaryData, $offsetIndex, 10);
        $offsetIndex += 10;

        // extract optional seconds part
        if ($binaryData[$offsetIndex] != 'Z'
        && $binaryData[$offsetIndex] != '+'
        && $binaryData[$offsetIndex] != '-') {
            $dateTimeString .= substr($binaryData, $offsetIndex, 2);
            $offsetIndex += 2;
            $format .= 's';
        }

        $dateTime = \DateTime::createFromFormat($format, $dateTimeString, new \DateTimeZone('UTC'));

        // extract time zone settings
        if ($binaryData[$offsetIndex] == '+'
        || $binaryData[$offsetIndex] == '-') {
            $dateTime = static::extractTimeZoneData($binaryData, $offsetIndex, $dateTime);
        } elseif ($binaryData[$offsetIndex++] != 'Z') {
            throw new ParserException('Invalid UTC String', $offsetIndex);
        }

        $parsedObject = new UTCTime($dateTime);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
