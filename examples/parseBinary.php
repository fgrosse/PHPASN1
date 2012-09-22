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
'MIIC7DCCAdQCAQAwgagxCzAJBgNVBAYTAkRFMQ8wDQYDVQQI
 EwZCZXJsaW4xDzANBgNVBAcTBkJlcmxpbjEQMA4GA1UEChMH
 UEhQQVNOMTEfMB0GA1UECxMWRGV2ZWxvcG1lbnQgRGVwYXJ0
 bWVudDEZMBcGA1UEAxMQRnJpZWRyaWNoIEdyb3NzZTEpMCcG
 CSqGSIb3DQEJARYaZnJpZWRyaWNoLmdyb3NzZUBnbWFpbC5j
 b20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCe
 Kn3vrpNyDJHEPEb/Sh8uju95SSieKB94jzoH2blNom+y5yEA
 nKzt3Q5rWdqllt8g+HH8MKQ/S4B5j5T6PRPLLbeettjwe0Bl
 0LCaVBVkujuqEgHiDukj6ha+Mfp4XDAGNcTogd96y1tSx8PZ
 IwZ5AsxV53wAaU8xnSuege27/nDvGkYq70lgxWfzOqUmSgX9
 8kzXvDaUHNd0b7dnYxokG3qX/EzcQqaGkrkGQGQDWZOAx1hs
 5vIvrDSUnK8QcsckulOX5kQPlX4meMOkvJImj+aBXUH6IQq0
 U2TBHjcxxsA5gytU9UtR/a9q+zUeHalyCzwyL3+677ctltTO
 XsB7AgMBAAEwDQYJKoZIhvcNAQEFBQADggEBAErfJw00cEcZ
 JXPPJFqUzS5pWUwc2sHXyZ1+1YVskm7mLGUYjyHYk+Y0shNZ
 XMRWTVqNOb7QygHgtF4xgquJMQwSkBfyp6aNhgNpTdyNHC6/
 7jmztd/J28LbZnoImxtROG8s9+xwFA0YW65cLzsxSLnvYTzg
 aPlNsTojCxEz5LSkjsXItAZtZKIZnAz7bE0M/hBfIaibKQDQ
 pch7717e2UG6k64bfoSq6ry0b6SjhE/8aD67TugHF/9Ry6XY
 Kv6dJjO3YKZkSeV+Btc+7rFRvAUKZoJZltf17IIdMYkcYgpn
 fIJx2xO7wi/PkeG3rI9vEJ64 46LGGjyKQza0CkmeFAQ=';

$binaryData = base64_decode($base64String);
$asnObject = ASN_Object::fromBinary($binaryData);

function printObject(ASN_Object $object, $depth=0) {
    $treeSymbol = '';
    $depthString = str_repeat('━', $depth);
    if($depth > 0) {
        $treeSymbol = '┣';
    }
    
    $name = strtoupper(Identifier::getShortName($object->getType()));
    echo "{$treeSymbol}{$depthString}<b>{$name}</b> : ";
    
    $content = $object->getContent();
    if(is_array($content)) {
        echo $object->__toString() . '<br/>';
        foreach ($object as $child) {
            printObject($child, $depth+1);
        }
    }
    else {
        echo $content . '<br/>';
    }
}

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