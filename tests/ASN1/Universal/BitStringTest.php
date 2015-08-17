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
use FG\ASN1\Universal\BitString;

class BitStringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new BitString('A0 12 00 43');
        $this->assertEquals(Identifier::BITSTRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new BitString('A0 12 00 43');
        $this->assertEquals(chr(Identifier::BITSTRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new BitString('A01200C3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new BitString('A0 12 00 C3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new BitString('a0 12 00 c3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new BitString('A0 12 00 c3');
        $this->assertEquals('A01200C3', $object->getContent());

        $object = new BitString(0xA01200C3);
        $this->assertEquals('A01200C3', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new BitString(0x00);
        $this->assertEquals(4, $object->getObjectLength());

        $object = new BitString(0xFF);
        $this->assertEquals(4, $object->getObjectLength());

        $object = new BitString(0xA000);
        $this->assertEquals(5, $object->getObjectLength());

        $object = new BitString(0x3F2001);
        $this->assertEquals(6, $object->getObjectLength());
    }

    public function testGetObjectLengthWithVeryLongBitString()
    {
        $hexString = '0x'.str_repeat('FF', 1024);
        $object = new BitString($hexString);
        $this->assertEquals(1 + 3 + 1 + 1024, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::BITSTRING);
        $expectedLength = chr(0x02);

        $object = new BitString(0xFF);
        $expectedContent  = chr(0x00);
        $expectedContent .= chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new BitString(0xFFA034);
        $expectedLength = chr(0x04);
        $expectedContent  = chr(0x00);
        $expectedContent .= chr(0xFF);
        $expectedContent .= chr(0xA0);
        $expectedContent .= chr(0x34);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new BitString(0xA8, 3);
        $expectedLength = chr(0x02);
        $expectedContent  = chr(0x03);
        $expectedContent .= chr(0xA8);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    public function testGetBinaryForLargeBitStrings()
    {
        $nrOfBytes = 1024;
        $hexString = '0x'.str_repeat('FF', $nrOfBytes);
        $object = new BitString($hexString);

        $expectedType = chr(Identifier::BITSTRING);
        $expectedLength = chr(0x80 | 0x02);  // long length form: 2 length octets
        $expectedLength .= chr(1025 >> 8);   // first 8 bit of 1025
        $expectedLength .= chr(1025 & 0xFF); // last 8 bit of 1025
        $expectedContent = chr(0x00);        // number of unused bits
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
        $originalObject = new BitString(0x12);
        $binaryData = $originalObject->getBinary();
        $parsedObject = BitString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new BitString(0x010203A0, 3);
        $binaryData = $originalObject->getBinary();
        $parsedObject = BitString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new BitString(0xA0);
        $originalObject2 = new BitString(0x314510);

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = BitString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(4, $offset);
        $parsedObject = BitString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(10, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: A FG\ASN1\Universal\BitString should have a content length of at least 2. Extracted length was 1
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01()
    {
        $binaryData  = chr(Identifier::BITSTRING);
        $binaryData .= chr(0x01);
        $binaryData .= chr(0x00);
        BitString::fromBinary($binaryData);
    }
}
