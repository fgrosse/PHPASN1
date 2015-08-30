<?php

namespace FG\X509;


use FG\ASN1\OID;

class KeyUsage
{
    const OID = OID::CERT_EXT_KEY_USAGE;
    const BITMAP_SIZE = 8;
    const DIGITAL_SIGNATURE = 0;
    const NON_REPUDIATION = 1;
    const KEY_ENCIPHERMENT = 2;
    const DATA_ENCIPHERMENT = 3;
    const KEY_AGREEMENT = 4;
    const KEY_CERT_SIGN = 5;
    const CRL_SIGN = 6;
    const ENCIPHER_ONLY = 7;
    const DECIPHER_ONLY = 8;

    static private $keyMap = [
        'digitalSignature' => self::DIGITAL_SIGNATURE,
        'nonRepudiation' => self::NON_REPUDIATION,
        'keyEncipherment' => self::KEY_ENCIPHERMENT,
        'dataEncipherment' => self::DATA_ENCIPHERMENT,
        'keyAgreement' => self::KEY_AGREEMENT,
        'keyCertSign' => self::KEY_CERT_SIGN,
        'cRLSign' => self::CRL_SIGN,
        'encipherOnly' => self::ENCIPHER_ONLY,
        'decipherOnly' => self::DECIPHER_ONLY
    ];

    /**
     * @return string[]
     */
    public static function getNames()
    {
        return array_keys(self::$keyMap);
    }

    /**
     * @param array $keys
     * @return int
     */
    public static function makeFlagsFromNames(array $keys)
    {
        $flags = 0;
        foreach ($keys as $indicator) {
            if (!is_string($indicator) || !isset(self::$keyMap[$indicator])) {
                throw new \RuntimeException('Invalid KeyUsage indicator');
            }
            $flags |= self::$keyMap[$indicator];
        }

        return $flags;
    }

    /**
     * @param int $flags
     * @return string[]
     */
    public static function decodeFlags($flags)
    {
        if (!is_numeric($flags)) {
            throw new \RuntimeException('Flags must be an integer');
        }

        $reverseMap = array_flip(self::$keyMap);
        $found = [];
        for ($testBit = 0; $testBit <= self::BITMAP_SIZE; $testBit++) {
            if ($flags & $testBit == $testBit) {
                $found[] = $reverseMap[$testBit];
            }
        }

        return $found;
    }
}