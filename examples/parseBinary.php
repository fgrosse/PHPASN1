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

require_once '../classes/PHPASN_Autoloader.php';
PHPASN_Autoloader::register();

$base64String = 
'MIIDgjCCAmoCCQCUNzVn9BPCZDANBgkqhkiG9w0BAQUFADCBgjELMAkGA1UEBhMC
REUxDzANBgNVBAgTBkJlcmxpbjEPMA0GA1UEBxMGQmVybGluMRYwFAYDVQQKEw1D
b3J2ZXNwYWNlLmRlMRYwFAYDVQQDEw1jb3J2ZXNwYWNlLmRlMSEwHwYJKoZIhvcN
AQkBFhJjb3J2ZTAxMEBnbWFpbC5jb20wHhcNMTIwOTE5MDgyMDIzWhcNMTQwOTE5
MDgyMDIzWjCBgjELMAkGA1UEBhMCREUxDzANBgNVBAgTBkJlcmxpbjEPMA0GA1UE
BxMGQmVybGluMRYwFAYDVQQKEw1Db3J2ZXNwYWNlLmRlMRYwFAYDVQQDEw1jb3J2
ZXNwYWNlLmRlMSEwHwYJKoZIhvcNAQkBFhJjb3J2ZTAxMEBnbWFpbC5jb20wggEi
MA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDjRWaAMUa0RHVSUg6gPuWcHQO1
qNGsQOJeGoy66I7YcITn36faO1eWuwzfLWuRR2tLMy4lcBV32z5KhJ4ubrwf5lFK
Ct5n4DsZWgelXdg6R2LIeLClk1FigJwPPe44a3VXp65KDn6rgvM/2B3itMjNJnZg
2EZ/UAP1MEMLXjMy58cwCobzR+zKYo6HT7YOCYI+2E283NlAxFHdcIuwoeKEnnVn
BdXCBZ3ZIwH3VWWxRJu+eohcqy6DPsfhwxhimw3qrBDUNY8GNlig8DfC+R7qYGEd
QtIAUg7BcICoeibC04BS4iVOfcn0uFIgOiZ+aIAhdYqXUmVV+l2agg06SFDxAgMB
AAEwDQYJKoZIhvcNAQEFBQADggEBAATEaHROhONgNiWBrHJBxKHDN5SuasDUmoLK
MVbNIQvYu3vSavjRnlmYnnasaaxWh0PrFsqymuIrenrDTyTKcRtyXxUBT/aaV2ED
/wWHf9hGt2/Ak8fzV2fMkDeP4zEP7BCoruKhY0WdcoLp1m+irtXrWZfLFbUV0H4a
bsd2sTjSmFODfUQurCMgsFZiRkOVglF+GadcKgmpXjV/dQdMoVrrSyJCXCbfoWM7
hdNRpM3HMDR6o9SD5OwmzOCERC11euLbUU0NBc6sju2flvMlANDhi6+TeZkUnsxv
gJ4RBHv9KofTIg/5QULUyVu7c/PlYn+gr7FDw2qUvRtdxIL5pvs=';

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
    
    echo $object->__toString() . '<br/>';
    
    $content = $object->getContent();
    if(is_array($content)) {        
        foreach ($object as $child) {
            printObject($child, $depth+1);
        }
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