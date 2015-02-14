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
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;

class BitString extends OctetString implements Parsable
{
    private $nrOfUnusedBits;

    /**
     * Creates a new ASN.1 BitString object.
     *
     * @param string|int $value Either the hexadecimal value as a string (spaces are allowed - leading 0x is optional) or a numeric value
     * @param int $nrOfUnusedBits the number of unused bits in the last octet [optional].
     * @throws Exception if the second parameter is no positive numeric value
     */
    public function __construct($value, $nrOfUnusedBits = 0)
    {
        parent::__construct($value);

        if (!is_numeric($nrOfUnusedBits) || $nrOfUnusedBits < 0) {
            throw new Exception("BitString: second parameter needs to be a positive number (or zero)!");
        }

        $this->nrOfUnusedBits = $nrOfUnusedBits;
    }

    public function getType()
    {
        return Identifier::BITSTRING;
    }

    protected function calculateContentLength()
    {
        // add one to the length for the first octet which encodes the number of unused bits in the last octet
        return parent::calculateContentLength() + 1;
    }

    protected function getEncodedValue()
    {
        // the first octet determines the number of unused bits
        $nrOfUnusedBitsOctet = chr($this->nrOfUnusedBits);
        $actualContent = parent::getEncodedValue();

        return $nrOfUnusedBitsOctet.$actualContent;
    }

    public function getNumberOfUnusedBits()
    {
        return $this->nrOfUnusedBits;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::BITSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 2);

        $nrOfUnusedBits = ord($binaryData[$offsetIndex]);
        $value = substr($binaryData, $offsetIndex+1, $contentLength-1);
        $offsetIndex += $contentLength;

        $parsedObject = new BitString(bin2hex($value), $nrOfUnusedBits);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
