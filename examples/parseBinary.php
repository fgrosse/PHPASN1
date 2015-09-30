<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';
require_once 'shared.php';

use FG\ASN1\Object;

$base64String =
'MIIFGDCCAwACAQAwOjEWMBQGCgmSJomT8ixkARkWBnNlY3VyZTEgMB4GA1UEAxMX
LlNlY3VyZSBFbnRlcnByaXNlIENBIDEwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAw
ggIKAoICAQCzgEpL+Za7a3y7YpURDrxlGIBlks25fD0tHaZIYkBTaXA5h+9MWoXn
FA7AlIUt8pbBvXdJbOCmGaeQmBfBH0Qy9vTbx/DR2IOwzqy2ZHuurI5bPL12ceE2
Mxa9xgY/i7U6MAUtoA3amEd7cKj2fz9EWZruRladOX0DXv9KexSan+45QjCWH+u2
Cxem2zH9ZDNPGBuAF9YsAvkdHdAoX8aSm05ZAjUiO2e/+L57whh7zZiDY3WIhin7
N/2JNTKVO6lx50S8a34XUKBt3SKgSR941hcLrBYUNftUYsTPo40bzKKcWqemiH+w
jQiDrln4V2b5EbVeoGWe4UDPXCVmC6UPklG7iYfF0eeK4ujV8uc9PtV2LvGLOFdm
AYE3+FAba5byQATw/DY8EJKQ7ptPigJhVe47NNeJlsKwk1haJ9k8ZazjS+vT45B5
pqe0yBFAEon8TFnOLnAOblmKO12i0zqMUNAAlmr1c8jNjLr+dhruS+QropZmzZ24
mAnFG+Y0qpfhMzAxTGQyVjyGwDfRK/ARmtrGpmROjj5+6VuMmZ6Ljf3xN09epmtH
gJe+lYNBlpfUYg16tm+OusnziYnXL6nIo2ChOY/7GNJJif9fjvvaPDCC98K64av5
5rpIx7N/XH4hwHeQQkEQangExE+8UMyBNFNmvPnIHVHUZdYo4SLsYwIDAQABoIGY
MBsGCisGAQQBgjcNAgMxDRYLNi4zLjk2MDAuMi4weQYJKoZIhvcNAQkOMWwwajAQ
BgkrBgEEAYI3FQEEAwIBADAdBgNVHQ4EFgQU5nEIMEUT5mMd1WepmviwgK7dIzww
GQYJKwYBBAGCNxQCBAweCgBTAHUAYgBDAEEwCwYDVR0PBAQDAgGGMA8GA1UdEwEB
/wQFMAMBAf8wDQYJKoZIhvcNAQELBQADggIBAKZl6bAeaID3b/ic4aztL8ZZI7vi
D3A9otUKx6v1Xe63zDPR+DiWSnxb9m+l8OPtnWkcLkzEIM/IMWorHKUAJ/J871D0
Qx+0/HbkcrjMtVu/dNrtb9Z9CXup66ZvxTPcpEziq0/n2yw8QdBaa+lli65Qcwcy
tzMQK6WQTRYfvVCIX9AKcPKxwx1DLH+7hL/bERB1lUDu59Jx6fQfqJrFVOY2N8c0
MGvurfoHGmEoyCMIyvmIMu4+/wSNEE/sSDp4lZ6zuF6rf1m0GiLdTX2XJE+gfvep
JTFmp4S3WFqkszKvaxBIT+jV0XKTNDwnO+dpExwU4jZUh18CdEFkIUuQb0gFF8B7
WJFVpNdsRqZRPBz83BW1Kjo0yAmaoTrGNmG0p6Qf3K2zbk1+Jik3VZq4rvKoTi20
6RvLA2//cMNfkYPsuqvoHGe2e0GOLtIB63wJzloWROpb72ohEHsvCKullIJVSuiS
9sfTBAenHCyndgAEd4T3npTUdaiNumVEm5ilZId7LAYekJhkgFu3vlcl8blBJKjE
skVTp7JpBmdXCL/G/6H2SFjca4JMOAy3DxwlGdgneIaXazHs5nBK/BgKPIyPzZ4w
secxBTTCNgI48YezK3GDkn65cmlnkt6F6Mf0MwoDaXTuB88Jycbwb5ihKnHEJIsO
draiRBZruwMPwPIP';

$binaryData = base64_decode($base64String);
$asnObject = Object::fromBinary($binaryData);

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PHPASN1 Examples</title>
  <meta name="description" content="How to parse binary ASN.1 data with PHPASN1">
  <meta name="author" content="Friedrich Große">
</head>
<body>
    <p>Got <?= strlen($binaryData) ?> byte of binary data: </p>
    <pre><?php printObject($asnObject);?></pre>
</body>
</html>
