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

use FG\ASN1\OID;

function echoOIDRow($oidString)
{
    $oidName = OID::getName($oidString);
    echo "<tr><td>{$oidString}</td><td>{$oidName}</td></tr>";
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PHPASN1 Examples</title>
  <meta name="description" content="Howto get the name of object identifiers with PHPASN1">
  <meta name="author" content="Friedrich Große">
  <style type="text/css">td {padding: 0 10px;}</style>
</head>
<body>
    <table border=1>
        <?php
            echoOIDRow('1.2.840.113549.1.1.1');
            echoOIDRow('1.2.840.113549.1.1.5');
            echoOIDRow('2.5.29.37');
        ?>
    </table>
</body>
</html>
