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
use FG\ASN1\OID;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\X509\CertificateExtensions;
use FG\X509\SAN\DNSName;
use FG\X509\SAN\IPAddress;
use FG\X509\SAN\SubjectAlternativeNames;

class CertificateExtensionTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new CertificateExtensions();
        $this->assertEquals(Identifier::SET, $object->getType());
    }

    public function testGetContent()
    {
        $object = new CertificateExtensions();
        $content = $object->getContent();
        $this->assertTrue(is_array($content));
        $this->assertTrue(sizeof($content) == 0);

        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));

        $object->addSubjectAlternativeNames($sans);
        $this->assertTrue(sizeof($object->getContent()) == 1);
        $this->assertContains($sans, $object->getContent());
    }

    public function testGetObjectLength()
    {
        $object = new CertificateExtensions();
        $objectIdentifier = new ObjectIdentifier(OID::CERT_EXT_SUBJECT_ALT_NAME);
        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $object->addSubjectAlternativeNames($sans);

        $sizeOfFirstExtensionSequence = 2 + $objectIdentifier->getObjectLength() + $sans->getObjectLength();
        $expectedSize = 2 + 2 + $sizeOfFirstExtensionSequence; // Extensions are sequences of Object identifiers and octet strings that are contained in a sequence which is contained in a set
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $object = new CertificateExtensions();
        $objectIdentifier = new ObjectIdentifier(OID::CERT_EXT_SUBJECT_ALT_NAME);
        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $object->addSubjectAlternativeNames($sans);

        $expectedType = chr(Identifier::SET);
        $expectedLength = chr(2 + 2 + $objectIdentifier->getObjectLength() + $sans->getObjectLength());
        $expectedContent  = chr(Identifier::SEQUENCE);
        $expectedContent .= chr(2 + $objectIdentifier->getObjectLength() + $sans->getObjectLength());
        $expectedContent .= chr(Identifier::SEQUENCE);
        $expectedContent .= chr($objectIdentifier->getObjectLength() + $sans->getObjectLength());
        $expectedContent .= $objectIdentifier->getBinary();
        $expectedContent .= $sans->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new CertificateExtensions();
        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $sans->addIP(new IPAddress('192.168.0.1'));
        $originalobject->addSubjectAlternativeNames($sans);

        $binaryData = $originalobject->getBinary();
        $parsedObject = CertificateExtensions::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $objectIdentifier = new ObjectIdentifier(OID::CERT_EXT_SUBJECT_ALT_NAME);

        $originalobject1 = new CertificateExtensions();
        $sans1 = new SubjectAlternativeNames();
        $sans1->addDomainName(new DNSName('corvespace.de'));
        $sans1->addIP(new IPAddress('192.168.0.1'));
        $originalobject1->addSubjectAlternativeNames($sans1);

        $originalobject2 = new CertificateExtensions();
        $sans2 = new SubjectAlternativeNames();
        $sans2->addDomainName(new DNSName('google.com'));
        $originalobject2->addSubjectAlternativeNames($sans2);

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = CertificateExtensions::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $offsetAfterFirstObject = $sans1->getObjectLength() + $objectIdentifier->getObjectLength() + 2  + 2 + 2;
        $this->assertEquals($offsetAfterFirstObject, $offset);
        $parsedObject = CertificateExtensions::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals($offsetAfterFirstObject + $sans2->getObjectLength() + $objectIdentifier->getObjectLength() + 2  + 2 + 2, $offset);
    }
}
