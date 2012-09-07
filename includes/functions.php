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

function isValidType($type) {
	switch($type) {
		case hexdec(ASN_BOOLEAN): 	$result = true; break;
		case hexdec(ASN_INTEGER): 	$result = true; break;
		case ASN_BITSTRING: 		$result = true; break;
		case ASN_OCTETSTRING: 		$result = true; break;
		case ASN_NULL: 				$result = true; break;
		case ASN_OBJECTIDENTIFIER: 	$result = true; break;
		case ASN_ObjectDescripter: 	$result = true; break;
		case ASN_UTF8String: 		$result = true; break;
		case ASN_SEQUENCE: 			$result = true; break;
		case ASN_SET: 				$result = true; break;
		case ASN_NumericString: 	$result = true; break;
		case ASN_PrintableString: 	$result = true; break;
		case ASN_TeletexString: 	$result = true; break;
		case ASN_IA5String:			$result = true; break;
		case ASN_UTCTime: 			$result = true; break;
		default: 					$result = false;break;
	}
	return $result;
}
?>