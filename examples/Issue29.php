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
use FG\ASN1\Identifier;

require_once __DIR__.'/../vendor/autoload.php';

$hex = "a02b302906092a864886f70d01090e311c301a30180603551d110411300f820d636f72766573706163652e6465";
$asn = Object::fromBinary(hex2bin($hex));

function printObject(Object $object, $depth = 0)
{
    $name = strtoupper(Identifier::getShortName($object->getType()));
    $treeSymbol = '';
    $depthString = str_repeat('━', $depth);
    if ($depth > 0) {
        $treeSymbol = '┣';
        $name = ' ' . $name;
    }

    echo "{$treeSymbol}{$depthString}{$name} : ";
    echo $object->__toString() . PHP_EOL;

    $content = $object->getContent();
    if ($content instanceof Object) {
        printObject($content, $depth+1);
    } else if (is_array($content)) {
        foreach ($object as $child) {
            printObject($child, $depth+1);
        }
    }
}

printObject($asn);
