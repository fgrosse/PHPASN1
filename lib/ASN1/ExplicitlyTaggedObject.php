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

use FG\ASN1\Exception\ParserException;

/**
 * Class ExplicitlyTaggedObject decorate an inner object with an additional tag that gives information about
 * its context specific meaning.
 *
 * Explanation taken from A Layman's Guide to a Subset of ASN.1, BER, and DER:
 * >>> An RSA Laboratories Technical Note
 * >>> Burton S. Kaliski Jr.
 * >>> Revised November 1, 1993
 *
 * [...]
 * Explicitly tagged types are derived from other types by adding an outer tag to the underlying type.
 * In effect, explicitly tagged types are structured types consisting of one component, the underlying type.
 * Explicit tagging is denoted by the ASN.1 keywords [class number] EXPLICIT (see Section 5.2).
 * [...]
 *
 * @see http://luca.ntop.org/Teaching/Appunti/asn1.html
 */
class ExplicitlyTaggedObject extends Object
{
    private $decoratedObject;
    private $tag;

    /**
     * @param int $tag
     * @param \FG\ASN1\Object $object
     */
    public function __construct($tag, Object $object)
    {
        $this->tag = $tag;
        $this->decoratedObject = $object;
    }

    protected function calculateContentLength()
    {
        return $this->decoratedObject->getObjectLength();
    }

    protected function getEncodedValue()
    {
        return $this->decoratedObject->getBinary();
    }

    public function getContent()
    {
        return $this->decoratedObject;
    }

    public function __toString()
    {
        $decoratedType = Identifier::getShortName($this->decoratedObject->getType());
        return "Context specific $decoratedType with tag [{$this->tag}]";
    }

    public function getType()
    {
        return Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, true, $this->tag);
    }

    public function getTag()
    {
        return $this->tag;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $identifierOctet = ord($binaryData[$offsetIndex++]);
        assert(Identifier::isContextSpecificClass($identifierOctet));
        assert(Identifier::isConstructed($identifierOctet));
        $tag = Identifier::getTagNumber($identifierOctet);

        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $offsetIndexOfDecoratedObject = $offsetIndex;
        $decoratedObject = Object::fromBinary($binaryData, $offsetIndex);
        if ($decoratedObject->getObjectLength() != $contentLength) {
            throw new ParserException("Context-Specific explicitly tagged object [$tag] starting at offset $offsetIndexOfDecoratedObject is longer than allowed in the outer tag", $offsetIndexOfDecoratedObject);
        }

        $parsedObject = new self($tag, $decoratedObject);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
}
