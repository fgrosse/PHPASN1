<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        return ord($this->getIdentifier());
    }

    public function getIdentifier()
    {
        $identifier = Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, true, $this->tag);

        return is_int($identifier) ? chr($identifier) : $identifier;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $identifier = self::parseBinaryIdentifier($binaryData, $offsetIndex);
        $firstIdentifierOctet = ord($identifier);
        assert(Identifier::isContextSpecificClass($firstIdentifierOctet));
        assert(Identifier::isConstructed($firstIdentifierOctet));
        $tag = Identifier::getTagNumber($identifier);

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
