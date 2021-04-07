<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\X509\CSR;

use FG\ASN1\ASNObject;
use FG\X509\CSR\CSR;
use FG\Test\ASN1TestCase;
use FG\ASN1\OID;

class CSRSignatureTest extends ASN1TestCase
{
    private $pem = '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEA06GG0gy12nbyp5yiuNfsgBAt4uRWmUgkA1kQuCdDoFhLTjMs
ROreQJGKVbyPMiY1HwML/XaC0NRZtqMssrwmKvh7KGb6QEmz7dCVdk/eU7IF480/
93BZmGYH2sdqG40pTwblagw8D21Cx+wTwHHkAmX4Rzc86Cw+GPSAgh0kD9TygEtJ
xcA/xd/NUHj5FlwDclxGZH7B89g/OSVWOqrOYFpQfl5SSTAMc1bgyUQMW1rh5sIR
W0xuCUCIcBsaoibYGE/R/5TPdoMhqYAmbyXlJAED8eozIeY5XKaWe+jnr+XlRkFY
UOJbkt+91slQ+WS7zWMS2UVzkZT7ha7cgR+fIwIDAQABAoIBADCjUO75iNn6uG4Z
K7S7u1j7XvBkdhqoX859K8CHFZ3GbRg93pDAQfApAtgNjAOEkHTeKGVKVWpVQ4Ec
I2u4njUGApgYgq8/wSCI7bDRTlGB+qSOSYM+yPijim6XoiVO3g2Rkiw7P5p0DAMS
mL+D7Vk3wkXrxg2+DU+C8f31YJJafY+1IW1GfNWsbUxzzN4k31Txxo3FNI0t+MlV
tzmdwxQYiHXrHa5etqidH2KP/uelkyHPBNp3LCYWpnMA0BnnmKEWq4HvqrsmuZbh
RA3yaXd1tqwuBf5lVIC3ertdADhRTNtOn3R7N8sygT5mhNDXnpvYC/m60GshTbcz
pYqO0EECgYEA79AEp7FJwei8XAsJ1Ah73ZDV7BRrM6ymHLqJg4ob4SuFt5D62enM
zjeOZHccO2p0iGQkKvHY5CoOx1NZZRZCfALzxdM/2r5P24DOkmoN4Bz9Ye0Rb8MQ
nMOpCK6aA+62/w5hzyHNgH66ILpP2NlUJJXKnqdRU+FjA3UArr8c5BMCgYEA4eqH
Idoh8xozvhR/8uG5Ksdu+WgucEo2WbXwwvZc4JjyKWOfcLujMzs1gCrezwJulGVy
yS/VQevtlu7g/29GdDTqtJA3gb3rmsYZlJPA+VYHB6jIRhDVFie6BQAhycux9Kem
hFaR172HMxsv0fH2j790JxG3CUECZg6vhpyUGrECgYEAkJ1wATQvV2kjFZpufdfz
4kQyTOBvWUUXEyIGRTWm5F2bkHw0We7c28qy6rDNbKMgzRMeoGshsU4PhXIk6YnH
5ALfwH9I9X9opuUBLVgZL/orbr9IkY4fWXnAWIT6Sb7NyfeBUih/RaqmUZApEIrW
bA2Ml6osqBm13OLU24xSPtMCgYAKpfRCmzZ4b+a6cdrqBsukgYvwg4GU11qiddno
RpZwG6VmYAS22pFBq+vEo8CmvzWZFwMHCpMhrU4gjBfc0lDez1O0uQt0uQ/u1qGE
CEbTOcPRD5qI+uggSDUTYUM2cLxtjK1jlXUAVzWVKhXh7maxtdNyt/oJnJ/RUaXi
UWUFsQKBgQCAuOnqi4CntD28a8rXCFK97YAmTsRI6Ax14AaoMf8hGRc69CeU1Od3
kKnF0o/HC1mdL4ADeup/OfHZiXpCDuoG6N7MS5QxszbPVkZjznlNxJLu3oDdO48u
dd0uSrw8HCy5VnOIpNvxsjCQs/LtPB4cJgZeUx91Yod1Fg2C04Nbnw==
-----END RSA PRIVATE KEY-----';

