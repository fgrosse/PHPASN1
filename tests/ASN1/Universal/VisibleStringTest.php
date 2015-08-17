<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\ASN1\Universal;

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\VisibleString;

class VisibleStringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new VisibleString('Hello World');
        $this->assertEquals(Identifier::VISIBLE_STRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new VisibleString('Hello World');
        $this->assertEquals(chr(Identifier::VISIBLE_STRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new VisibleString('Hello World');
        $this->assertEquals('Hello World', $object->getContent());

        $object = new VisibleString('');
        $this->assertEquals('', $object->getContent());

        $object = new VisibleString('             ');
        $this->assertEquals('             ', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = 'Hello World';
        $object = new VisibleString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = 'Hello World';
        $expectedType = chr(Identifier::VISIBLE_STRING);
        $expectedLength = chr(strlen($string));

        $object = new VisibleString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new VisibleString('Hello World');
        $binaryData = $originalObject->getBinary();
        $parsedObject = VisibleString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new VisibleString('Hello ');
        $originalObject2 = new VisibleString(' World');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = VisibleString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = VisibleString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}
