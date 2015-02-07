<?php


use FG\ASN1\Object;
use FG\ASN1\Identifier;
use FG\ASN1\ContextSpecificObject;
require_once __DIR__.'/../vendor/autoload.php';

$hex = "a02b302906092a864886f70d01090e311c301a30180603551d110411300f820d636f72766573706163652e6465";
$asn = Object::fromBinary(hex2bin($hex));

function printObject(Object $object, $depth = 0)
{
    $treeSymbol = '';
    $depthString = str_repeat('━', $depth);
    if ($depth > 0) {
        $treeSymbol = '┣';
    }

    $name = strtoupper(Identifier::getShortName($object->getType()));

    if ($object instanceof ContextSpecificObject) {
        $name .= ' (parsed CSO)';
    }

    echo "{$treeSymbol}{$depthString} {$name} : ";

    echo $object->__toString() . PHP_EOL;

    $content = $object->getContent();
    if (is_array($content)) {
        foreach ($object as $child) {
            printObject($child, $depth+1);
        }
    }
}

printObject($asn);