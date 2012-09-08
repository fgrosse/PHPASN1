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
$asnBitStr = new ASN_BitString("3082010a02820101009e2a7");
$asnObjIdent = new ASN_ObjectIdentifier("1.2.840.113549.1.9.1");
$asnNull = new ASN_NULL();

function printVariableInfo(ASN_Object $variable) {
    echo '<tr>';
    echo '<td><b>'.get_class($variable).'</b></td>';
    echo '<td>'.$variable->getValue().'</td>';
    echo '<td>'.$variable->getHexValue().'</td>';
    echo '<td>'.$variable->getContentLength().'</td>';
    echo '<td>'.base64_encode($variable->getBinary()).'</td>';
    echo '</tr>';
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="Examples of all available PHPASN1 ASN classes">
    <meta name="author" content="Friedrich Große">
    <title>PHPASN1 Classes</title>
</head>
<body>
    <h1>Examples for all available PHPASN1 classes</h1>
    <table>
        <th>Class</th>
        <th>Value</th>
        <th>HexValue</th>
        <th>Length</th>
        <th>Binary (base64)</th>
        
        <?php
            printVariableInfo($asnBool);
            printVariableInfo($asnInt);
            printVariableInfo($asnPrintStr);
            printVariableInfo($asnBitStr);
            printVariableInfo($asnObjIdent);
            printVariableInfo($asnNull);
        ?>
    </table>
</body>
</html>