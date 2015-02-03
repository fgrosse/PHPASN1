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
