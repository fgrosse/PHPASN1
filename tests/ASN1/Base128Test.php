<?php

namespace FG\tests\ASN1;

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
        return [
            [0x00000000, "\x00"],
            // self explanatory

            [0x0000003F, "\x3F"],
            // binary:  00111111
            // septets:  0111111
            // padded:  00111111
            // hex:     0x3F

            [0x0000007F, "\x7F"],
            // binary:  01111111
            // septets:  1111111
            // padded:  01111111
            // hex:     0x7F

            [0x00000080, "\x81\x00"],
            // binary:  10000000
            // septets:  0000001 00000000
            // padded:  10000001 00000000
            // hex:     0x81     0x00

            [0x00004001, "\x81\x80\x01"],
            // binary:  01000000 00000001
            // septets:       01  0000000  0000001
            // padded:  10000001 10000000 00000001
            // hex:     0x81     0x80     0x01

            [0x00002000, "\xC0\x00"],
            // binary:  00100000 00000000
            // septets:  1000000  0000000
            // padded:  11000000 00000000
            // hex:     0xC0     0x00

            [0x001FFFFF, "\xFF\xFF\x7F"],
            // binary:  00011111 11111111 11111111
            // septet:   1111111  1111111  1111111
            // padded:  11111111 11111111 01111111
            // hex:     0xFF     0xFF     0x7F

             [0x0FFFFFFF, "\xFF\xFF\xFF\x7F"],
            // binary:  00001111 11111111 11111111 11111111
            // septet:   1111111  1111111  1111111  1111111
            // padded:  11111111 11111111 11111111 01111111
            // hex:     0xFF     0xFF     0xFF     0x7F

            [0xA34B253,  "\xD1\xD2\xE4\x53"],
            // binary:  00001010 00110100 10110010 01010011
            // septet:   1010001  1010010  1100100  1010011
            // padded:  11010001 11010010 11100100 01010011
            // hex:     0xD1     0xD2     0xE4     0x53
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Malformed base-128 encoded value (0xFFFF).
     */
    public function testDecodeFailsIfLastOctetSignificantBitSet()
    {
        Base128::decode("\xFF\xFF");
    }
}
