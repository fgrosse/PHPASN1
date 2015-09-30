<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FG\ASN1\Object;

require_once __DIR__.'/../vendor/autoload.php';
require_once 'shared.php';

$hex = 'a02b302906092a864886f70d01090e311c301a30180603551d110411300f820d636f72766573706163652e6465';
$asn = Object::fromBinary(hex2bin($hex));

printObject($asn);
