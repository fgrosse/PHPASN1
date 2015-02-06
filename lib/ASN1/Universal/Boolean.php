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

use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Exception\ParserException;

class Boolean extends Object implements Parsable
{
    private $value;

    /**
     * @param bool $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return Identifier::BOOLEAN;
    }

    protected function calculateContentLength()
    {
        return 1;
    }

    protected function getEncodedValue()
    {
        if ($this->value == false) {
            return chr(0x00);
        } else {
            return chr(0xFF);
        }
    }

    public function getContent()
    {
        if ($this->value == true) {
            return "TRUE";
        } else {
            return "FALSE";
        }
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::BOOLEAN, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        if ($contentLength != 1) {
            throw new ParserException("An ASN.1 Boolean should not have a length other than one. Extracted length was {$contentLength}", $offsetIndex);
        }

        $value = ord($binaryData[$offsetIndex++]);
        $booleanValue = $value == 0xFF ? true : false;

        $parsedObject = new self($booleanValue);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
