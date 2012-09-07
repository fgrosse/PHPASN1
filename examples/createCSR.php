<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
    <!--
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
     -->
	<head>
		<meta http-equiv="content-Type" content="text/html; charset=ISO-8859-1">
		<title>CSR ENCODER by Friedrich Gro�e</title>
		<link rel="stylesheet" type="text/css" href="./style/common.css">
	</head>
	<body>
	<table border="1" width="100%">
	<tr>
		<td width="50%"><h1>Client</h1></td>
		<!--<td width="50%"><h1>Server</h1></td>-->
	</tr>
	<tr><td width="50%" valign="top">
<?php
	define("DIR_INCLUDE", "includes/");
	define("DIR_CLASSES", "classes/");
	
	require_once(DIR_INCLUDE . "constants.php");
	require_once(DIR_INCLUDE . "functions.php");
	
	require_once(DIR_CLASSES . "ASN_Classes.php");
	require_once(DIR_CLASSES . "CSR.php");
	
	{//Test	Vars
		$asnBool = new ASN_Boolean(false);
		$asnInt = new ASN_Integer(123);
		$asnPrintStr = new ASN_PrintableString("CommonName");
		$asnBitStr = new ASN_BitString("3082010a02820101009e2a7defae93720c91c43c46ff4a1f2e8eef7949289e281f788f3a07d9b94da26fb2e721009caceddd0e6b59daa596df20f871fc30a43f4b80798f94fa3d13cb2db79eb6d8f07b4065d0b09a541564ba3baa1201e20ee923ea16be31fa785c300635c4e881df7acb5b52c7c3d923067902cc55e77c00694f319d2b9e81edbbfe70ef1a462aef4960c567f33aa5264a05fdf24cd7bc36941cd7746fb767631a241b7a97fc4cdc42a68692b906406403599380c7586ce6f22fac34949caf1072c724ba5397e6440f957e2678c3a4bc92268fe6815d41fa210ab45364c11e3731c6c039832b54f54b51fdaf6afb351e1da9720b3c322f7fbaefb72d96d4ce5ec07b0203010001");
		$asnObjIdent = new ASN_ObjectIdentifier("1.2.840.113549.1.9.1");
		$asnNull = new ASN_NULL();
	}
	
	
	{//Build CSR		
		$versionNr			= new ASN_Integer(0);
		$set_commonName		= new CSR_StringObject($OID["COMMON_NAME"], "CommonName");
		$set_email			= new CSR_SimpleObject($OID["EMAIL"], new ASN_IA5String("DNEmail"));
		$set_orgName		= new CSR_StringObject($OID["ORGANIZATION_NAME"], "Organisation");
		$set_localName		= new CSR_StringObject($OID["LOCALITY_NAME"], "Locality City Town");
		$set_state			= new CSR_StringObject($OID["STATE_OR_PROVINCE_NAME"], "State");
		$set_country		= new CSR_StringObject($OID["COUNTRY_NAME"], "DE");
		$set_ou				= new CSR_StringObject($OID["OU_NAME"], "Organisation Units");
		$publicKey 			= new CSR_PublicKey("3082010a02820101009e2a7defae93720c91c43c46ff4a1f2e8eef7949289e281f788f3a07d9b94da26fb2e721009caceddd0e6b59daa596df20f871fc30a43f4b80798f94fa3d13cb2db79eb6d8f07b4065d0b09a541564ba3baa1201e20ee923ea16be31fa785c300635c4e881df7acb5b52c7c3d923067902cc55e77c00694f319d2b9e81edbbfe70ef1a462aef4960c567f33aa5264a05fdf24cd7bc36941cd7746fb767631a241b7a97fc4cdc42a68692b906406403599380c7586ce6f22fac34949caf1072c724ba5397e6440f957e2678c3a4bc92268fe6815d41fa210ab45364c11e3731c6c039832b54f54b51fdaf6afb351e1da9720b3c322f7fbaefb72d96d4ce5ec07b0203010001");
		$signature			= new ASN_BitString("4adf270d347047192573cf245a94cd2e69594c1cdac1d7c99d7ed5856c926ee62c65188f21d893e634b213595cc4564d5a8d39bed0ca01e0b45e3182ab89310c129017f2a7a68d8603694ddc8d1c2ebfee39b3b5dfc9dbc2db667a089b1b51386f2cf7ec70140d185bae5c2f3b3148b9ef613ce068f94db13a230b1133e4b4a48ec5c8b4066d64a2199c0cfb6c4d0cfe105f21a89b2900d0a5c87bef5eded941ba93ae1b7e84aaeabcb46fa4a3844ffc683ebb4ee80717ff51cba5d82afe9d2633b760a66449e57e06d73eeeb151bc050a66825996d7f5ec821d31891c620a677c8271db13bbc22fcf91e1b7ac8f6f109eb8e3a2c61a3c8a4336b40a499e1404");
		$signatureAlgorithm	= new CSR_SignatureKeyAlgorithm(OID_SHA1_WITH_RSA_ENCRYPTION);		
		
		$subjectSequence = new ASN_Sequence(array(
			$set_commonName,
			$set_email,
			$set_orgName,
			$set_localName,
			$set_state,
			$set_country,
			$set_ou
		));

		$mainSequence  = new ASN_Sequence(array($versionNr, $subjectSequence, $publicKey));
		$startSequence = new ASN_Sequence(array($mainSequence, $signatureAlgorithm, $signature));
	
	}
	
	{//Use the CSR-Class to create the csr
		$csr = new CSR(
			0,
			"CommonName",
			"DNEmail",
			"Organisation",
			"Locality City Town",
			"State",
			"DE",
			"Organisation Units",
			"3082010a02820101009e2a7defae93720c91c43c46ff4a1f2e8eef7949289e281f788f3a07d9b94da26fb2e721009caceddd0e6b59daa596df20f871fc30a43f4b80798f94fa3d13cb2db79eb6d8f07b4065d0b09a541564ba3baa1201e20ee923ea16be31fa785c300635c4e881df7acb5b52c7c3d923067902cc55e77c00694f319d2b9e81edbbfe70ef1a462aef4960c567f33aa5264a05fdf24cd7bc36941cd7746fb767631a241b7a97fc4cdc42a68692b906406403599380c7586ce6f22fac34949caf1072c724ba5397e6440f957e2678c3a4bc92268fe6815d41fa210ab45364c11e3731c6c039832b54f54b51fdaf6afb351e1da9720b3c322f7fbaefb72d96d4ce5ec07b0203010001",
			"4adf270d347047192573cf245a94cd2e69594c1cdac1d7c99d7ed5856c926ee62c65188f21d893e634b213595cc4564d5a8d39bed0ca01e0b45e3182ab89310c129017f2a7a68d8603694ddc8d1c2ebfee39b3b5dfc9dbc2db667a089b1b51386f2cf7ec70140d185bae5c2f3b3148b9ef613ce068f94db13a230b1133e4b4a48ec5c8b4066d64a2199c0cfb6c4d0cfe105f21a89b2900d0a5c87bef5eded941ba93ae1b7e84aaeabcb46fa4a3844ffc683ebb4ee80717ff51cba5d82afe9d2633b760a66449e57e06d73eeeb151bc050a66825996d7f5ec821d31891c620a677c8271db13bbc22fcf91e1b7ac8f6f109eb8e3a2c61a3c8a4336b40a499e1404"
		);
	}
	
	
	{//Ausgabe
		echo "<table border='1' style='table-layout:fixed;' width=100%>
				<th width=70px><b>Class</b></th>
				<th><b>Value</b></th>
				<th width=150><b>HexValue</b></th>
				<th width=50><b>Length</b></th>
				<th width=350px><b>Base64</b></th>";
		echo "<tr>
				<td><b>Bool</b></td>
				<td>".$asnBool->getValue()."</td>
				<td>".$asnBool->getHexValue()."</td>
				<td>".$asnBool->getContentLength()."</td>

				<td>".base64_encode($asnBool->getBinary())."</td>
			</tr>";
		echo "<tr>
				<td><b>Integer</b></td>
				<td>".$asnInt->getValue()."</td>
				<td>".$asnInt->getHexValue()."</td>
				<td>".$asnInt->getContentLength()."</td>

				<td>".base64_encode($asnInt->getBinary())."</td>
			<tr>";
		echo "<tr>
				<td><b>PrintStr</b></td>
				<td>".$asnPrintStr->getValue()."</td>
				<td>".$asnPrintStr->getHexValue()."</td>
				<td>".$asnPrintStr->getContentLength()."</td>

				<td>".base64_encode($asnPrintStr->getBinary())."</td>
			<tr>";
		echo "<tr>
				<td><b>BitString</b></td>
				<td>".$asnBitStr->getValue()."</td>
				<td>".$asnBitStr->getHexValue()."</td>
				<td>".$asnBitStr->getContentLength()."</td>

				<td>".base64_encode($asnBitStr->getBinary())."</td>
			<tr>";
		echo "<tr>
				<td><b>ObjIdent</b></td>
				<td>".$asnObjIdent->getValue()."</td>
				<td>".$asnObjIdent->getHexValue()."</td>
				<td>".$asnObjIdent->getContentLength()."</td>

				<td>".base64_encode($asnObjIdent->getBinary())."</td>
			<tr>";
		echo "<tr>
				<td><b>NULL</b></td>
				<td>".$asnNull->getValue()."</td>
				<td>".$asnNull->getHexValue()."</td>
				<td>".$asnNull->getContentLength()."</td>

				<td>".base64_encode($asnNull->getBinary())."</td>
			<tr>";
		echo "<tr>
				<td><b>My CSR</b></font></td>
				<td>".$startSequence->getValue()."</td>
				<td>".$startSequence->getHexValue()."</td>
				<td>".$startSequence->getContentLength()."</td>
				<td>".base64_encode($startSequence->getBinary())."</td>
			<tr>";		
		echo "</table><br><br><hr><br>";	
	}
