<?php

namespace FG\Test\X509;


use FG\Test\ASN1TestCase;
use FG\X509\ExtendedUsageMap;

class KeyPurposeMapTest extends ASN1TestCase
{
    public function testGetNames()
    {
        $names = ExtendedUsageMap::getNames();
        $oid = ExtendedUsageMap::getOid($names[0]);
        $this->assertInternalType('array', $names);
        $this->assertInstanceOf('FG\ASN1\Universal\ObjectIdentifier', $oid);

        $name = ExtendedUsageMap::getNameFromOid($oid);
        $this->assertInternalType('string', $name);
        $this->assertTrue(in_array($name, $names));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetOidInvalidString()
    {
        ExtendedUsageMap::getOid('123');
    }

    public function testGetOid()
    {
        $expectedOid = '1.3.6.1.5.5.7.3.1';
        $oid = ExtendedUsageMap::getOid('serverAuth');
        $this->assertEquals($expectedOid, $oid->getContent());
    }
}