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
use FG\ASN1\Universal\OctetString;

class OctetStringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new OctetString('30 14 06 08 2B 06 01 05 05 07 03 01 06 08 2B 06 01 05 05 07 03 02');
        $this->assertEquals(Identifier::OCTETSTRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new OctetString('30 14 06 08 2B 06 01 05 05 07 03 01 06 08 2B 06 01 05 05 07 03 02');
        $this->assertEquals(chr(Identifier::OCTETSTRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new OctetString('A01200C3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString('A0 12 00 C3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString('a0 12 00 c3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString('A0 12 00 c3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new OctetString(0xA01200C3);
        $this->assertEquals('A01200C3', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new OctetString(0x00);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new OctetString(0xFF);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new OctetString(0xA000);
        $this->assertEquals(4, $object->getObjectLength());

        $object = new OctetString(0x3F2001);
        $this->assertEquals(5, $object->getObjectLength());
    }

    public function testGetObjectLengthWithVeryLongOctetString()
    {
        $hexString = '0x'.str_repeat('FF', 1024);
        $object = new OctetString($hexString);
        $this->assertEquals(1 + 3 + 1024, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::OCTETSTRING);
        $expectedLength = chr(0x01);

        $object = new OctetString(0xFF);
        $expectedContent = chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new OctetString(0xFFA034);
        $expectedLength = chr(0x03);
        $expectedContent  = chr(0xFF);
        $expectedContent .= chr(0xA0);
        $expectedContent .= chr(0x34);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new OctetString(null);
        $expectedLength = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength, $object->getBinary());
    }

    public function testGetBinaryForLargeOctetStrings()
    {
        $nrOfBytes = 1024;
        $hexString = '0x'.str_repeat('FF', $nrOfBytes);
        $object = new OctetString($hexString);

        $expectedType = chr(Identifier::OCTETSTRING);
        $expectedLength = chr(0x80 | 0x02);  // long length form: 2 length octets
        $expectedLength .= chr(1024 >> 8);   // first 8 bit of 1025
        $expectedLength .= chr(1024 & 0xFF); // last 8 bit of 1025
        $expectedContent = '';
        for ($i = 0; $i < $nrOfBytes; $i++) {
            $expectedContent .= chr(0xFF);   // content
        }

        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new OctetString(0x12);
        $binaryData = $originalObject->getBinary();
        $parsedObject = OctetString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new OctetString(0x010203A0);
        $binaryData = $originalObject->getBinary();
        $parsedObject = OctetString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new OctetString(0xA0);
        $originalObject2 = new OctetString(0x314510);

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = OctetString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = OctetString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(8, $offset);
    }

    public function testEmptyValue()
    {
        $object = new OctetString(null);
        $this->assertEmpty($object->getContent());
        $this->assertEquals(2, $object->getObjectLength());
    }
}
