<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/hexdump.php';
require_once __DIR__.'/../vendor/autoload.php';

use FG\ASN1\OID;
use FG\ASN1\Object;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\NumericString;
use FG\ASN1\Universal\CharacterString;
use FG\ASN1\Universal\UTF8String;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\UTCTime;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Composite\AttributeTypeAndValue;
use FG\ASN1\Composite\RDNString;
use FG\ASN1\Composite\RelativeDistinguishedName;
use FG\X509\CertificateSubject;
use FG\X509\PublicKey;
use FG\X509\AlgorithmIdentifier;
use FG\X509\CSR\CSR;

// check if openssl is installed on this system
$openSSLVersionOutput = shell_exec('openssl version');
if (substr($openSSLVersionOutput, 0, 7) == 'OpenSSL') {
    $openSSLisAvailable = true;
} else {
    $openSSLisAvailable = false;
}

function printVariableInfo(Object $variable)
{
    $className = get_class($variable);
    $stringValue = nl2br($variable->__toString());
    $binaryData = $variable->getBinary();
    $base64Binary = chunk_split(base64_encode($binaryData), 24);

    echo '<tr>';
    echo "<td class='ASNclass'>{$className}</td>";
    echo "<td class='toString'>\"<span class='red'>{$stringValue}</span>\"</td>";
    echo "<td class='monospace base64'>{$base64Binary}</td>";
    echo '<td>'.hexdump($binaryData, true, true, true).'</td>';

    global $openSSLisAvailable;
    if ($openSSLisAvailable) {
        $openSSLOutput = shell_exec("echo '{$base64Binary}' | openssl asn1parse -inform PEM -dump -i 2>&1");
        echo "<td class='openSSL'><pre>{$openSSLOutput}</pre></td>";
    }
    echo '</tr>';
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="Examples of all available PHPASN1 ASN classes">
    <meta name="author" content="Friedrich Große">
    <link rel="stylesheet" href="common.css"/>
    <title>PHPASN1 Classes</title>
</head>
<body>
    <h1>Examples for all available PHPASN1 classes</h1>

    <?php
    if ($openSSLisAvailable == false) {
        echo "<p class='notice'>Note: OpenSSL could not be found on this system. This is absolutely no problem
        for PHPASN1 but it could have been used to show you that the generated binaries are indeed correct :)</p>";
    }
    ?>

    <table>
        <th>Class</th>
        <th>toString()</th>
        <th>Binary (base64)</th>
        <th>Binary (hex)</th>

        <?php
            if ($openSSLisAvailable) {
                echo '<th>OpenSSL asn1parse output</th>';
            }

            // Primitives
            printVariableInfo(new Boolean(true));
            printVariableInfo(new Boolean(false));
            printVariableInfo(new Integer(123456));
            printVariableInfo(new Integer(-546));
            printVariableInfo(new Enumerated(1));
            printVariableInfo(new PrintableString('Hello World'));
            printVariableInfo(new NumericString('123456'));
            printVariableInfo(new CharacterString('Foo'));
            printVariableInfo(new UTF8String('Hello ♥♥♥ World'));
            // there are more restricted character string classes available

            printVariableInfo(new BitString('3082010a02820101009e2a7'));
            printVariableInfo(new OctetString('x01020304AE123200A0'));
            printVariableInfo(new ObjectIdentifier(OID::PKCS9_EMAIL));
            printVariableInfo(new NullObject());
            printVariableInfo(new UTCTime('2012-09-30 14:33'));
            printVariableInfo(new GeneralizedTime('2008-07-01 22:35:17.024540+06:30'));

            // Constructed
            printVariableInfo(new Sequence(
                new ObjectIdentifier(OID::COMMON_NAME),
                new PrintableString('Friedrich')
            ));

            printVariableInfo(new Set(
                new ObjectIdentifier(OID::COUNTRY_NAME),
                new PrintableString('Germany')
            ));

            printVariableInfo(new AttributeTypeAndValue(
                OID::ORGANIZATION_NAME,
                new PrintableString('My Organization')
            ));

            printVariableInfo(new RelativeDistinguishedName(
                OID::RSA_ENCRYPTION,
                new Integer(62342)
            ));

            printVariableInfo(new RDNString(
                OID::COMMON_NAME,
                'Hello World'
            ));

            // X509
            printVariableInfo(new CertificateSubject(
                'Friedrich Grosse',
                'friedrich.grosse@gmail.com',
                'PHPASN1',
                'Berlin',
                'Berlin',
                'DE',
                'Development Department'
            ));

            printVariableInfo(new PublicKey(
                '00 00 00 01 23 00 00 01 01 00 E7 40 F2 69 15 D5 4A DD 24 A1 C3
                 0C 93 38 03 1A 49 70 EB 76 B0 F5 1B BF 42 97 BA E1 F3 61 CC E1
                 63 0A BA 0F 66 6D 30 4E 31 67 65 39 E1 2E 93 A7 E7 3E 1E 76 10
                 81 5A 41 C6 DF 12 05 46 AC 61 A9 29 A3 BD 1A 16 35 38 DF 75 F6
                 0A 25 F7 3A F5 D3 C6 EB 84 7D 40 D4 1E 56 CA 0B 46 28 F7 79 D6
                 08 BB 1B 01 C7 2A 14 93 B2 F7 89 CB 11 F3 4B E6 5F 7A 2B 22 F4
                 14 CB 36 47 40 89 45 FC 14 03 1D 8B 31 AF 4C 57 63 7D 87 FD 95
                 0C ED 64 C1 BF 8F FC E2 76 6C 90 A3 4F 50 72 9D AB 01 1E 84 7A
                 63 95 9A 2E 0E 59 F1 AF A9 D0 6E AB 8E 45 96 80 AD 57 26 04 2F
                 B7 B0 41 69 2B 7A BD F9 2F FE 60 99 A9 81 FF 94 A5 8E 5B 7F A8
                 C0 63 4B F3 79 F4 F7 3C 27 81 FF 90 5C C6 04 BC C2 C1 62 2E 47
                 8A EF 6F 95 53 89 99 34 7C 4C B4 AE A0 E8 AD C1 04 B8 B6 E2 02
                 BB 65 93 39 42 F9 FD 69 20 E3 CF EF 49 E9'
            ));

            printVariableInfo(new AlgorithmIdentifier(OID::SHA1_WITH_RSA_SIGNATURE));

            printVariableInfo($csr = new CSR(
                'Friedrich Grosse',
                'friedrich.grosse@gmail.com',
                'PHPASN1',
                'Berlin',
                'Berlin',
                'DE',
                'Development Department',
                '3082010a02820101009e2a7defae93720c91c43c46ff4a1f2e8eef7949289e281f788f3a07d9b94da26fb2e721009caceddd0e6b59daa596df20f871fc30a43f4b80798f94fa3d13cb2db79eb6d8f07b4065d0b09a541564ba3baa1201e20ee923ea16be31fa785c300635c4e881df7acb5b52c7c3d923067902cc55e77c00694f319d2b9e81edbbfe70ef1a462aef4960c567f33aa5264a05fdf24cd7bc36941cd7746fb767631a241b7a97fc4cdc42a68692b906406403599380c7586ce6f22fac34949caf1072c724ba5397e6440f957e2678c3a4bc92268fe6815d41fa210ab45364c11e3731c6c039832b54f54b51fdaf6afb351e1da9720b3c322f7fbaefb72d96d4ce5ec07b0203010001',
                '4adf270d347047192573cf245a94cd2e69594c1cdac1d7c99d7ed5856c926ee62c65188f21d893e634b213595cc4564d5a8d39bed0ca01e0b45e3182ab89310c129017f2a7a68d8603694ddc8d1c2ebfee39b3b5dfc9dbc2db667a089b1b51386f2cf7ec70140d185bae5c2f3b3148b9ef613ce068f94db13a230b1133e4b4a48ec5c8b4066d64a2199c0cfb6c4d0cfe105f21a89b2900d0a5c87bef5eded941ba93ae1b7e84aaeabcb46fa4a3844ffc683ebb4ee80717ff51cba5d82afe9d2633b760a66449e57e06d73eeeb151bc050a66825996d7f5ec821d31891c620a677c8271db13bbc22fcf91e1b7ac8f6f109eb8e3a2c61a3c8a4336b40a499e1404'
            ));
        ?>
    </table>
</body>
</html>
