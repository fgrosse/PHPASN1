<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\X509;

use FG\Test\ASN1TestCase;
use FG\X509\SAN\IPAddress;

class IPAddressTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new IPAddress('192.168.0.1');
        $this->assertEquals(0x87, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new IPAddress('192.168.0.1');
        $this->assertEquals(chr(0x87), $object->getIdentifier());
    }

    public function testGetContent()
    {
        $object = new IPAddress('192.168.0.1');
        $this->assertEquals('192.168.0.1', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new IPAddress('192.168.0.1');
        $expectedSize = 6; // Type + Length + 4 Byte
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(0x87);
        $expectedLength = chr(4);
        $ipAddress  = chr(192);
        $ipAddress .= chr(168);
        $ipAddress .= chr(0);
        $ipAddress .= chr(1);
        $object = new IPAddress('192.168.0.1');
        $this->assertEquals($expectedType.$expectedLength.$ipAddress, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new IPAddress('192.168.0.1');
        $binaryData = $originalObject->getBinary();
        $parsedObject = IPAddress::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: A FG\X509\SAN\IPAddress should have a content length of 4. Extracted length was 2
     * @depends testFromBinary
     */
    public function testFromBinaryWithWrongLengthThrowsException()
    {
        $binaryData  = chr(0x87);
        $binaryData .= chr(2);
        $binaryData .= chr(192);
        $binaryData .= chr(168);
        IPAddress::fromBinary($binaryData);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new IPAddress('192.168.0.1');
        $originalObject2 = new IPAddress('10.65.32.123');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = IPAddress::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(6, $offset);
        $parsedObject = IPAddress::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(12, $offset);
    }
}
