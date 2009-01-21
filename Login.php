<?PHP

// Login.php
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

ini_set("include_path", "/home/daft/konferens/");

// Inkludera filer
require("Functions/function_errorHandler.php");
require("Classes/class_User.php");
require("Classes/class_Configuration.php");
require("Classes/class_ExternalStorage.php");
require("Classes/class_UserPresentation.php");

// Definiera felhanterare
// set_error_handler("error_handler");

// Kolla så att vi fått ett giltig user och ett giltigt pass
if (!($_POST['UserName'] AND $_POST['Password']))
	trigger_error("36", E_USER_ERROR);
	
// Logga in
$oUser = new User(0, $_POST['UserName'], TRUE);
$oUP = new UserPresentation($oUser);
if ($sErrorMsg = $oUP->getErrorMsg())
	trigger_error("37: ".$sErrorMsg, E_USER_ERROR);
		
if (!($oUP->logOn($_POST['Password'])))
	trigger_error("37: ".$oUP->getErrorMsg(), E_USER_ERROR);
	
// Det gick!

session_start();
// Fixa grejer
$_SESSION['oUser'] = $oUP;
$_SESSION['a_iOldThreads'] = array();
$oConfiguration = Configuration::createInstance();
$_SESSION['sIP'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['iValidUntil'] = time() + $oConfiguration->getCustomValue("ValidTime") * 60;

// Redirekta
$sURL = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $oConfiguration->getCustomValue("GoWhereAfterLogin");
header("Location: ".$sURL);

?>