<?php

namespace FG\Test\X509;

use FG\Test\ASN1TestCase;
use FG\X509\KeyUsage;

class KeyUsageTest extends ASN1TestCase
{
    public function testFlagsAndKeys()
    {
        $this->assertEquals(KeyUsage::DIGITAL_SIGNATURE, KeyUsage::makeFlagsFromNames(['digitalSignature']));
        $this->assertEquals(KeyUsage::NON_REPUDIATION, KeyUsage::makeFlagsFromNames(['nonRepudiation']));
        $this->assertEquals(KeyUsage::KEY_ENCIPHERMENT, KeyUsage::makeFlagsFromNames(['keyEncipherment']));
        $this->assertEquals(KeyUsage::DATA_ENCIPHERMENT, KeyUsage::makeFlagsFromNames(['dataEncipherment']));
        $this->assertEquals(KeyUsage::KEY_AGREEMENT, KeyUsage::makeFlagsFromNames(['keyAgreement']));
        $this->assertEquals(KeyUsage::KEY_CERT_SIGN, KeyUsage::makeFlagsFromNames(['keyCertSign']));
        $this->assertEquals(KeyUsage::CRL_SIGN, KeyUsage::makeFlagsFromNames(['cRLSign']));
        $this->assertEquals(KeyUsage::DECIPHER_ONLY, KeyUsage::makeFlagsFromNames(['decipherOnly']));
        $this->assertEquals(KeyUsage::ENCIPHER_ONLY, KeyUsage::makeFlagsFromNames(['encipherOnly']));
    }

    public function testWorksForKnownNames()
    {
        $names = KeyUsage::getNames();
        foreach ($names as $name) {
            $this->assertTrue(is_numeric(KeyUsage::makeFlagsFromNames([$name])));
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testRejectsUnknownNames()
    {
        KeyUsage::makeFlagsFromNames(['unknown']);
    }

    public function testDecodesFlags()
    {
        $names = KeyUsage::getNames();
        $flags = KeyUsage::makeFlagsFromNames($names);
        $extractedFlags = KeyUsage::decodeFlags($flags);
        $this->assertEquals($names, $extractedFlags);
    }
}