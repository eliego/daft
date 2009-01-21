<?PHP

// Post.php
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
require("Classes/class_MiniThread.php");
require("Classes/class_Logger.php");
require("Classes/class_User.php");
require("Classes/class_UserPresentation.php");
require("Classes/class_Post.php");

// Definiera felhanterare
// set_error_handler("errorHandler");

// Objekt
$oConfiguration =& Configuration::createInstance();

// Kolla så vi fått rätt grejer
if (!(strlen($_POST['body']) > 0))
	trigger_error("36", E_USER_ERROR);
	
if (!(is_numeric($_GET['ID']) AND ($_GET['ID'] > 0)))
	trigger_error("36", E_USER_ERROR);

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

// Skapa ny post
$oPost = new Post($_SESSION['oUser']->getUser(), wordwrap(nl2br(htmlspecialchars($_POST['body'])), $oConfiguration->getCustomValue("LineBreak"), "<br>"), $_GET['ID']);
if (!($oPost->saveData()))
	trigger_error("44: ".$oPost->getErrorMsg(), E_USER_ERROR);
	
// Gör inte den nya posten ploppad
$_SESSION['a_iOldThreads'][$_GET['ID']]++;
	
// Redirekta
header("Location: ".$oConfiguration->getCustomValue("GoWhereAfterNewPost"));

?>