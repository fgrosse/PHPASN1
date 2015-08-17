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
use FG\ASN1\Universal\Enumerated;

class EnumeratedTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new Enumerated(1);
        $this->assertEquals(Identifier::ENUMERATED, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new Enumerated(1);
        $this->assertEquals(chr(Identifier::ENUMERATED), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new Enumerated(0);
        $this->assertEquals(0, $object->getContent());

        $object = new Enumerated(1);
        $this->assertEquals(1, $object->getContent());

        $object = new Enumerated(512);
        $this->assertEquals(512, $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new Enumerated(0);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Enumerated(127);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Enumerated(128);
        $this->assertEquals(4, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::ENUMERATED);
        $expectedLength = chr(0x01);

        $object = new Enumerated(0);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Enumerated(127);
        $expectedContent = chr(0x7F);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Enumerated(7420);
        $expectedLength   = chr(0x02);
        $expectedContent  = chr(0x1C);
        $expectedContent .= chr(0xFC);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new Enumerated(0);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Enumerated::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new Enumerated(127);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Enumerated::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new Enumerated(200);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Enumerated::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new Enumerated(1);
        $originalObject2 = new Enumerated(2);

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = Enumerated::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = Enumerated::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(6, $offset);
    }
}
