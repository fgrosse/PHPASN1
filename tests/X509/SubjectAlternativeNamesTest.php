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
use FG\ASN1\Identifier;
use FG\X509\SAN\DNSName;
use FG\X509\SAN\IPAddress;
use FG\X509\SAN\SubjectAlternativeNames;

class SubjectAlternativeNamesTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new SubjectAlternativeNames();
        $this->assertEquals(Identifier::OCTETSTRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new SubjectAlternativeNames();
        $this->assertEquals(chr(Identifier::OCTETSTRING), $object->getIdentifier());
    }

    public function testGetContent()
    {
        $object = new SubjectAlternativeNames();
        $content = $object->getContent();
        $this->assertTrue(is_array($content));
        $this->assertTrue(count($content) == 0);

        $dnsName = new DNSName('corvespace.de');
        $object->addDomainName($dnsName);
        $this->assertTrue(count($object->getContent()) == 1);
        $this->assertContains($dnsName, $object->getContent());

        $ipAddress = new IPAddress('192.168.0.1');
        $object->addIP($ipAddress);
        $this->assertTrue(count($object->getContent()) == 2);
        $this->assertContains($ipAddress, $object->getContent());
    }

    public function testGetObjectLength()
    {
        $dnsName = new DNSName('example.dhl.com');
        $object = new SubjectAlternativeNames();
        $object->addDomainName($dnsName);
        $expectedSize = 2 + 2 + $dnsName->getObjectLength(); // all registered SANs are encapsulated in a sequence which is encapsulated in a octet string
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $dnsName = new DNSName('example.dhl.com');
        $object = new SubjectAlternativeNames();
        $object->addDomainName($dnsName);

        $expectedType = chr(Identifier::OCTETSTRING);
        $expectedLength = chr(0x13);
        $expectedContent  = chr(Identifier::SEQUENCE);
        $expectedContent .= chr(0x11);
        $expectedContent .= $dnsName->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new SubjectAlternativeNames();
        $originalObject->addDomainName(new DNSName('corvespace.de'));
        $originalObject->addIP(new IPAddress('192.168.0.1'));
        $binaryData = $originalObject->getBinary();
        $parsedObject = SubjectAlternativeNames::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new SubjectAlternativeNames();
        $originalObject1->addDomainName(new DNSName('corvespace.de'));
        $originalObject1->addIP(new IPAddress('192.168.0.1'));
        $originalObject1->addIP(new IPAddress('10.218.0.1'));

        $originalObject2 = new SubjectAlternativeNames();
        $originalObject2->addDomainName(new DNSName('google.com'));

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = SubjectAlternativeNames::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(31, $offset);
        $parsedObject = SubjectAlternativeNames::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(47, $offset);
    }
}
