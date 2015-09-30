<?php

use FG\ASN1\Identifier;
use FG\ASN1\Object;

function printObject(Object $object, $depth = 0)
{
    $treeSymbol = '';
    $depthString = str_repeat('─', $depth);
    if ($depth > 0) {
        $treeSymbol = '├';
    }

    $name = Identifier::getShortName($object->getType());
    echo "{$treeSymbol}{$depthString}{$name} : ";

    echo $object->__toString().PHP_EOL;

    $content = $object->getContent();
    if (is_array($content)) {
        foreach ($object as $child) {
            printObject($child, $depth + 1);
        }
    }
}
