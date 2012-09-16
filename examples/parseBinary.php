<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 * 
 * Copyright © Friedrich Große, Berlin 2012
 * 
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PHPASN1;

require_once '../external/hexdump.php';
require_once '../classes/PHPASN_Autoloader.php';
PHPASN_Autoloader::register();

$base64String = 
'MIIDfjCCAuegAwIBAgIKZGgbPQAAAABnVTANBgkqhkiG9w0BAQUFADBGMQswCQYD
VQQGEwJVUzETMBEGA1UEChMKR29vZ2xlIEluYzEiMCAGA1UEAxMZR29vZ2xlIElu
dGVybmV0IEF1dGhvcml0eTAeFw0xMjA4MTYxMjI2MTlaFw0xMzA2MDcxOTQzMjda
MGcxCzAJBgNVBAYTAlVTMRMwEQYDVQQIEwpDYWxpZm9ybmlhMRYwFAYDVQQHEw1N
b3VudGFpbiBWaWV3MRMwEQYDVQQKEwpHb29nbGUgSW5jMRYwFAYDVQQDEw13d3cu
Z29vZ2xlLmRlMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEoGt7UvjRfcHM
tHNixkq3marhniRadVnpzux9iqTfB8sLIf39djxjoxOmaP6ddk7ZE8UaZ2eI22Kv
Yk9CLC8RLBMWkiql03gjzZ9D0fxUUT0Usp42mR8IoELELqru5f6OLLEBZxdKNZzr
9vrMLJypM61AMTfuLD9MvtlGASnHKwIDAQABo4IBUDCCAUwwHQYDVR0lBBYwFAYI
KwYBBQUHAwEGCCsGAQUFBwMCMB0GA1UdDgQWBBS7E76yPlQGezzNghLX64dAkcH1
HTAfBgNVHSMEGDAWgBS/wDDr9UMRPme6npH7/Gra42sSJDBbBgNVHR8EVDBSMFCg
TqBMhkpodHRwOi8vd3d3LmdzdGF0aWMuY29tL0dvb2dsZUludGVybmV0QXV0aG9y
aXR5L0dvb2dsZUludGVybmV0QXV0aG9yaXR5LmNybDBmBggrBgEFBQcBAQRaMFgw
VgYIKwYBBQUHMAKGSmh0dHA6Ly93d3cuZ3N0YXRpYy5jb20vR29vZ2xlSW50ZXJu
ZXRBdXRob3JpdHkvR29vZ2xlSW50ZXJuZXRBdXRob3JpdHkuY3J0MAwGA1UdEwEB
/wQCMAAwGAYDVR0RBBEwD4INd3d3Lmdvb2dsZS5kZTANBgkqhkiG9w0BAQUFAAOB
gQBOBSVeQj4EiCzcgVUmO9BIzQqOHgOQq2ePbgrUln9aX+0SCLvJf/38HrT884Jf
CznPwmTKJmjLRIq6v6RPvYC9O45EM7kjB1YXcCCKiPK7IGmJf8dwZAO4MKLtJnv4
D0k6lc6/SWpmbg33TqEDjl8OsvMUzV6S8XRz2L/rqZ1z1g==';

$binaryData = base64_decode($base64String);
$asnObjects = ASN_Object::fromBinary($binaryData);

?>