    private $cert = '-----BEGIN CERTIFICATE REQUEST-----
MIICRzCCAbMCAQAwgYcxCzAJBgNVBAYTAlVTMQkwBwYDVQQIEwAxFjAUBgNVBAcT
DVNhbiBGcmFuc2lzY28xDzANBgNVBAoTBkdpdGh1YjELMAkGA1UECxMCSVQxEzAR
BgNVBAMTCmdpdGh1Yi5vcmcxIjAgBgkqhkiG9w0BCQEWE25vLXJlcGx5QGdpdGh1
Yi5vcmcwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDToYbSDLXadvKn
nKK41+yAEC3i5FaZSCQDWRC4J0OgWEtOMyxE6t5AkYpVvI8yJjUfAwv9doLQ1Fm2
oyyyvCYq+HsoZvpASbPt0JV2T95TsgXjzT/3cFmYZgfax2objSlPBuVqDDwPbULH
7BPAceQCZfhHNzzoLD4Y9ICCHSQP1PKAS0nFwD/F381QePkWXANyXEZkfsHz2D85
JVY6qs5gWlB+XlJJMAxzVuDJRAxbWuHmwhFbTG4JQIhwGxqiJtgYT9H/lM92gyGp
gCZvJeUkAQPx6jMh5jlcppZ76Oev5eVGQVhQ4luS373WyVD5ZLvNYxLZRXORlPuF
rtyBH58jAgMBAAEwDQYJKoZIhvcNAQELBQADfwAKAAAEAQAAAAAACAAABQAAAAAA
AAAAAAAAAgAAAAAAAAgABQAHAAAAAAAAANcAAAAAAAAAAAsAfgwAAAAAAAALAAAN
DQAADwAAAAANAAAAAAAACwALAAAAAPYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
AAAAAAAAAAAAAAg=
-----END CERTIFICATE REQUEST-----';

    public function testSignatureSubject()
    {
        $private_key = openssl_pkey_get_private($this->pem);
        $pem = openssl_pkey_get_details($private_key)['key'];
        $pemparts = explode("\n", trim($pem));
        array_pop($pemparts);
        array_shift($pemparts);
        $binarykey = base64_decode(implode('', $pemparts));
        $asn1KeyWrapper = ASNObject::fromBinary($binarykey);
        
        // Now get the key out in hex format
        $asn1Key = $asn1KeyWrapper->getContent();
        $hexkey = $asn1Key[1]->getContent();
        
        $csr = new CSR('github.org', 'no-reply@github.org', 'Github', 'San Fransisco', '', 'US', 'IT', $hexkey);
        $digest = $csr->getSignatureSubject();
        $this->assertEquals(bin2hex($digest), '308201b3020100308187310b30090603550406130255533109300706035504081300311630140603550407130d53616e204672616e736973636f310f300d060355040a1306476974687562310b3009060355040b13024954311330110603550403130a6769746875622e6f72673122302006092a864886f70d01090116136e6f2d7265706c79406769746875622e6f726730820122300d06092a864886f70d01010105000382010f003082010a0282010100d3a186d20cb5da76f2a79ca2b8d7ec80102de2e456994824035910b82743a0584b4e332c44eade40918a55bc8f3226351f030bfd7682d0d459b6a32cb2bc262af87b2866fa4049b3edd095764fde53b205e3cd3ff77059986607dac76a1b8d294f06e56a0c3c0f6d42c7ec13c071e40265f847373ce82c3e18f480821d240fd4f2804b49c5c03fc5dfcd5078f9165c03725c46647ec1f3d83f3925563aaace605a507e5e5249300c7356e0c9440c5b5ae1e6c2115b4c6e094088701b1aa226d8184fd1ff94cf768321a980266f25e5240103f1ea3321e6395ca6967be8e7afe5e546415850e25b92dfbdd6c950f964bbcd6312d945739194fb85aedc811f9f230203010001');
    }

