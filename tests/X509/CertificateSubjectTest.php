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

use FG\ASN1\OID;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\X509\CertificateExtensions;
use FG\X509\CertificateSubject;
use FG\X509\SAN\DNSName;
use FG\X509\SAN\IPAddress;
use FG\X509\SAN\SubjectAlternativeNames;

class CertificateSubjectTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new CertificateSubject('Friedrich Große', 'friedrich.grosse@foo.de', 'Organization', 'Locality', 'State', 'Country', 'OrgaUnit');
        $this->assertEquals(Identifier::SEQUENCE, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new CertificateSubject('Friedrich Große', 'friedrich.grosse@foo.de', 'Organization', 'Locality', 'State', 'Country', 'OrgaUnit');
        $this->assertEquals(chr(Identifier::SEQUENCE), $object->getIdentifier());
    }

    public function testFromBinary()
    {
        $this->markTestIncomplete('Not implemented');
        $originalObject = new CertificateSubject('Friedrich Große', 'friedrich.grosse@foo.de', 'Organization', 'Locality', 'State', 'Country', 'OrgaUnit');

        $binaryData = $originalObject->getBinary();
        $parsedObject = CertificateSubject::fromBinary($binaryData);
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
}
