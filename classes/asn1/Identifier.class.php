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

abstract class Identifier {
       
    const EOC               = 0x00; // unsupported for now
    const BOOLEAN           = 0x01;
    const INTEGER           = 0x02;
    const BITSTRING         = 0x03;
    const OCTETSTRING       = 0x04; // unsupported for now
    const NULL              = 0x05;
    const OBJECT_IDENTIFIER = 0x06;
    const OBJECT_DESCRIPTOR = 0x07; // unsupported for now
    const OBJECT_EXTERNAL   = 0x08; // unsupported for now 
    const FLOAT             = 0x09; // unsupported for now
    const ENUMERATED        = 0x0A;
    const EMBEDDED_PDV      = 0x0B; // unsupported for now
    const UTF8_STRING       = 0x0C; // unsupported for now
    const RELATIVE_OID      = 0x0D; // unsupported for now
    // value 0x0E and 0x0F are reserved for future use
    
    const SEQUENCE          = 0x30;
    const SET               = 0x31;
    const NUMERIC_STRING    = 0x12; // unsupported for now
    const PRINTABLE_STRING  = 0x13;
    const T61_STRING        = 0x14; // unsupported for now
    const VIDEOTEXT_STRING  = 0x15; // unsupported for now
    const IA5_STRING        = 0x16;
    const UTC_TIME          = 0x17; // unsupported for now
    const GENERALIZED_TIME  = 0x18; // unsupported for now
    const GRAPHIC_STRING    = 0x19; // unsupported for now
    const VISIBLE_STRING    = 0x1A; // unsupported for now
    const GENERAL_STRING    = 0x1B; // unsupported for now
    const UNIVERSAL_STRING  = 0x1C; // unsupported for now
    const CHARACTER_STRING  = 0x1D; // unsupported for now
    const BMP_STRING        = 0x1E; // unsupported for now
    
    const LONG_FORM         = 0x1F; // unsupported for now
    const IS_CONSTRUCTED    = 0x20;
    
    public static function getName($identifierOctet) {
        if(!is_numeric($identifierOctet)) {
            $identifierOctet = ord($identifierOctet);
        }
                
        switch ($identifierOctet) {
            case self::EOC:
                return 'ASN.1 End-of-contents octet';
            case self::BOOLEAN:
                return 'ASN.1 Boolean';
            case self::INTEGER:
                return 'ASN.1 Integer';
            case self::BITSTRING:
                return 'ASN.1 Bit String';
            case self::OCTETSTRING:
                return 'ASN.1 Octet String';
            case self::NULL:
                return 'ASN.1 NULL';
            case self::OBJECT_IDENTIFIER:
                return 'ASN.1 Object Identifier';
            case self::OBJECT_DESCRIPTOR:
                return 'ASN.1 Object Descriptor';
            case self::OBJECT_EXTERNAL:
                return 'ASN.1 Object External';
            case self::FLOAT:
                return 'ASN.1 Float';
            case self::ENUMERATED:
                return 'ASN.1 Enumerated';
            case self::EMBEDDED_PDV:
                return 'ASN.1 Embedded PDV';
            case self::UTF8_STRING:
                return 'ASN.1 UTF8 String';
            case self::RELATIVE_OID:
                return 'ASN.1 Relative OID';
            case self::SEQUENCE:
                return 'ASN.1 Sequence';
            case self::SET:
                return 'ASN.1 Set';
            case self::NUMERIC_STRING:
                return 'ASN.1 Numeric String';
            case self::PRINTABLE_STRING:
                return 'ASN.1 Printable String';
            case self::T61_STRING:
                return 'ASN.1 T61 String';
            case self::VIDEOTEXT_STRING:
                return 'ASN.1 Videotext String';
            case self::IA5_STRING:
                return 'ASN.1 IA5 String';
            case self::UTC_TIME:
                return 'ASN.1 UTC Time';
            case self::GENERALIZED_TIME:
                return 'ASN.1 Generalized Time';
            case self::GRAPHIC_STRING:
                return 'ASN.1 Graphic String';
            case self::VISIBLE_STRING:
                return 'ASN.1 Visible String';
            case self::GENERAL_STRING:
                return 'ASN.1 General String';
            case self::UNIVERSAL_STRING:
                return 'ASN.1 Universal String';
            case self::CHARACTER_STRING:
                return 'ASN.1 Character String';
            case self::BMP_STRING:
                return 'ASN.1 BMP String';
            
            case 0x0E:
                return 'ASN.1 RESERVED (0x0E)';
            case 0x0F:
                return 'ASN.1 RESERVED (0x0F)';
            
            case self::LONG_FORM:
                throw new NotImplementedException('Long form of identifier octets is not yet implemented');
            
            default:
                return 'UNKNOWN Type (0x'.dechex($identifierOctet).')';            
        }
    } 
    
}