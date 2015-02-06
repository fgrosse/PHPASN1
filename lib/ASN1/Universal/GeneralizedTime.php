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
 * This ASN.1 universal type contains date and time information according to ISO 8601
 *
 * The type consists of values representing:
 * a) a calendar date, as defined in ISO 8601; and
 * b) a time of day, to any of the precisions defined in ISO 8601, except for the hours value 24 which shall not be used; and
 * c) the local time differential factor as defined in ISO 8601.
 *
 * Decoding of this type will accept the Basic Encoding Rules (BER)
 * The encoding will comply with the Distinguished Encoding Rules (DER).
 */
class GeneralizedTime extends AbstractTime implements Parsable
{
    private $microseconds;

    public function __construct($dateTime = null, $dateTimeZone = 'UTC')
    {
        parent::__construct($dateTime, $dateTimeZone);
        $this->microseconds = $this->value->format('u');
        if ($this->containsFractionalSecondsElement()) {
            // DER requires us to remove trailing zeros
            $this->microseconds = preg_replace('/([1-9]+)0+$/', '$1', $this->microseconds);
        }
    }

    public function getType()
    {
        return Identifier::GENERALIZED_TIME;
    }

    protected function calculateContentLength()
    {
        $contentSize = 15; // YYYYMMDDHHmmSSZ

        if ($this->containsFractionalSecondsElement()) {
            $contentSize += 1 + strlen($this->microseconds);
        }

        return $contentSize;
    }

    public function containsFractionalSecondsElement()
    {
        return intval($this->microseconds) > 0;
    }

    protected function getEncodedValue()
    {
        $encodedContent = $this->value->format('YmdHis');
        if ($this->containsFractionalSecondsElement()) {
            $encodedContent .= ".{$this->microseconds}";
        }

        return $encodedContent.'Z';
    }

    public function __toString()
    {
        if ($this->containsFractionalSecondsElement()) {
            return $this->value->format("Y-m-d\tH:i:s.uP");
        } else {
            return $this->value->format("Y-m-d\tH:i:sP");
        }
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::GENERALIZED_TIME, $offsetIndex++);
        $lengthOfMinimumTimeString = 14; // YYYYMMDDHHmmSS
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, $lengthOfMinimumTimeString);
        $maximumBytesToRead = $contentLength;

        $format = 'YmdGis';
        $content = substr($binaryData, $offsetIndex, $contentLength);
        $dateTimeString = substr($content, 0, $lengthOfMinimumTimeString);
        $offsetIndex += $lengthOfMinimumTimeString;
        $maximumBytesToRead -= $lengthOfMinimumTimeString;

        if ($contentLength == $lengthOfMinimumTimeString) {
            $localTimeZone = new \DateTimeZone(date_default_timezone_get());
            $dateTime = \DateTime::createFromFormat($format, $dateTimeString, $localTimeZone);
        } else {
            if ($binaryData[$offsetIndex] == '.') {
                $maximumBytesToRead--; // account for the '.'
                $nrOfFractionalSecondElements = 1; // account for the '.'

                while ($maximumBytesToRead > 0
                      && $binaryData[$offsetIndex+$nrOfFractionalSecondElements] != '+'
                      && $binaryData[$offsetIndex+$nrOfFractionalSecondElements] != '-'
                      && $binaryData[$offsetIndex+$nrOfFractionalSecondElements] != 'Z') {
                    $nrOfFractionalSecondElements++;
                    $maximumBytesToRead--;
                }

                $dateTimeString .= substr($binaryData, $offsetIndex, $nrOfFractionalSecondElements);
                $offsetIndex += $nrOfFractionalSecondElements;
                $format .= '.u';
            }

            $dateTime = \DateTime::createFromFormat($format, $dateTimeString, new \DateTimeZone('UTC'));

            if ($maximumBytesToRead > 0) {
                if ($binaryData[$offsetIndex] == '+'
                || $binaryData[$offsetIndex] == '-') {
                    $dateTime = static::extractTimeZoneData($binaryData, $offsetIndex, $dateTime);
                } elseif ($binaryData[$offsetIndex++] != 'Z') {
                    throw new ParserException('Invalid ISO 8601 Time String', $offsetIndex);
                }
            }
        }

        $parsedObject = new GeneralizedTime($dateTime);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
