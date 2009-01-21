<?PHP

// Logout.php
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
require("Functions/function_errorHandler.php");
require("Functions/function_validUser.php");
require("Classes/class_Configuration.php");
require("Classes/class_ExternalStorage.php");
require("Classes/class_Logger.php");
require("Classes/class_User.php");
require("Classes/class_UserPresentation.php");
require("Classes/yapter.php");

// Definiera felhanterare
// set_error_handler("errorHandler");

// Session
session_start();

// Kolla om vi är inloggade
if (!($bInlogged = validUser()))
{
	// Det är vi inte, fixa variabler och stäng sessionen igen
	session_destroy();
	trigger_error("43", E_USER_ERROR);
	die();	
}

// Logga ut anv?ndare
$_SESSION['oUser']->logOff();
session_destroy();

// Visa sida
$oTemplate = new Template("/home/daft/Templates/Logout.tpl");
$oTemplate->setParseMode(TRUE);
$oTemplate->setWarningLevel(E_YAPTER_ERROR);
$oTemplate->parse();
$oTemplate->spit();

?>
