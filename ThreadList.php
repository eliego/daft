<?PHP

// ThreadList.php
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
require("Functions/function_timer.php");
timer(); // Starta timer
require("Functions/function_errorHandler.php");
require("Functions/function_validUser.php");
require("Functions/function_fixDateFormat.php");
require("Classes/class_Configuration.php");
require("Classes/class_ExternalStorage.php");
require("Classes/class_MiniThread.php");
require("Classes/class_Thread.php");
require("Classes/class_ListThread.php");
require("Classes/class_Logger.php");
require("Classes/class_ThreadList.php");
require("Classes/class_User.php");
require("Classes/class_UserPresentation.php");
require("Classes/yapter.php");

// Definiera felhanterare
// set_error_handler("errorHandler");

// Objekt
$oConfiguration =& Configuration::createInstance();
$oTemplate = new Template("/home/daft/Templates/ThreadList.tpl");
$oTemplate->setParseMode(TRUE);
$oTemplate->setWarningLevel(E_YAPTER_ERROR);

// Kolla s? vi f?tt r?tt grejer
if (!((is_numeric($_GET['Page'])) AND ($_GET['Page'] > 0)))
	$_GET['Page'] = 1;
	
if (!(in_array($_GET['Sort'], array("LastTimestamp", "Posts", "Reads"))))
	$_GET['Sort'] = "LastTimestamp";

// Session
session_start();

// Kolla om vi ?r inloggade
if (!($bInlogged = validUser()))
{
	// Det ?r vi inte, fixa variabler och st?ng sessionen igen
	$iNumberThreads = $oConfiguration->getCustomValue("DefaultNumberThreads");
	$iThreadTimestamp = 0;
	$a_iOldThreads = array();
	session_destroy();
} else {
	// Det ?r vi, fixa variabler
	$iNumberThreads = $_SESSION['oUser']->getNumberThreads();
	$iThreadTimestamp = $_SESSION['oUser']->getLastLogout();
	$a_iOldThreads = $_SESSION['a_iOldThreads'];
}

// H?mta tr?dlistan
$oTL = new ThreadList();
if (FALSE===($a_oThreads = $oTL->getThreads($_GET['Sort'], $iNumberThreads, $iNumberThreads * ($_GET['Page'] - 1), $iThreadTimestamp, $a_iOldThreads)))
	trigger_error("11: ".$oTL->getErrorMsg(), E_USER_WARNING);

// Bygg upp sidan
$oTemplate->set("title", $oConfiguration->getCustomValue("Title"));
if ($bInlogged) {
	$oTemplate->set("User_ID", $_SESSION['oUser']->getID());
	$oTemplate->set("User_Name", htmlspecialchars($_SESSION['oUser']->getName()));
	$oTemplate->replace("Login_Outside", "Login_Inside"); }
$oTemplate->set("Page", $_GET['Page']);
$oTemplate->set("Page_Next", $_GET['Page'] + 1);
$oTemplate->set("Page_Prev", $_GET['Page'] - 1);
$oTemplate->set("Sort", $_GET['Sort']);
$iCounter = 0;
foreach (array_keys($a_oThreads) AS $iThread)
{
	$oThread =& $a_oThreads[$iThread];
	$oUser = $oThread->getUser();
	$oLastUser = $oThread->getLastPostUser();
	$oTemplate->set("Thread_ID", $oThread->getThreadID());
	$oTemplate->set("Thread_Rubrik", $oThread->getRubrik());
	$oTemplate->set("Thread_Answers", $oThread->getNumberPosts() - 1);
	$oTemplate->set("Thread_Reads", $oThread->getNumberReads());
	$oTemplate->set("Thread_Last_Time", fixDateFormat($oThread->getLastPostTimestamp()));
	$oTemplate->set("Thread_User_ID", $oUser->getID());
	$oTemplate->set("Thread_User_Name", htmlspecialchars($oUser->getName()));
	$oTemplate->set("Thread_Last_Name", htmlspecialchars($oLastUser->getName()));
	$oTemplate->set("Thread_Last_ID", $oLastUser->getID());
	$oTemplate->set("Thread_Color", ($iCounter % 2) ? "ntg" : "ntw");
	$oTemplate->set("New", $oThread->getNew() ? "Visible" : "Hidden");
	$oTemplate->set("User_Status", $oUser->getOnline() ? "on" : "off");
	$oTemplate->set("Last_User_Status", $oLastUser->getOnline() ? "on" : "off");
	$oLastUser->getOnline() ? $oTemplate->replace("Thread_Last_User_Offline", "Thread_User_Online") & $oTemplate->parse("Thread_Last_User_Online") : $oTemplate->parse("Thread_Last_User_Offline");
	$oTemplate->parse("Threads");
	$iCounter++;
	
}

// Visa inget om vi inte hade n?gra tr?dar
$iCounter ? TRUE : $oTemplate->replace("Threads","Empty");

// Skicka till broswern
$oTemplate->set("Timer", timer());
$oTemplate->parse();
$oTemplate->spit();
echo("<FONT COLOR=\"#335178\">".timer()."</font>");
?>