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
