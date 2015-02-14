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

namespace FG\ASN1;

use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Exception\ParserException;

class ContextSpecificObject extends Construct
{

    private $index;

    private $length;

    private $constructed;

    public function __construct($index, Object $object, $isConstructed = true)
    {
        parent::__construct($object);

        $this->length = $object->getObjectLength();
        $this->index = $index;
        $this->constructed = $isConstructed ? 1 : 0;
    }

    protected function calculateContentLength()
    {
        return $this->length;
    }

    public function getType()
    {
        return (Identifier::CLASS_CONTEXT_SPECIFIC << 6) | ($this->constructed << 5) | $this->index;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public static function fromBinary(& $binaryData, & $offsetIndex = 0)
    {
        $identifierOctet = ord($binaryData[$offsetIndex]);
        $subBinaryData = substr($binaryData,  2 + $offsetIndex);
        $subOffsetIndex = 0;

        $index = $identifierOctet & 15;

        return new self($index, Object::fromBinary($subBinaryData, $subOffsetIndex));
    }
}