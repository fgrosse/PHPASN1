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
use FG\ASN1\Universal\UniversalString;

class UniversalStringTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new UniversalString("Hello World");
        $this->assertEquals(Identifier::UNIVERSAL_STRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new UniversalString("Hello World");
        $this->assertEquals(chr(Identifier::UNIVERSAL_STRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new UniversalString("Hello World");
        $this->assertEquals("Hello World", $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = "Hello World";
        $object = new UniversalString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = "Hello World";
        $expectedType = chr(Identifier::UNIVERSAL_STRING);
        $expectedLength = chr(strlen($string));

        $object = new UniversalString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new UniversalString("Hello World");
        $binaryData = $originalobject->getBinary();
        $parsedObject = UniversalString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new UniversalString("Hello ");
        $originalobject2 = new UniversalString(" World");

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = UniversalString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = UniversalString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}
