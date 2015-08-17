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
use FG\ASN1\Universal\Boolean;

class BooleanTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new Boolean(true);
        $this->assertEquals(Identifier::BOOLEAN, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new Boolean(true);
        $this->assertEquals(chr(Identifier::BOOLEAN), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new Boolean(true);
        $this->assertEquals('TRUE', $object->getContent());

        $object = new Boolean(false);
        $this->assertEquals('FALSE', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new Boolean(true);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Boolean(false);
        $this->assertEquals(3, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::BOOLEAN);
        $expectedLength = chr(0x01);

        $object = new Boolean(true);
        $expectedContent = chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Boolean(false);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new Boolean(true);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Boolean::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new Boolean(false);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Boolean::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new Boolean(true);
        $originalObject2 = new Boolean(false);

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(6, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Boolean should not have a length other than one. Extracted length was 2
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01()
    {
        $binaryData  = chr(Identifier::BOOLEAN);
        $binaryData .= chr(0x02);
        $binaryData .= chr(0xFF);
        Boolean::fromBinary($binaryData);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Boolean should not have a length other than one. Extracted length was 0
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength02()
    {
        $binaryData  = chr(Identifier::BOOLEAN);
        $binaryData .= chr(0x00);
        $binaryData .= chr(0xFF);
        Boolean::fromBinary($binaryData);
    }
}
