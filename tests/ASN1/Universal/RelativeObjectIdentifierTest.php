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
use FG\ASN1\Universal\RelativeObjectIdentifier;

class RelativeObjectIdentifierTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new RelativeObjectIdentifier('8751.3.2');
        $this->assertEquals(Identifier::RELATIVE_OID, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new RelativeObjectIdentifier('8751.3.2');
        $this->assertEquals(chr(Identifier::RELATIVE_OID), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new RelativeObjectIdentifier('8751.3.2');
        $this->assertEquals('8751.3.2', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new RelativeObjectIdentifier('1.2.3');
        $this->assertEquals(2 + 3, $object->getObjectLength());

        $object = new RelativeObjectIdentifier('1.2.250.1.16.9');
        $this->assertEquals(2 + 7, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new RelativeObjectIdentifier('8571.3.2');
        $expectedType     = chr(Identifier::RELATIVE_OID);
        $expectedLength   = chr(0x04);
        $expectedContent  = chr(0xC2);
        $expectedContent .= chr(0x7B);
        $expectedContent .= chr(0x03);
        $expectedContent .= chr(0x02);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new RelativeObjectIdentifier('8571.3.2');
        $binaryData = $originalObject->getBinary();
        $parsedObject = RelativeObjectIdentifier::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new RelativeObjectIdentifier('8571.3.2');
        $originalObject2 = new RelativeObjectIdentifier('45.2.3455.1');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = RelativeObjectIdentifier::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(6, $offset);
        $parsedObject = RelativeObjectIdentifier::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(13, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 4: Malformed ASN.1 Relative Object Identifier
     * @depends testFromBinary
     */
    public function testFromBinaryWithMalformedOID()
    {
        $binaryData  = chr(Identifier::RELATIVE_OID);
        $binaryData .= chr(0x03);
        $binaryData .= chr(42);
        $binaryData .= chr(128 | 1);
        $binaryData .= chr(128 | 1);
        RelativeObjectIdentifier::fromBinary($binaryData);
    }
}
