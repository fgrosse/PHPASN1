<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

require_once __DIR__.'/../vendor/autoload.php';

// $data has been generated using https://pkijs.org/examples/OCSP_resp_complex_example.html
$data = 'MIIEXgoBAKCCBFcwggRTBgkrBgEFBQcwAQEEggREMIIEQDBeoSAwHjEcMAkGA1UE
BhMCUlUwDwYDVQQDHggAVABlAHMAdBgPMjAxNTA3MTYxNzU0MjFaMCkwJzASMAcG
BSsOAwIaBAEBBAEBAgEBgAAYDzIwMTUwNzE2MTc1NDIxWjALBgkqhkiG9w0BAQUD
ggEBAG5B1xOnhgzgpsnspWd9c4eLIeOY1XXl7q2DUO2kGji4WbBXtWDMEv7QQO9/
8devmyWNTFlkScbhiMPIEfoES6AyW8lZu9aON2tMssgj/Ev9+H+A2Z2WHQdeDtv7
AS2mYa/7e3Ucsb63pAmBcxgDY55eHwmivBfZIWF6RjbzuJ0Uxz6NvrVEHlWz2KE0
1n/7qbcwbHx8nu1s/IdswFG9iePm5sTsHPsbvsMQ5ZfkvkuL/t5WlLONlRYunOA4
Nytf2lUIZbZznB0dvQG4vm1F9IQEcv1V0aU0D/ZU81I7lSPlPEv/DMTEo1vePTZv
Gv8S1K+keDD/bfwM22d663Vfmt+gggLKMIICxjCCAsIwggGsoAMCAQICAQEwCwYJ
KoZIhvcNAQEFMB4xHDAJBgNVBAYTAlJVMA8GA1UEAx4IAFQAZQBzAHQwHhcNMTMw
MjAxMDAwMDAwWhcNMTYwMjAxMDAwMDAwWjAeMRwwCQYDVQQGEwJSVTAPBgNVBAMe
CABUAGUAcwB0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsFlz/3yl
mk+8uP4/x61UWBliqW08/1jMA83Qj2yuCAvhMhmuq8BvyWP4jnXoxTy3iTZFDXIj
5Vg2FlHWna0TMsU5jlmv8W/mu9U3+q7i1+339JrEZkrDLNDpJ23gqKBgC1OaNH7+
vicBRmoP5kj2X62XMaKT5TMFBZzTtSF7IaUjt9SVXQjG3aHIy7D1mOypvEw4LSS+
RvvJTezSQeCoLpX7HziRnoVUNkJWZHL2wG5rb/SJzpXwHkRa7R250vN8TFtSSsp4
YXzK6aks/qkxRJ1UkBLUAZlRasUD+zu8gyTIz0sNgFrOtx8EOzjZRiJ/vBiVOn88
Brf4i0D+fVXmJQIDAQABow8wDTALBgNVHQ8EBAMCAAIwCwYJKoZIhvcNAQEFA4IB
AQBfubKPTvmDGrDxoqCbPwFoPRC0STwPL2GV8f5sD/Sbyc0NoJdygUO2DvquGGn5
6UJCfLo1u6Dn4zuuDs3m6if86HTpAf9Z3a72ok2Tor/NFwYt+vDOrFY5F4bXDZkf
u4zuDLmjpj26Dk4te3BVohsLTXbvJ5a/TT2VanwNOyx85lXPxy3V8Rr1AwlmHZoz
DDbUGbe/noUDJCgMjvaKKvLykIhIcW+g6W7SOcKRflw5H8kzDv816XFODSC3X1Uw
o3aVy9du/0mH+g4HvyVVplO90tdoHD1gHUMZwuen4dbTzhWv4dtLFelWM5lGWbLE
Wn7kJghclgIxv10nkGyfrowt';

// OCSP response status according to
// https://tools.ietf.org/html/rfc6960#section-4.2.1
$validResponseStatuses = array(
    0 => 'Response has valid confirmations',
    1 => 'Illegal confirmation request',
    2 => 'Internal error in issuer',
    3 => 'Try again later',
    // (4) is not used
    5 => 'Must sign the request',
    6 => 'Request unauthorized',
);

$ocspResponse = Sequence::fromBinary(base64_decode($data));

/* @var Enumerated $responseStatus */
$elements = $ocspResponse->getChildren();
$responseStatus = $elements[0];
$responseStatusCode = $responseStatus->getContent();

echo PHP_EOL;
echo "OCSP response status: $responseStatusCode ({$validResponseStatuses[$responseStatusCode]})".PHP_EOL;

/** @var ExplicitlyTaggedObject $responseBytes */
$responseBytes = $elements[1];

/** @var Sequence $responseBytesSequence */
$responseBytesSequence = $responseBytes->getContent();

/** @var ObjectIdentifier $responseType */
$responseType = $responseBytesSequence->getChildren()[0];
echo "ResponseType: {$responseType}".PHP_EOL;

$response = $responseBytesSequence->getChildren()[1];
echo "Response (octet string): {$response}".PHP_EOL;
