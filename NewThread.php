<?PHP

// NewThread.php
// Skriven av Eli Kaufman f?r Daft
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
require("Functions/function_validUser.php");
require("Classes/class_Configuration.php");
require("Classes/class_User.php");
require("Classes/class_UserPresentation.php");
require("Classes/yapter.php");
$oConfiguration =& Configuration::createInstance();

// Kolla om inloggad
session_start();
if (!validUser())
	trigger_error("43", E_USER_ERROR);
	
// Template
$oTemplate = new Template("/home/daft/Templates/NewThread.tpl");
$oTemplate->setParseMode(TRUE);
$oTemplate->setWarningLevel(E_YAPTER_ERROR);
$oTemplate->set("Title", $oConfiguration->getCustomValue("Title"));
$oTemplate->set("User_ID", $_SESSION['oUser']->getID());	$oTemplate->set("User_Name", htmlspecialchars($_SESSION['oUser']->getName()));
$oTemplate->replace("Login_Outside", "Login_Inside");
$oTemplate->set("Signature", "\n\n\n".$_SESSION['oUser']->getSignature());

// Tryck ut
$oTemplate->parse();
$oTemplate->spit();

?>