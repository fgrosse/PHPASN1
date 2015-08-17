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
use FG\ASN1\Universal\BMPString;

class BMPStringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new BMPString('Hello World');
        $this->assertEquals(Identifier::BMP_STRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new BMPString('Hello World');
        $this->assertEquals(chr(Identifier::BMP_STRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new BMPString('Hello World');
        $this->assertEquals('Hello World', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = 'Hello World';
        $object = new BMPString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = 'Hello World';
        $expectedType = chr(Identifier::BMP_STRING);
        $expectedLength = chr(strlen($string));

        $object = new BMPString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new BMPString('Hello World');
        $binaryData = $originalObject->getBinary();
        $parsedObject = BMPString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new BMPString('Hello ');
        $originalObject2 = new BMPString(' World');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = BMPString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = BMPString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}
