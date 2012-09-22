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

abstract class OID {

    const RSA_ENCRYPTION            = '1.2.840.113549.1.1.1';
    const MD5_WITH_RSA_ENCRYPTION   = '1.2.840.113549.1.1.4';
    const SHA1_WITH_RSA_SIGNATURE   = '1.2.840.113549.1.1.5';
    const PKCS9_EMAIL               = '1.2.840.113549.1.9.1';
    const PKCS9_UNSTRUCTURED_NAME   = '1.2.840.113549.1.9.2';
    const PKCS9_CONTENT_TYPE        = '1.2.840.113549.1.9.3';
    const PKCS9_MESSAGE_DIGEST      = '1.2.840.113549.1.9.4';
    const PKCS9_SIGNING_TIME        = '1.2.840.113549.1.9.5';
    const COMMON_NAME               = '2.5.4.3';
    const SURNAME                   = '2.5.4.4';
    const SERIAL_NUMBER             = '2.5.4.5';
    const COUNTRY_NAME              = '2.5.4.6';
    const LOCALITY_NAME             = '2.5.4.7';
    const STATE_OR_PROVINCE_NAME    = '2.5.4.8';
    const STREET_ADDRESS            = '2.5.4.9';
    const ORGANIZATION_NAME         = '2.5.4.10';
    const OU_NAME                   = '2.5.4.11';
    const TITLE                     = '2.5.4.12';
    const DESCRIPTION               = '2.5.4.13';
    const POSTAL_ADDRESS            = '2.5.4.16';
    const POSTAL_CODE               = '2.5.4.17';
    const AUTHORITY_REVOCATION_LIST = '2.5.4.38';
    
    /**
     * Returns the name of the given object identifier.
     *
     * Some OIDs are saved as class constants in this class.
     * If the wanted oidString is not among them, this method will
     * query http://oid-info.com for the right name.
     * This behavior can be suppressed by setting the second method parameter to false.
     * 
     * @see self::loadFromWeb($oidString)
     */
    public static function getName($oidString, $loadFromWeb=true) {
        switch ($oidString) {
            case self::RSA_ENCRYPTION:
                return 'RSA Encryption';
            case self::MD5_WITH_RSA_ENCRYPTION:
                return 'MD5 with RSA Encryption';
            case self::SHA1_WITH_RSA_SIGNATURE:
                return 'SHA-1 with RSA Signature';
                
            case self::PKCS9_EMAIL:
                return 'PKCS #9 Email Address';
            case self::PKCS9_UNSTRUCTURED_NAME:
                return 'PKCS #9 Unstructured Name';
            case self::PKCS9_CONTENT_TYPE:
                return 'PKCS #9 Content Type';
            case self::PKCS9_MESSAGE_DIGEST:
                return 'PKCS #9 Message Digest';
            case self::PKCS9_SIGNING_TIME:
                return 'PKCS #9 Signing Time';
                
            case self::COMMON_NAME:
                return 'Common Name';
            case self::SURNAME:
                return 'Surname';
            case self::SERIAL_NUMBER:
                return 'Serial Number';
            case self::COUNTRY_NAME:
                return 'Country Name';
            case self::LOCALITY_NAME:
                return 'Locality Name';
            case self::STATE_OR_PROVINCE_NAME:
                return 'State or Province Name';
            case self::STREET_ADDRESS:
                return 'Street Address';
            case self::ORGANIZATION_NAME:
                return 'Organization Name';
            case self::OU_NAME:
                return 'Organization Unit Name';
            case self::TITLE:
                return 'Title';
            case self::DESCRIPTION:
                return 'Description';
            case self::POSTAL_ADDRESS:
                return 'Postal Address';
            case self::POSTAL_CODE:
                return 'Postal Code';
            case self::AUTHORITY_REVOCATION_LIST:
                return 'Authority Revocation List';
                
            default:
                if($loadFromWeb) {
                    return self::loadFromWeb($oidString);
                }
                else {
                    return $oidString;
                }
        }
    }
    
    public static function loadFromWeb($oidString) {
        $ch = curl_init("http://oid-info.com/get/{$oidString}");
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        $contents = curl_exec($ch);
        curl_close($ch);
        
        // This pattern needs to be updated as soon as the website layout of oid-info.com changes
        preg_match_all('#<tt>(.+)\(\d\)</tt>#si', $contents, $oidName);
        
        if(empty($oidName) || empty($oidName[1])) {
            return "{$oidString} (unkown)";
        }

        $oidName = ucfirst(preg_replace('/([A-Z])/', ' $1', $oidName[1][0]));
        $oidName = str_replace('-', ' ', $oidName);
        
        return $oidName;
    }
}
?>