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
use FG\ASN1\Object;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

require_once __DIR__.'/../vendor/autoload.php';
require_once 'shared.php';

$input1 = '
MIICWDCCAhgCAQAwVzEYMBYGA1UEAwwPY3J5cHRvZ3JhcGh5LmlvMQ0wCwYDVQQK
DARQeUNBMQswCQYDVQQGEwJVUzEOMAwGA1UECAwFVGV4YXMxDzANBgNVBAcMBkF1
c3RpbjCCAbYwggErBgcqhkjOOAQBMIIBHgKBgQCNf628CeKEqvppFUzqJBdwBJCe
UZ+LNdaFzeW07NyVg+dNNwoPiK2pjwJvJ3Yvs9XaeDb5ht/Ns1ieW5Jb6hFN78A+
+B2uMMJLvG3z1YjpNCe7pkID1KWxaHsrXjtkPUxhSXb4n5WjjT5MiQZfupdRTCLF
Ctu/KJFjp0tUhZs1twIVAINd5WvQfPf4LiAy/niUmu0ReqLvAoGAH3F7Wgd4L8Lk
5o4xH+qRpU7dNrhqxjTRTwWmipfq6dLvMfse895Cw9EA35ymT1vcKux7/ftHTPgx
/qBYU7XgWfLSSYCgrEY/HoGK81I+PLeaOdRfqScxiXdShCRpz4VAsBSRAk6q+85g
GOih9GWMND9Lp8CyHlN2oh9L64SRlh4DgYQAAoGABxPwdkH2Npu1qVRSdKLUwBmY
Nn+zcbueE0NjY2cu1o+CF0wt4FyOg5vG3laN1QuijY2dhxlCOq7FVX3xDXc6si1t
Zcu4eASml7yP2WW5Uvn36FDt8TyKzbXXU7bRDlngtXMuPIK6+hQDQrxKO7oWvQaB
yKai27t+/mziuEY7FwugADAJBgcqhkjOOAQDAy8AMCwCFGHVjcAo0BEIGKfYF9dC
NXJ8Ss/fAhQJe1LhmOzpXeFyc/CpJN8jzp2BiA==';

$input2 = '
MIIFGDCCAwACAQAwOjEWMBQGCgmSJomT8ixkARkWBnNlY3VyZTEgMB4GA1UEAxMX
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
draiRBZruwMPwPIP
';

$input3 = '
MIIC4zCCAcsCAQAwcjELMAkGA1UEBhMCQVUxEzARBgNVBAgMClNvbWUtU3RhdGUx
ITAfBgNVBAoMGEludGVybmV0IFdpZGdpdHMgUHR5IEx0ZDENMAsGA1UEAwwEdGVz
dDEcMBoGCSqGSIb3DQEJARYNdGVzdEB0ZXN0LmNvbTCCASIwDQYJKoZIhvcNAQEB
BQADggEPADCCAQoCggEBAL7t6CR90kOZ0m3qJx9koHBFdXnunEq9ELSUvsZ7oFiw
6zJsyi18S9znVRLEgsW4v2rTsLiJmKnMF2Kyd9AQ2X2zKUK49CTMqG1p/4lF0oLa
nQgNmjYC4VL61wTte6CUAJX7wZA8LL00QfCdgILKodP12E3c7Yp3yw5TAOBdA3Av
KnpsUiNLmQN3HSn9dwk9rCHulrP8LL97l//H2KSCvOvSfk1XeN7ADSyWMC6ZPBD/
7WVnQo7GgMAA0Qhz+nIIFA5AyuFEbRiff+L5EEl+Bg6R9tgx+QUt9JXxF260dkAb
iKBmPcJldl2/GEmz6RAXhMwJnN35tbdzw0LVLzsypr0CAwEAAaAsMBMGCSqGSIb3
DQEJAjEGDAR0ZXN0MBUGCSqGSIb3DQEJBzEIDAYxMjM0NTYwDQYJKoZIhvcNAQEF
BQADggEBACqZ5+3GP7QbTJAqnUloMRBJGYZl0/EyW6dAMoq7E+RiR2OlJGXgP+ga
DPv7Wa4jZiYmf2CZCWKP+WvTvcg4AQq5h7hhAN+u+s8kk0lIjYjnkKRAedGYKDUO
fPD9WowISru3RB1xyxlvgeZS6WoA6TD8REVa1UyFoNzUewvsrNVkKSHh1xk/+ePx
2Ovvrcg9pAWY4K8FvMRdFQKnEud9CAoMxqz3kszhxDW6rcr2mgFPSrKi5WNj+Scg
Tqod8xbB753JWjEbG6Hui9LIMihTX3ZJ0c2GB0buhEgz49y83X/byFHSGGSQpzxX
qXDFVov9UZ+sGy8CJ5ahII79yrfKpxY=
';

try {
    echo 'Input 1:' . PHP_EOL;
    printObject(Object::fromBinary(base64_decode($input1)));
} catch (Exception $exception) {
    echo "ERROR: " . $exception->getMessage();
}

echo PHP_EOL;

try {
    echo 'Input 2:' . PHP_EOL;
    printObject(Object::fromBinary(base64_decode($input2)));
} catch (Exception $exception) {
    echo "ERROR: " . $exception->getMessage();
}

echo PHP_EOL;

try {
    echo 'Input 3:' . PHP_EOL;
    printObject(Object::fromBinary(base64_decode($input3)));
} catch (Exception $exception) {
    echo "ERROR: " . $exception->getMessage();
}
