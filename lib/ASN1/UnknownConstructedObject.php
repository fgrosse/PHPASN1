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

class UnknownConstructedObject extends Construct
{
    private $identifier;
    private $contentLength;

    /**
     * @param string $binaryData
     * @param integer $offsetIndex
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function __construct($binaryData, &$offsetIndex)
    {
        $this->identifier = self::parseBinaryIdentifier($binaryData, $offsetIndex);
        $this->contentLength = self::parseContentLength($binaryData, $offsetIndex);


        $children = array();
        $octetsToRead = $this->contentLength;
        while ($octetsToRead > 0) {
            $newChild = Object::fromBinary($binaryData, $offsetIndex);
            $octetsToRead -= $newChild->getObjectLength();
            $children[] = $newChild;
        }

        $this->children = array();
        $this->addChildren($children);
    }

    public function getType()
    {
        return ord($this->identifier);
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    protected function calculateContentLength()
    {
        return $this->contentLength;
    }

    protected function getEncodedValue()
    {
        return '';
    }
}
