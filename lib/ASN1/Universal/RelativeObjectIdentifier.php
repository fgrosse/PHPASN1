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

use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Exception\GeneralException;
use FG\ASN1\Exception\ParserException;

class RelativeObjectIdentifier extends ObjectIdentifier implements Parsable
{
    public function __construct($subIdentifiers)
    {
        $this->value = $subIdentifiers;
        $this->subIdentifiers = explode('.', $subIdentifiers);
        $nrOfSubIdentifiers = count($this->subIdentifiers);

        for ($i = 0; $i < $nrOfSubIdentifiers; $i++) {
            if (is_numeric($this->subIdentifiers[$i])) {
                // enforce the integer type
                $this->subIdentifiers[$i] = intval($this->subIdentifiers[$i]);
            } else {
                throw new GeneralException("[{$subIdentifiers}] is no valid object identifier (sub identifier ".($i+1)." is not numeric)!");
            }
        }
    }

    public function getType()
    {
        return Identifier::RELATIVE_OID;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::RELATIVE_OID, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);

        $oidString = '';
        $octetsToRead = $contentLength;
        while ($octetsToRead > 0) {
            $number = 0;
            do {
                if ($octetsToRead == 0) {
                    throw new ParserException('Malformed ASN.1 Relative Object Identifier', $offsetIndex-1);
                }
                $octet = ord($binaryData[$offsetIndex++]);
                $number = ($number << 7) + ($octet & 0x7F);
                $octetsToRead--;
            } while ($octet & 0x80);
            $oidString .= "{$number}.";
        }

        // remove trailing '.'
        $oidString = substr($oidString, 0, -1);

        $parsedObject = new self($oidString);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