    public function testAddSignature()
    {
        $private_key = openssl_pkey_get_private($this->pem);
        $pem = openssl_pkey_get_details($private_key)['key'];
        $pemparts = explode("\n", trim($pem));
        array_pop($pemparts);
        array_shift($pemparts);
        $binarykey = base64_decode(implode('', $pemparts));
        $asn1KeyWrapper = ASNObject::fromBinary($binarykey);

        // Now get the key out in hex format
        $asn1Key = $asn1KeyWrapper->getContent();
        $hexkey = $asn1Key[1]->getContent();

        $csr = new CSR('github.org', 'no-reply@github.org', 'Github', 'San Fransisco', '', 'US', 'IT', $hexkey);
        $digest = $csr->getSignatureSubject();
        $signature = null;
        openssl_sign($digest, $signature, $private_key, OPENSSL_ALGO_SHA256);
        $csr->setSignature($signature, OID::SHA256_WITH_RSA_SIGNATURE);

        $cert = str_replace("\r\n", "\n", $csr->__toString());
        $expected = str_replace("\r\n", "\n", $this->cert);
        $this->assertEquals($expected, $cert);
    }

    public function testAddSignatureTwice()
    {
        $private_key = openssl_pkey_get_private($this->pem);
        $pem = openssl_pkey_get_details($private_key)['key'];
        $pemparts = explode("\n", trim($pem));
        array_pop($pemparts);
        array_shift($pemparts);
        $binarykey = base64_decode(implode('', $pemparts));
        $asn1KeyWrapper = ASNObject::fromBinary($binarykey);

        // Now get the key out in hex format
        $asn1Key = $asn1KeyWrapper->getContent();
        $hexkey = $asn1Key[1]->getContent();

        $csr = new CSR('github.org', 'no-reply@github.org', 'Github', 'San Fransisco', '', 'US', 'IT', $hexkey);
        $digest = $csr->getSignatureSubject();
        $signature = null;
        openssl_sign($digest, $signature, $private_key, OPENSSL_ALGO_SHA256);
        $csr->setSignature($signature, OID::SHA256_WITH_RSA_SIGNATURE);
        $csr->setSignature($signature, OID::SHA256_WITH_RSA_SIGNATURE);

        $cert = str_replace("\r\n", "\n", $csr->__toString());
        $expected = str_replace("\r\n", "\n", $this->cert);
        $this->assertEquals($expected, $cert);
    }

    public function testCertificate()
    {
        $private_key = openssl_pkey_get_private($this->pem);
        $pem = openssl_pkey_get_details($private_key)['key'];
        $pemparts = explode("\n", trim($pem));
        array_pop($pemparts);
        array_shift($pemparts);
        $binarykey = base64_decode(implode('', $pemparts));
        $asn1KeyWrapper = ASNObject::fromBinary($binarykey);

        // Now get the key out in hex format
        $asn1Key = $asn1KeyWrapper->getContent();
        $hexkey = $asn1Key[1]->getContent();

        $csr = new CSR('github.org', 'no-reply@github.org', 'Github', 'San Fransisco', '', 'US', 'IT', $hexkey);
        $digest = $csr->getSignatureSubject();
        $signature = null;
        openssl_sign($digest, $signature, $private_key, OPENSSL_ALGO_SHA256);
        $csr->setSignature($signature, OID::SHA256_WITH_RSA_SIGNATURE);

        $cert = $csr->__toString();
        $key = openssl_csr_get_public_key($cert);
        $certPem = openssl_pkey_get_details($key)['key'];
        
        $this->assertEquals($certPem, $pem);
    }

}
