<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2012
 *
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
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

    public function testGetContent()
    {
        $object = new SubjectAlternativeNames();
        $content = $object->getContent();
        $this->assertTrue(is_array($content));
        $this->assertTrue(sizeof($content) == 0);

        $dnsName = new DNSName('corvespace.de');
        $object->addDomainName($dnsName);
        $this->assertTrue(sizeof($object->getContent()) == 1);
        $this->assertContains($dnsName, $object->getContent());

        $ipAddress = new IPAddress('192.168.0.1');
        $object->addIP($ipAddress);
        $this->assertTrue(sizeof($object->getContent()) == 2);
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
        $originalobject = new SubjectAlternativeNames();
        $originalobject->addDomainName(new DNSName('corvespace.de'));
        $originalobject->addIP(new IPAddress('192.168.0.1'));
        $binaryData = $originalobject->getBinary();
        $parsedObject = SubjectAlternativeNames::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new SubjectAlternativeNames();
        $originalobject1->addDomainName(new DNSName('corvespace.de'));
        $originalobject1->addIP(new IPAddress('192.168.0.1'));
        $originalobject1->addIP(new IPAddress('10.218.0.1'));

        $originalobject2 = new SubjectAlternativeNames();
        $originalobject2->addDomainName(new DNSName('google.com'));

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = SubjectAlternativeNames::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(31, $offset);
        $parsedObject = SubjectAlternativeNames::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(47, $offset);
    }
}