?>

<center><b>My generated CSR</b><br>
<font size="2">DER-Size(<?echo$csr->getDERByteSize()?> Byte)</font><br>
<textarea id='csr' cols=66 rows=17 >
<?php 
	echo($csr->getBase64DER());
	//echo(base64_encode($signature->getBinary()));
	//echo(base64_encode($startSequence->getBinary()));
?>
</textarea><br>
<input value="Send to Clipboard (IE only)" type="button"
	 onClick="window.clipboardData.setData('text', document.getElementById('csr').innerHTML);">
</center>
<br><br><br><br><hr><br><br>
<iframe width="100%" height="1200px" src="index.html">ERROR: NO IFRAME-SUPPORT!!</iframe><br>
</td>
<!--<td width="50%" height="100%" valign="top">
	<b>User:</b> pkidpwn\administrator<br>
	<iframe width="100%" height="250px" src="https://10.176.100.100:33443/pki/prog/csr.php">ERROR: NO IFRAME-SUPPORT!!</iframe><br>
	<b>Beispiel CSR</b><br>
<textarea id='templ' cols=66 rows=20 readonly>
-----BEGIN CERTIFICATE REQUEST-----
MIIC4TCCAckCAQAwgZsxEzARBgNVBAMTCkNvbW1vbk5hbWUxFjAUBgkqhkiG9w0B
CQEWB0RORW1haWwxFTATBgNVBAoTDE9yZ2FuaXNhdGlvbjEbMBkGA1UEBxMSTG9j
YWxpdHkgQ2l0eSBUb3duMQ4wDAYDVQQIEwVTdGF0ZTELMAkGA1UEBhMCREUxGzAZ
BgNVBAsTEk9yZ2FuaXNhdGlvbiBVbml0czCCASIwDQYJKoZIhvcNAQEBBQADggEP
ADCCAQoCggEBAJ4qfe+uk3IMkcQ8Rv9KHy6O73lJKJ4oH3iPOgfZuU2ib7LnIQCc
rO3dDmtZ2qWW3yD4cfwwpD9LgHmPlPo9E8stt5622PB7QGXQsJpUFWS6O6oSAeIO
6SPqFr4x+nhcMAY1xOiB33rLW1LHw9kjBnkCzFXnfABpTzGdK56B7bv+cO8aRirv
SWDFZ/M6pSZKBf3yTNe8NpQc13Rvt2djGiQbepf8TNxCpoaSuQZAZANZk4DHWGzm
8i+sNJScrxByxyS6U5fmRA+VfiZ4w6S8kiaP5oFdQfohCrRTZMEeNzHGwDmDK1T1
S1H9r2r7NR4dqXILPDIvf7rvty2W1M5ewHsCAwEAAaAAMA0GCSqGSIb3DQEBBQUA
A4IBAQBK3ycNNHBHGSVzzyRalM0uaVlMHNrB18mdftWFbJJu5ixlGI8h2JPmNLIT
WVzEVk1ajTm+0MoB4LReMYKriTEMEpAX8qemjYYDaU3cjRwuv+45s7XfydvC22Z6
CJsbUThvLPfscBQNGFuuXC87MUi572E84Gj5TbE6IwsRM+S0pI7FyLQGbWSiGZwM
+2xNDP4QXyGomykA0KXIe+9e3tlBupOuG36Equq8tG+ko4RP/Gg+u07oBxf/Ucul
2Cr+nSYzt2CmZEnlfgbXPu6xUbwFCmaCWZbX9eyCHTGJHGIKZ3yCcdsTu8Ivz5Hh
t6yPbxCeuOOixho8ikM2tApJnhQE
-----END CERTIFICATE REQUEST-----
</textarea><br>
<input value="Send to Clipboard (IE only)" type="button"
	 onClick="window.clipboardData.setData('text', document.getElementById('templ').innerHTML);">
</td>-->
</tr>
</body>
</html>