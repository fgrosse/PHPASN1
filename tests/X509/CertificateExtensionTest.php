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

use FG\ASN1\Object;
use FG\ASN1\Universal\OctetString;
use FG\Test\ASN1TestCase;
use FG\ASN1\OID;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\X509\CertificateExtensions;
use FG\X509\KeyUsage;
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

    public function testGetIdentifier()
    {
        $object = new CertificateExtensions();
        $this->assertEquals(chr(Identifier::SET), $object->getIdentifier());
    }

    public function testGetContent()
    {
        $object = new CertificateExtensions();
        $content = $object->getContent();
        $this->assertTrue(is_array($content));
        $this->assertTrue(count($content) == 0);

        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));

        $object->addSubjectAlternativeNames($sans);
        $this->assertTrue(count($object->getContent()) == 1);
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
        $originalObject = new CertificateExtensions();
        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $sans->addIP(new IPAddress('192.168.0.1'));
        $originalObject->addSubjectAlternativeNames($sans);

        $binaryData = $originalObject->getBinary();
        $parsedObject = CertificateExtensions::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $objectIdentifier = new ObjectIdentifier(OID::CERT_EXT_SUBJECT_ALT_NAME);

        $originalObject1 = new CertificateExtensions();
        $sans1 = new SubjectAlternativeNames();
        $sans1->addDomainName(new DNSName('corvespace.de'));
        $sans1->addIP(new IPAddress('192.168.0.1'));
        $originalObject1->addSubjectAlternativeNames($sans1);

        $originalObject2 = new CertificateExtensions();
        $sans2 = new SubjectAlternativeNames();
        $sans2->addDomainName(new DNSName('google.com'));
        $originalObject2->addSubjectAlternativeNames($sans2);

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = CertificateExtensions::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $offsetAfterFirstObject = $sans1->getObjectLength() + $objectIdentifier->getObjectLength() + 2  + 2 + 2;
        $this->assertEquals($offsetAfterFirstObject, $offset);
        $parsedObject = CertificateExtensions::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals($offsetAfterFirstObject + $sans2->getObjectLength() + $objectIdentifier->getObjectLength() + 2  + 2 + 2, $offset);
    }

    private function assertCaExtensions(array $content)
    {
        /** @var Object[] $content */
        $this->assertEquals(4, count($content));

        $first = $content[0];
        $this->assertEquals(Identifier::OCTETSTRING, $first->getType());

        $second = $content[1];
        $this->assertEquals(Identifier::SEQUENCE, $second->getType());
        $secondContent = $second->getContent();
        $this->assertEquals(Identifier::OCTETSTRING, $secondContent[0]->getType());

        $third = $content[2];
        $this->assertEquals(Identifier::SEQUENCE, $third->getType());
        $thirdContent = $third->getContent();
        $this->assertEquals(Identifier::BOOLEAN, $thirdContent[0]->getType());

        $fourth = $content[3];
        $this->assertEquals(Identifier::BITSTRING, $fourth->getType());

    }

    public function testCaExtensions()
    {
        $extension = new CertificateExtensions();

        $hash = strtoupper(hash('sha256', 'test'));
        $extension->addSubjectKeyIdentifier($hash);
        $extension->addIssuerKeyIdentifier($hash);
        $extension->addCertAuthorityConstraint(true);

        $i = KeyUsage::KEY_ENCIPHERMENT | KeyUsage::NON_REPUDIATION;
        $eHex = decHex($i);
        $eHex = strlen($eHex) % 2 == 1 ? '0' . $eHex : $eHex;

        $extension->addKeyUsage($i);
        /** @var Object[] $content */
        $content = $extension->getContent();

        $this->assertCaExtensions($content);

        $this->assertEquals($hash, $content[0]->getContent());
        $this->assertEquals($hash, $content[1]->getContent()[0]->getContent());
        $this->assertTrue(true, $content[2]->getContent()[0]->getContent());
        $this->assertEquals($eHex, $content[3]->getContent());

        $serialized = $extension->getBinary();
        $parsed = CertificateExtensions::fromBinary($serialized);
        for ($i = 0; $i < count($content); $i++) {
            $this->assertEquals($content[$i], $parsed->getContent()[$i]);
        }
    }
}
