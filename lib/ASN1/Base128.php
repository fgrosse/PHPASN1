<?php

namespace FG\ASN1;
use InvalidArgumentException;

/**
 * A base-128 decoder.
 */
class Base128
{
    /**
     * @param integer $value
     *
     * @return string
     */
    public static function encode($value)
    {
        $value = gmp_init($value, 10);
        $octets = chr(gmp_strval(gmp_and($value, 0x7f), 10));

        $rshift = function ($number, $positions) {
            return gmp_div($number, gmp_pow(2, (int)$positions));
        };

        $value = $rshift($value, 7);
        while (gmp_cmp($value, 0) > 0) {
            $octets .= chr(gmp_strval(gmp_or(0x80, gmp_and($value, 0x7f)), 10));
            $value = $rshift($value, 7);
        }

        return strrev($octets);
    }

    /**
     * @param string $octets
     *
     * @return int
     * @throws InvalidArgumentException if the given octets represent a malformed base-128 value or the decoded value would exceed the the maximum integer length
     */
    public static function decode($octets)
    {
        $bitsMax = (PHP_INT_SIZE * 8) - 1;
        $bitsUsed = 0;
        $bitsPerOctet = 7;
        $value = 0;
        $i = 0;

        while (true) {
            if (!isset($octets[$i])) {
                throw new InvalidArgumentException(sprintf('Malformed base-128 encoded value (0x%s).', strtoupper(bin2hex($octets)) ?: '0'));
            }

            $bitsUsed += $bitsPerOctet;
            if ($bitsUsed > $bitsMax) {
                throw new InvalidArgumentException(sprintf('Value (0x%s) exceeds the maximum integer length when base128-decoded.', strtoupper(bin2hex($octets))));
            }

            $octet = ord($octets[$i++]);
            $value = ($value << $bitsPerOctet) + ($octet & 0x7F);

            if (0 === ($octet & 0x80)) {
                break;
            }
        }

        return $value;
    }
}
