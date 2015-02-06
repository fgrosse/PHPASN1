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

use FG\ASN1\OID;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Exception\GeneralException;
use FG\ASN1\Exception\ParserException;

class ObjectIdentifier extends Object implements Parsable
{
    protected $subIdentifiers;
    protected $value;

    public function __construct($value)
    {
        $this->subIdentifiers = explode('.', $value);
        $nrOfSubIdentifiers = count($this->subIdentifiers);

        for ($i = 0; $i < $nrOfSubIdentifiers; $i++) {
            if (is_numeric($this->subIdentifiers[$i])) {
                // enforce the integer type
                $this->subIdentifiers[$i] = intval($this->subIdentifiers[$i]);
            } else {
                throw new GeneralException("[{$value}] is no valid object identifier (sub identifier ".($i+1)." is not numeric)!");
            }
        }

        // Merge the first to arcs of the OID registration tree (per ASN definition!)
        if ($nrOfSubIdentifiers >= 2) {
            $this->subIdentifiers[1] = ($this->subIdentifiers[0] * 40) + $this->subIdentifiers[1];
            unset($this->subIdentifiers[0]);
        }

        $this->value = $value;
    }

    public function getContent()
    {
        return $this->value;
    }

    public function getType()
    {
        return Identifier::OBJECT_IDENTIFIER;
    }

    protected function calculateContentLength()
    {
        $length = 0;
        foreach ($this->subIdentifiers as $subIdentifier) {
            do {
                $subIdentifier = $subIdentifier >> 7;
                $length++;
            } while ($subIdentifier > 0);
        }

        return $length;
    }

    protected function getEncodedValue()
    {
        $encodedValue = '';
        foreach ($this->subIdentifiers as $subIdentifier) {
            $octets = chr($subIdentifier & 0x7F);
            $subIdentifier = $subIdentifier >> 7;
            while ($subIdentifier > 0) {
                $octets .= chr(0x80 | ($subIdentifier & 0x7F));
                $subIdentifier = $subIdentifier >> 7;
            }
            $encodedValue .= strrev($octets);
        }

        return $encodedValue;
    }

    public function __toString()
    {
        return OID::getName($this->value);
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OBJECT_IDENTIFIER, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);

        $firstOctet = ord($binaryData[$offsetIndex++]);
        $oidString = floor($firstOctet/40).'.'.($firstOctet % 40);

        $octetsToRead = $contentLength - 1;
        while ($octetsToRead > 0) {
            $number = 0;
            do {
                if ($octetsToRead == 0) {
                    throw new ParserException('Malformed ASN.1 Object Identifier', $offsetIndex-1);
                }
                $octet = ord($binaryData[$offsetIndex++]);
                $number = ($number << 7) + ($octet & 0x7F);
                $octetsToRead--;
            } while ($octet & 0x80);
            $oidString .= ".{$number}";
        }

        $parsedObject = new self($oidString);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
