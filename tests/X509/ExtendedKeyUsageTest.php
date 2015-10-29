<?php

namespace FG\Test\X509;


use FG\Test\ASN1TestCase;
use FG\X509\ExtendedKeyUsage;
use FG\X509\ExtendedUsageMap;

class ExtendedKeyUsageTest extends ASN1TestCase
{
    public function testKeyPurpose()
    {
        $oid = ExtendedUsageMap::getOid(ExtendedUsageMap::CODESIGNING);
        $purpose = new ExtendedKeyUsage();
        $purpose->addKeyPurpose($oid);
        $purposes = $purpose->getKeyPurposes();
        $this->assertEquals($oid, $purposes[0]);
    }

    public function testFromBinary()
    {
        $usage = new ExtendedKeyUsage();
        foreach ([
            ExtendedUsageMap::CODESIGNING,
            ExtendedUsageMap::SERVERAUTH
        ] as $purpose) {
            $usage->addKeyPurpose(ExtendedUsageMap::getOid($purpose));
        }

        $binary = $usage->getBinary();
        $parsed = ExtendedKeyUsage::fromBinary($binary);
        $this->assertEquals($usage, $parsed);
    }
}