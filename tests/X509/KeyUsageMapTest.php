<?php

namespace FG\Test\X509;

use FG\Test\ASN1TestCase;
use FG\X509\KeyUsageMap;

class KeyUsageMapTest extends ASN1TestCase
{
    public function testFlagsAndKeys()
    {
        $this->assertEquals(KeyUsageMap::DIGITAL_SIGNATURE, KeyUsageMap::makeFlagsFromNames(['digitalSignature']));
        $this->assertEquals(KeyUsageMap::NON_REPUDIATION, KeyUsageMap::makeFlagsFromNames(['nonRepudiation']));
        $this->assertEquals(KeyUsageMap::KEY_ENCIPHERMENT, KeyUsageMap::makeFlagsFromNames(['keyEncipherment']));
        $this->assertEquals(KeyUsageMap::DATA_ENCIPHERMENT, KeyUsageMap::makeFlagsFromNames(['dataEncipherment']));
        $this->assertEquals(KeyUsageMap::KEY_AGREEMENT, KeyUsageMap::makeFlagsFromNames(['keyAgreement']));
        $this->assertEquals(KeyUsageMap::KEY_CERT_SIGN, KeyUsageMap::makeFlagsFromNames(['keyCertSign']));
        $this->assertEquals(KeyUsageMap::CRL_SIGN, KeyUsageMap::makeFlagsFromNames(['cRLSign']));
        $this->assertEquals(KeyUsageMap::DECIPHER_ONLY, KeyUsageMap::makeFlagsFromNames(['decipherOnly']));
        $this->assertEquals(KeyUsageMap::ENCIPHER_ONLY, KeyUsageMap::makeFlagsFromNames(['encipherOnly']));
    }

    public function testWorksForKnownNames()
    {
        $names = KeyUsageMap::getNames();
        foreach ($names as $name) {
            $this->assertTrue(is_numeric(KeyUsageMap::makeFlagsFromNames([$name])));
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testRejectsUnknownNames()
    {
        KeyUsageMap::makeFlagsFromNames(['unknown']);
    }

    /**
     * @expectedException \Exception
     */
    public function testDecodeRejectsNonIntegers()
    {
        KeyUsageMap::decodeFlags('unknown');
    }

    public function testDecodesFlags()
    {
        $names = KeyUsageMap::getNames();
        $flags = KeyUsageMap::makeFlagsFromNames($names);
        $extractedFlags = KeyUsageMap::decodeFlags($flags);
        $this->assertEquals($names, $extractedFlags);
    }
}