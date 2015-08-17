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
use FG\X509\SAN\DNSName;

class DNSNameTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new DNSName('test.corvespace.de');
        $this->assertEquals(0x82, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new DNSName('test.corvespace.de');
        $this->assertEquals(chr(0x82), $object->getIdentifier());
    }

    public function testGetContent()
    {
        $object = new DNSName('test.corvespace.de');
        $this->assertEquals('test.corvespace.de', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = 'test.corvespace.de';
        $object = new DNSName($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = 'test.corvespace.de';
        $expectedType = chr(0x82);
        $expectedLength = chr(strlen($string));

        $object = new DNSName($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new DNSName('test.corvespace.de');
        $binaryData = $originalObject->getBinary();
        $parsedObject = DNSName::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new DNSName('test.corvespace.de');
        $originalObject2 = new DNSName('superdomain.com');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = DNSName::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(20, $offset);
        $parsedObject = DNSName::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(37, $offset);
    }
}
