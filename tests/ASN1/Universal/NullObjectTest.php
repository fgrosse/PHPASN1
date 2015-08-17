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
use FG\ASN1\Universal\NullObject;

class NullObjectTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new NullObject();
        $this->assertEquals(Identifier::NULL, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new NullObject();
        $this->assertEquals(chr(Identifier::NULL), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new NullObject();
        $this->assertEquals('NULL', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new NullObject();
        $this->assertEquals(2, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new NullObject();
        $expectedType = chr(Identifier::NULL);
        $expectedLength = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new NullObject();
        $binaryData = $originalObject->getBinary();
        $parsedObject = NullObject::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new NullObject();
        $originalObject2 = new NullObject();

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = NullObject::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(2, $offset);
        $parsedObject = NullObject::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(4, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Null should not have a length other than zero. Extracted length was 1
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01()
    {
        $binaryData  = chr(Identifier::NULL);
        $binaryData .= chr(0x01);
        NullObject::fromBinary($binaryData);
    }
}
