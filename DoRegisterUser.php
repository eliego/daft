<?PHP

// DoRegisterUser.php
// Skriven av Eli Kaufman för Daft
// Copyright Daft 2003 under GPL

// This file is part of PHPDaft.

// PHPDaft is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.

// PHPDaft is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with PHPDaft; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// Visa var filerna finns
ini_set("include_path", "/home/daft/konferens/");

// Inkludera filer
require("Classes/class_User.php");
require("Classes/class_ExternalStorage.php");
require("Functions/function_errorHandler.php");
require("Classes/class_UserPresentation.php");
require("Classes/class_Configuration.php");
require("Classes/yapter.php");
require("Functions/function_randStr.php");
require("Functions/function_validUser.php");
		
$oConfiguration = Configuration::createInstance();

// Definiera felhanterare
// set_error_handler("errorHandler");

// Kolla så att allt vi fått är OK
if (!($_POST['Age']))
	$_POST['Age'] = 0;
	
// Kolla om vi redirectats fr?n login-sidan
session_start();
if (validUser()) 
	// Jepp det har vi, skicka till n?t kul st?lle
	header("Location: ".$oConfiguration->getCustomValue("GoWhereAfterLogin"));
else 
	// N? det har vi inte
	session_destroy();
if (
	(strlen($_POST['Name']) < $oConfiguration->getCustomValue("NameMin")) or
	(strlen($_POST['Name']) > $oConfiguration->getCustomValue("NameMax")) or
	(strlen($_POST['RealName']) < $oConfiguration->getCustomValue("RealNameMin")) or
	(strlen($_POST['RealName']) > $oConfiguration->getCustomValue("RealNameMax")) or
	(strlen($_POST['Email']) < $oConfiguration->getCustomValue("EmailMin")) or
	(strlen($_POST['Email']) > $oConfiguration->getCustomValue("EmailMax")) or
	(strlen($_POST['Signature']) < $oConfiguration->getCustomValue("SignatureMin")) or
	(strlen($_POST['Signature']) > $oConfiguration->getCustomValue("SignatureMax")) or
	(($_POST['Age']) and (!(is_numeric($_POST['Age'])))) or
	($_POST['Age'] < $oConfiguration->getCustomValue("AgeMin")) or
	($_POST['Age'] > $oConfiguration->getCustomValue("AgeMax")) or
	(strlen($_POST['HomePage']) < $oConfiguration->getCustomValue("HomepageMin")) or
	(strlen($_POST['HomePage']) > $oConfiguration->getCustomValue("HomepageMax")) or
	(strlen($_POST['Other']) < $oConfiguration->getCustomValue("OtherMin")) or
	(strlen($_POST['Other']) > $oConfiguration->getCustomValue("OtherMax")))
		trigger_error("36", E_USER_ERROR);

// Annars, skapa användaren
$oUser = new User(0, $_POST['Name']);
$oUP = new UserPresentation($oUser, TRUE);
if ($sErrorMsg = $oUP->getErrorMsg()) {
	trigger_error("37: ".$sErrorMsg, E_USER_ERROR); die(); }
$oUP->setRealName(htmlspecialchars($_POST['RealName']));
$oUP->setEmail(htmlspecialchars($_POST['Email']));
$oUP->setSignature(htmlspecialchars($_POST['Signature']));
$oUP->setAge($_POST['Age']);
$oUP->setHomesite(htmlspecialchars($_POST['HomePage']));
$oUP->setOther(htmlspecialchars($_POST['Other']));
$oUP->setNumberThreads($oConfiguration->getCustomValue("DefaultNumberThreads"));
$oUP->setNumberPostsInThread($oConfiguration->getCustomValue("DefaultNumberPosts"));
if (!($oUP->createPasswordAndSend()))
	trigger_error("39: ".$oUP->getErrorMsg(), E_USER_ERROR);
if (!($oUP->saveData()))
	trigger_error("39: ".$oUP->getErrorMsg(), E_USER_ERROR);

// Templat
$oTemplate = new Template("/home/daft/Templates/DoRegisterUser.tpl");
$oTemplate->set("Title", $oConfiguration->getCustomValue("Title"));
$oTemplate->parse();
$oTemplate->spit();

?>