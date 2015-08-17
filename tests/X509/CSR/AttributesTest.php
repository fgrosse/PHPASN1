<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\X509\CSR;

use FG\Test\ASN1TestCase;
use FG\ASN1\OID;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\X509\CSR\Attributes;
use FG\X509\CertificateExtensions;
use FG\X509\SAN\DNSName;
use FG\X509\SAN\SubjectAlternativeNames;

class AttributesTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new Attributes();
        $this->assertEquals(0xa0, $object->getType());  // Identifier indicates this object is context specific and constructed
    }

    public function testGetIdentifier()
    {
        $object = new Attributes();
        $this->assertEquals(chr(0xa0), $object->getIdentifier());
    }

    public function testGetContent()
    {
        $attributes = new Attributes();
        $content = $attributes->getContent();
        $this->assertTrue(is_array($content));
        $this->assertEquals(0, count($content));

        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $extensionRequest = new CertificateExtensions();
        $extensionRequest->addSubjectAlternativeNames($sans);

        $attributes->addAttribute(OID::PKCS9_EXTENSION_REQUEST, $extensionRequest);
        $content = $attributes->getContent();
        $this->assertTrue(is_array($content));
        $this->assertEquals(1, count($content));

        /** @var Sequence $attribute */
        $attribute = $content[0];
        $this->assertTrue($attribute instanceof Sequence);
        $this->assertEquals(2, $attribute->getNumberofChildren());
        $attributeArray = $attribute->getChildren();

        $objectIdentifier = $attributeArray[0];
        $this->assertTrue($objectIdentifier instanceof ObjectIdentifier);
        $this->assertEquals(OID::PKCS9_EXTENSION_REQUEST, $objectIdentifier->getContent());

        $this->assertEquals($extensionRequest, $attributeArray[1]);
    }

    public function testFromBinary()
    {
        $originalObject = new Attributes();
        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $extensionRequest = new CertificateExtensions();
        $extensionRequest->addSubjectAlternativeNames($sans);
        $originalObject->addAttribute(OID::PKCS9_EXTENSION_REQUEST, $extensionRequest);

        $binaryData = $originalObject->getBinary();
        $parsedObject = Attributes::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }
}
