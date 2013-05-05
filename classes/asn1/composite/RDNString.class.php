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

class RDNString extends RelativeDistinguishedName {
            
    public function __construct($objectIdentifierString, $value) {
        if(ASN_PrintableString::isValid($value)) {
            $value = new ASN_PrintableString($value);
        }
        else {
            if(ASN_IA5String::isValid($value)) {
                $value = new ASN_IA5String($value);
            }
            else {
                $value = new ASN_UTF8String($value);
            }
        }
        
        parent::__construct($objectIdentifierString, $value);
    }
    
}
?>