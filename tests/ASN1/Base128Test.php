<?php

namespace FG\Tests\ASN1;

use FG\ASN1\Base128;

class Base128Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getDataToDecode
     */
    public function testEncode($value, $expected)
    {
        $this->assertEquals($expected, Base128::encode($value));
    }

    /**
     * @dataProvider getDataToDecode
     */
    public function testDecode($expected, $octets)
    {
        $this->assertEquals($expected, Base128::decode($octets));
    }

    public function getDataToDecode()
    {
        return array(
            array(0x00000000, "\x00"),
            array(0x0000007F, "\x7F"),
            array(0x00000080, "\x81\x00"),
            array(0x00002000, "\xC0\x00"),
            array(0x001FFFFF, "\xFF\xFF\x7F"),
            array(0x0FFFFFFF, "\xFF\xFF\xFF\x7F"),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Malformed base-128 encoded value (0xFFFF).
     */
    public function testDecodeFailsIfLastOctetSignificantBitSet()
    {
        Base128::decode("\xFF\xFF");
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value (0xFFFFFFFFFFFFFFFFFFFF7F) exceeds the maximum integer length when base128-decoded.
     */
    public function testDecodeFailsIfOverflows()
    {
        Base128::decode("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x7F");
    }
}
