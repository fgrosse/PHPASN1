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
use FG\ASN1\Universal\Null;

class NullTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new Null();
        $this->assertEquals(Identifier::NULL, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new Null();
        $this->assertEquals(chr(Identifier::NULL), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new Null();
        $this->assertEquals('NULL', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new Null();
        $this->assertEquals(2, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new Null();
        $expectedType = chr(Identifier::NULL);
        $expectedLength = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new Null();
        $binaryData = $originalobject->getBinary();
        $parsedObject = Null::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new Null();
        $originalobject2 = new Null();

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Null::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(2, $offset);
        $parsedObject = Null::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
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
        Null::fromBinary($binaryData);
    }
}
