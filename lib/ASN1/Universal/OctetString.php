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

use Exception;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;

class OctetString extends Object implements Parsable
{
    protected $value;

    public function __construct($value)
    {
        if (is_string($value)) {
            // remove gaps between hex digits
            $value = preg_replace('/\s|0x/', '', $value);
        } elseif (is_numeric($value)) {
            $value = dechex($value);
        } else {
            throw new Exception("OctetString: unrecognized input type!");
        }

        if (strlen($value) %2 != 0) {
            // transform values like 1F2 to 01F2
            $value = "0".$value;
        }

        $this->value = $value;
    }

    public function getType()
    {
        return Identifier::OCTETSTRING;
    }

    protected function calculateContentLength()
    {
        return strlen($this->value)/2;
    }

    protected function getEncodedValue()
    {
        $value = $this->value;
        $result = '';

        //Actual content
        while (strlen($value) >= 2) {
            // get the hex value byte by byte from the string and and add it to binary result
            $result .= chr(hexdec(substr($value, 0, 2)));
            $value = substr($value, 2);
        }

        return $result;
    }

    public function getContent()
    {
        return strtoupper($this->value);
    }

    public function getBinaryContent()
    {
        return $this->getEncodedValue();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OCTETSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);

        $value = substr($binaryData, $offsetIndex, $contentLength);
        $offsetIndex += $contentLength;

        $parsedObject = new OctetString(bin2hex($value));
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
