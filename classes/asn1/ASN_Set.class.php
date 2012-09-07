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

class ASN_Set extends ASN_Sequence {
	
	function __construct($param = null) {
		$this->type = ASN1_SET;
		$this->value = array();
		
		if( isset($param) ) {
			if( is_array($param) ) {
				for($i=0 ; $i < count($paramArr) ; $i++)
					$this->addChild($paramArr[$i]);
			}
			else if( $param instanceof ASN_Object ) {
				$this->addChild($param);
			}
			else throw new Exception("[$param] is no ASN_OBJECT!");
		}
	}
}
?>