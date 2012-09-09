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

require_once '../classes/PHPASN_Autoloader.php';
PHPASN_Autoloader::register();
 
$asnBool = new ASN_Boolean(false);
$asnInt = new ASN_Integer(123456);
$asnPrintStr = new ASN_PrintableString("Hello World");
$asnBitStr = new ASN_BitString("3082010a02820101009e2a7", 0);
$asnObjIdent = new ASN_ObjectIdentifier(OID::EMAIL);
$asnNull = new ASN_NULL();
$asnSequence = new ASN_Sequence($asnBool, $asnInt, $asnPrintStr);
$asnSet = new ASN_Set($asnObjIdent, $asnSequence);

$csr = new CSR(
    0,
    "Friedrich Grosse",
    "friedrich.grosse@gmail.com",
    "PHPASN1",
    "Berlin",
    "Berlin",
    "DE",
    "Development Department",
    "3082010a02820101009e2a7defae93720c91c43c46ff4a1f2e8eef7949289e281f788f3a07d9b94da26fb2e721009caceddd0e6b59daa596df20f871fc30a43f4b80798f94fa3d13cb2db79eb6d8f07b4065d0b09a541564ba3baa1201e20ee923ea16be31fa785c300635c4e881df7acb5b52c7c3d923067902cc55e77c00694f319d2b9e81edbbfe70ef1a462aef4960c567f33aa5264a05fdf24cd7bc36941cd7746fb767631a241b7a97fc4cdc42a68692b906406403599380c7586ce6f22fac34949caf1072c724ba5397e6440f957e2678c3a4bc92268fe6815d41fa210ab45364c11e3731c6c039832b54f54b51fdaf6afb351e1da9720b3c322f7fbaefb72d96d4ce5ec07b0203010001",
    "4adf270d347047192573cf245a94cd2e69594c1cdac1d7c99d7ed5856c926ee62c65188f21d893e634b213595cc4564d5a8d39bed0ca01e0b45e3182ab89310c129017f2a7a68d8603694ddc8d1c2ebfee39b3b5dfc9dbc2db667a089b1b51386f2cf7ec70140d185bae5c2f3b3148b9ef613ce068f94db13a230b1133e4b4a48ec5c8b4066d64a2199c0cfb6c4d0cfe105f21a89b2900d0a5c87bef5eded941ba93ae1b7e84aaeabcb46fa4a3844ffc683ebb4ee80717ff51cba5d82afe9d2633b760a66449e57e06d73eeeb151bc050a66825996d7f5ec821d31891c620a677c8271db13bbc22fcf91e1b7ac8f6f109eb8e3a2c61a3c8a4336b40a499e1404"
);
    

// check if openssl is installed on this system
$openSSLVersionOutput = shell_exec('openssl version');
if(substr($openSSLVersionOutput, 0, 7) == 'OpenSSL') {
    $openSSLisAvailable = true;    
}
else {
    $openSSLisAvailable = false;
}
    
function printVariableInfo(ASN_Object $variable) {
    $className = get_class($variable);
    $stringValue = nl2br($variable->__toString());
    $base64Binary = base64_encode($variable->getBinary());
    
    echo '<tr>';
    echo "<td class='ASNclass'>{$className}</td>";
    echo "<td class='monospace'>\"<span class='red'>{$stringValue}</span>\"</td>";           
    echo "<td class='monospace base64'>{$base64Binary}</td>";
    
    global $openSSLisAvailable;
    if($openSSLisAvailable) {
        $openSSLOutput = shell_exec("echo '{$base64Binary}' | openssl asn1parse -inform PEM -dump -i");
        echo "<td><pre>{$openSSLOutput}</pre></td>";   
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
    if($openSSLisAvailable == false)
        echo "<p class='notice'>Note: OpenSSL could not be found on this system. This is absolutely no problem
        for PHPASN1 but it could have been used to show you that the generated binaries are indeed correct :)</p>";
    ?>
    
    <table>
        <th>Class</th>
        <th>toString()</th>
        <th>Binary (base64)</th>
        
        <?php        
            if($openSSLisAvailable) {
                echo '<th>OpenSSL asn1parse output</th>';
            }
            
            printVariableInfo($asnBool);
            printVariableInfo($asnInt);
            printVariableInfo($asnPrintStr);
            printVariableInfo($asnBitStr);
            printVariableInfo($asnObjIdent);
            printVariableInfo($asnNull);
            printVariableInfo($asnSequence);
            printVariableInfo($asnSet);
            printVariableInfo($csr);
        ?>
    </table>       
</body>
</html>