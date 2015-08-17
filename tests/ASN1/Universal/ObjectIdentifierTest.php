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
use FG\ASN1\Universal\ObjectIdentifier;

class ObjectIdentifierTest extends ASN1TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage [1.Foo.3] is no valid object identifier (sub identifier 2 is not numeric)!
     */
    public function testCreateWithInvalidObjectIdentifier()
    {
        new ObjectIdentifier('1.Foo.3');
    }

    public function testGetType()
    {
        $object = new ObjectIdentifier('1.2.3');
        $this->assertEquals(Identifier::OBJECT_IDENTIFIER, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new ObjectIdentifier('1.2.3');
        $this->assertEquals(chr(Identifier::OBJECT_IDENTIFIER), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new ObjectIdentifier('1.2.3');
        $this->assertEquals('1.2.3', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new ObjectIdentifier('1.2.3');
        $this->assertEquals(4, $object->getObjectLength());

        $object = new ObjectIdentifier('1.2.250.1.16.9');
        $this->assertEquals(8, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new ObjectIdentifier('1.2.3');
        $expectedType     = chr(Identifier::OBJECT_IDENTIFIER);
        $expectedLength   = chr(0x02);
        $expectedContent  = chr(1 * 40 + 2);
        $expectedContent .= chr(3);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new ObjectIdentifier('1.2.250.1.16.9');
        $expectedLength   = chr(0x06);
        $expectedContent  = chr(1 * 40 + 2); // 1.2
        $expectedContent .= chr(128 | 1);    // 250
        $expectedContent .= chr(122);        //
        $expectedContent .= chr(1);          //   1
        $expectedContent .= chr(16);         //  16
        $expectedContent .= chr(9);          //   9
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new ObjectIdentifier('1.2.250.1.16.9');
        $binaryData = $originalObject->getBinary();
        $parsedObject = ObjectIdentifier::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new ObjectIdentifier('1.2.3');
        $originalObject2 = new ObjectIdentifier('1.2.250.1.16.9');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = ObjectIdentifier::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(4, $offset);
        $parsedObject = ObjectIdentifier::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(12, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 4: Malformed ASN.1 Object Identifier
     * @depends testFromBinary
     */
    public function testFromBinaryWithMalformedOID()
    {
        $binaryData  = chr(Identifier::OBJECT_IDENTIFIER);
        $binaryData .= chr(0x03);
        $binaryData .= chr(42);
        $binaryData .= chr(128 | 1);
        $binaryData .= chr(128 | 1);
        ObjectIdentifier::fromBinary($binaryData);
    }
}
