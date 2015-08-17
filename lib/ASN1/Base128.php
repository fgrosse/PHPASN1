<?php

namespace FG\ASN1;

use InvalidArgumentException;

/**
 * A base-128 decoder.
 */
class Base128
{
    /**
     * @param int $value
     *
     * @return string
     */
    public static function encode($value)
    {
        $value = gmp_init($value, 10);
        $octets = chr(gmp_strval(gmp_and($value, 0x7f), 10));

        $rightShift = function ($number, $positions) {
            return gmp_div($number, gmp_pow(2, $positions));
        };

        $value = $rightShift($value, 7);
        while (gmp_cmp($value, 0) > 0) {
            $octets .= chr(gmp_strval(gmp_or(0x80, gmp_and($value, 0x7f)), 10));
            $value = $rightShift($value, 7);
        }

        return strrev($octets);
    }

    /**
     * @param string $octets
     *
     * @throws InvalidArgumentException if the given octets represent a malformed base-128 value or the decoded value would exceed the the maximum integer length
     *
     * @return int
     */
    public static function decode($octets)
    {
        $bitsPerOctet = 7;
        $value = gmp_init(0, 10);
        $i = 0;

        $leftShift = function ($number, $positions) {
            return gmp_mul($number, gmp_pow(2, $positions));
        };

        while (true) {
            if (!isset($octets[$i])) {
                throw new InvalidArgumentException(sprintf('Malformed base-128 encoded value (0x%s).', strtoupper(bin2hex($octets)) ?: '0'));
            }

            $octet = gmp_init(ord($octets[$i++]), 10);

            $l1 = $leftShift($value, $bitsPerOctet);
            $r1 = gmp_and($octet, 0x7f);
            $value = gmp_add($l1, $r1);

            if (0 === gmp_cmp(gmp_and($octet, 0x80), 0)) {
                break;
            }
        }

        return gmp_strval($value);
    }
}
