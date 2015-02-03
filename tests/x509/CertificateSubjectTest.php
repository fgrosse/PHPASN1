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

namespace FG\Test\X509;

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\X509\CertificateSubject;

class CertificateSubjectTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new CertificateSubject("Friedrich Große", "friedrich.grosse@foo.de", "Organization", "Locality", "State", "Country", "OrgaUnit");
        $this->assertEquals(Identifier::SEQUENCE, $object->getType());
    }
    /*
    public function testFromBinary() {
        $originalobject = new CertificateSubject("Friedrich Große", "friedrich.grosse@foo.de", "Organization", "Locality", "State", "Country", "OrgaUnit");

        $binaryData = $originalobject->getBinary();
        $parsedObject = CertificateSubject::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    /*public function testFromBinaryWithOffset() {
        $objectIdentifier = new ASN_ObjectIdentifier(OID::CERT_EXT_SUBJECT_ALT_NAME);

        $originalobject1 = new CertificateExtensions();
        $sans1 = new SubjectAlternativeNames();
        $sans1->addDomainName(new SAN_DNSName('corvespace.de'));
        $sans1->addIP(new SAN_IPAddress('192.168.0.1'));
        $originalobject1->addSubjectAlternativeNames($sans1);

        $originalobject2 = new CertificateExtensions();
        $sans2 = new SubjectAlternativeNames();
        $sans2->addDomainName(new SAN_DNSName('google.com'));
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
    }*/
}
