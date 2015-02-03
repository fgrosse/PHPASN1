<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2013
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

    public function testGetContent()
    {
        $attributes = new Attributes();
        $content = $attributes->getContent();
        $this->assertTrue(is_array($content));
        $this->assertEquals(0, sizeof($content));

        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $extensionRequest = new CertificateExtensions();
        $extensionRequest->addSubjectAlternativeNames($sans);

        $attributes->addAttribute(OID::PKCS9_EXTENSION_REQUEST, $extensionRequest);
        $content = $attributes->getContent();
        $this->assertTrue(is_array($content));
        $this->assertEquals(1, sizeof($content));

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
        $originalobject = new Attributes();
        $sans = new SubjectAlternativeNames();
        $sans->addDomainName(new DNSName('corvespace.de'));
        $extensionRequest = new CertificateExtensions();
        $extensionRequest->addSubjectAlternativeNames($sans);
        $originalobject->addAttribute(OID::PKCS9_EXTENSION_REQUEST, $extensionRequest);

        $binaryData = $originalobject->getBinary();
        $parsedObject = Attributes::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }
}
