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
use FG\X509\CertificateSubject;

class CertificateSubjectTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new CertificateSubject("Friedrich Große", "friedrich.grosse@foo.de", "Organization", "Locality", "State", "Country", "OrgaUnit");
        $this->assertEquals(Identifier::SEQUENCE, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new CertificateSubject("Friedrich Große", "friedrich.grosse@foo.de", "Organization", "Locality", "State", "Country", "OrgaUnit");
        $this->assertEquals(chr(Identifier::SEQUENCE), $object->getIdentifier());
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
