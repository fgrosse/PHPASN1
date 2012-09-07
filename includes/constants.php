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
 	
	define("ASN1_BOOLEAN",			0x01);
	define("ASN1_INTEGER",			0x02);
	define("ASN1_BITSTRING",		0x03);
	define("ASN1_OCTETSTRING",		0x04);
	define("ASN1_NULL",				0x05);
	define("ASN1_OBJECTIDENTIFIER",	0x06);
	define("ASN1_ObjectDescripter",	0x07);
	define("ASN1_UTF8String",		0x0c);
	define("ASN1_SEQUENCE",			0x30);
	define("ASN1_SET",				0x31);
	define("ASN1_NumericString",	0x12);
	define("ASN1_PrintableString",	0x13);
	define("ASN1_TeletexString",	0x14);
	define("ASN1_IA5String",		0x16);
	define("ASN1_UTCTime",			0x17);
	define("ASN1_GeneralizedTime",	0x18);
	
	$ASNTYPE = array(
		"ASN1_BOOLEAN" => 			0x01,
		"ASN1_INTEGER" => 			0x02,
		"ASN1_BITSTRING" => 		0x03,
		"ASN1_OCTETSTRING" => 		0x04,
		"ASN1_NULL" => 				0x05,
		"ASN1_OBJECTIDENTIFIER" => 	0x06,
		"ASN1_ObjectDescripter" => 	0x07,
		"ASN1_UTF8String" => 		0x0c,
		"ASN1_SEQUENCE" => 			0x30,
		"ASN1_SET" => 				0x31,
		"ASN1_NumericString" => 	0x12,
		"ASN1_PrintableString" => 	0x13,
		"ASN1_TeletexString" => 	0x14,
		"ASN1_IA5String" => 		0x16,
		"ASN1_UTCTime" => 			0x17,
		"ASN1_GeneralizedTime" => 	0x18
	);
	
	//Object-IDs
	define("OID_EMAIL",						"1.2.840.113549.1.9.1");
	define("OID_RSA_ENCRYPTION",			"1.2.840.113549.1.1.1");
	define("OID_SHA1_WITH_RSA_ENCRYPTION",	"1.2.840.113549.1.1.5");
	
	define("OID_COMMON_NAME", 				"2.5.4.3");
	define("OID_COUNTRY_NAME", 				"2.5.4.6");
	define("OID_LOCALITY_NAME", 			"2.5.4.7");
	define("OID_STATE_OR_PROVINCE_NAME", 	"2.5.4.8");
	define("OID_ORGANIZATION_NAME", 		"2.5.4.10");
	define("OID_OU_NAME", 					"2.5.4.11");
	
	$OID = array(
		"EMAIL" => 						"1.2.840.113549.1.9.1",
		"RSA_ENCRYPTION" => 			"1.2.840.113549.1.1.1",
		"SHA1_WITH_RSA_ENCRYPTION" => 	"1.2.840.113549.1.1.5",
		"COMMON_NAME" =>  				"2.5.4.3",
		"COUNTRY_NAME" =>  				"2.5.4.6",
		"LOCALITY_NAME" =>  			"2.5.4.7",
		"STATE_OR_PROVINCE_NAME" =>  	"2.5.4.8",
		"ORGANIZATION_NAME" =>  		"2.5.4.10",
		"OU_NAME" =>  					"2.5.4.11"
	);
?>