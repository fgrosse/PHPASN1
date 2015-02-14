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

abstract class Construct extends Object implements \Iterator, Parsable
{
    /** @var \FG\ASN1\Object[] */
    protected $children;
    private $iteratorPosition = 0;

    public function __construct(Object $child1 = null, Object $child2 = null, Object $childN = null)
    {
        $this->children = array();
        $this->rewind();

        $children = func_get_args();
        $this->addChildren($children);
    }

    public function getContent()
    {
        return $this->children;
    }

    /**
     * Rewind the Iterator to the first element (Iterator::rewind)
     */
    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    /**
     * Return the current element (Iterator::current)
     */
    public function current()
    {
        return $this->children[$this->iteratorPosition];
    }

    /**
     * Return the key of the current element (Iterator::key)
     */
    public function key()
    {
        return $this->iteratorPosition;
    }

    /**
     * Move forward to next element (Iterator::next)
     */
    public function next()
    {
        $this->iteratorPosition++;
    }

    /**
     * Checks if current position is valid (Iterator::valid)
     */
    public function valid()
    {
        return isset($this->children[$this->iteratorPosition]);
    }

    protected function calculateContentLength()
    {
        $length = 0;
        foreach ($this->children as $component) {
            $length += $component->getObjectLength();
        }

        return $length;
    }

    protected function getEncodedValue()
    {
        $result = '';
        foreach ($this->children as $component) {
            $result .= $component->getBinary();
        }

        return $result;
    }

    public function addChild(Object $child)
    {
        $this->children[] = $child;
    }

    public function addChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function __toString()
    {
        $nrOfChildren = $this->getNumberOfChildren();
        $childString = $nrOfChildren == 1 ? 'child' : 'children';

        return "[{$nrOfChildren} {$childString}]";
    }

    public function getNumberOfChildren()
    {
        return count($this->children);
    }

    /**
     * @return \FG\ASN1\Object[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return \FG\ASN1\Object
     */
    public function getFirstChild()
    {
        return $this->children[0];
    }

    /**
     * @param string $binaryData
     * @param int $offsetIndex
     * @return Construct|static
     * @throws Exception\ParserException
     */
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static();
        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        $children = array();
        $octetsToRead = $contentLength;
        while ($octetsToRead > 0) {
            $newChild = Object::fromBinary($binaryData, $offsetIndex);
            $octetsToRead -= $newChild->getObjectLength();
            $children[] = $newChild;
        }

        $parsedObject->addChildren($children);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}
