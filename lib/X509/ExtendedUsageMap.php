<?php

namespace FG\X509;


use FG\ASN1\OID;
use FG\ASN1\Universal\ObjectIdentifier;

class ExtendedUsageMap
{
    const OID = OID::CERT_EXT_EXTENDED_KEY_USAGE;

    const SERVERAUTH = 'serverAuth';
    const CLIENTAUTH = 'clientAuth';
    const CODESIGNING = 'codeSigning';
    const EMAILPROTECTION = 'emailProtection';
    const TIMESTAMPING = 'timestamping';
    const OCSPSigning = 'OSCPSigning';

    /**
     * @var array
     */
    private static $keyMap = [
        self::SERVERAUTH => '1.3.6.1.5.5.7.3.1',
        self::CLIENTAUTH => '1.3.6.1.5.5.7.3.2',
        self::CODESIGNING => '1.3.6.1.5.5.7.3.3',
        self::EMAILPROTECTION => '1.3.6.1.5.5.7.3.4',
        self::TIMESTAMPING => '1.3.6.1.5.5.7.3.8',
        self::OCSPSigning => '1.3.6.1.5.5.7.3.9'
    ];

    /**
     * @return string[]
     */
    public static function getNames()
    {
        return array_keys(self::$keyMap);
    }

    /**
     * @param string $keyPurpose
     * @return ObjectIdentifier
     */
    public static function getOid($keyPurpose)
    {
        if (!is_string($keyPurpose) || !isset(self::$keyMap[$keyPurpose])) {
            throw new \RuntimeException('Invalid Extended Key Usage purpose');
        }

        return new ObjectIdentifier(self::$keyMap[$keyPurpose]);
    }

    /**
     * @param ObjectIdentifier $oid
     * @return string
     */
    public static function getNameFromOid(ObjectIdentifier $oid)
    {
        $oidContent = $oid->getContent();
        $flipped = array_flip(self::$keyMap);
        if (!isset($flipped[$oidContent])) {
            throw new \RuntimeException('Invalid Extended Key Usage oid');
        }

        return $flipped[$oidContent];
    }
}