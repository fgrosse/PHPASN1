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

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

abstract class Construct extends Object implements Countable, ArrayAccess, Parsable, IteratorAggregate
{
    /** @var Object[] */
    protected $children;

    /**
     * @param Object[] $children the variadic type hint is commented due to https://github.com/facebook/hhvm/issues/4858
     */
    public function __construct(/* HH_FIXME[4858]: variadic + strict */ ...$children)
    {
        $this->children = $children;
    }

    public function getContent()
    {
        return $this->children;
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
     * @return Object[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Object
     */
    public function getFirstChild()
    {
        return $this->children[0];
    }

    /**
     * @param string $binaryData
     * @param int $offsetIndex
     *
     * @throws Exception\ParserException
     *
     * @return Construct|static
     */
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static();
        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        $children = [];
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

    public function offsetExists($offset)
    {
        return isset($this->children[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->children[$offset]) ? $this->children[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->children[] = $value;
        }
        $this->children[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->children[$offset]);
    }

    public function count($mode = COUNT_NORMAL)
    {
        return count($this->children, $mode);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->children);
    }
}
