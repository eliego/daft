<?PHP

// ShowThread.php
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
require("Classes/class_Logger.php");
require("Classes/class_User.php");
require("Classes/class_UserPresentation.php");
require("Classes/class_Post.php");
require("Classes/yapter.php");

// Definiera felhanterare
// set_error_handler("errorHandler");

// Objekt
$oConfiguration =& Configuration::createInstance();
$oTemplate = new Template("/home/daft/Templates/ShowThread.tpl");
$oTemplate->setParseMode(TRUE);
$oTemplate->setWarningLevel(E_YAPTER_ERROR);

// Kolla s? vi f?tt r?tt grejer
if (!((is_numeric($_GET['Page'])) AND ($_GET['Page'] > 0)))
	$_GET['Page'] = 1;
	
if (!(is_numeric($_GET['ID']) AND ($_GET['ID'] > 0)))
	trigger_error("36", E_USER_ERROR);

// Session
session_start();

// Kolla om vi ?r inloggade
if (!($bInlogged = validUser()))
{
	// Det ?r vi inte, fixa variabler och st?ng sessionen igen
	$iNumberPosts = $oConfiguration->getCustomValue("DefaultNumberPosts");
	session_destroy();
} else {
	// Det ?r vi, fixa variabler, l?gg till tr?den i gammla tr?dar
	$iNumberPosts = $_SESSION['oUser']->getNumberPostsInThread();
	$oTemplate->set("User_Signature", "\n\n\n".$_SESSION['oUser']->getSignature()); }

if ($iNumberPosts == 0)
	$iNumberPosts = 1;

// H?mta poster
$oThread = new Thread($_GET['ID']);
if ($sErr = $oThread->getErrorMsg())
	trigger_error("8: ".$sErr, E_USER_WARNING);
	
if ($bInlogged) 
		$_SESSION['a_iOldThreads'][$_GET['ID']] = $oThread->getNumberPosts();
		
// Markera tr?den som l?st
$oThread->read();
	
if (FALSE===($a_oPosts = $oThread->getPosts($iNumberPosts * ($_GET['Page'] - 1), $iNumberPosts)))
	trigger_error("23. ".$oThread->getErrorMsg(), E_USER_WARNING);
	
// Bygg upp sidan
$oTemplate->set("title", $oConfiguration->getCustomValue("Title"));
$oTemplate->set("id", $oThread->getThreadID());
if ($bInlogged) {
	$oTemplate->set("User_ID", $_SESSION['oUser']->getID());
	$oTemplate->set("User_Name", htmlspecialchars($_SESSION['oUser']->getName()));
	$oTemplate->replace("Login_Outside", "Login_Inside");
	$oTemplate->replace("CantPost", "NewPost"); }

// Kolla om det finns fler sidor
if ($oThread->getNumberPosts() > $iNumberPosts) {
	$oTemplate->replace("Empty", "MultiPages");
	$oTemplate->replace("Empty2", "MultiPages");
	$iPages = (int)(@$oThread->getNumberPosts() / $iNumberPosts);
	if(!((double)$iPages == (@$oThread->getNumberPosts() / $iNumberPosts)))
		$iPages++;
	for ($n = 1; $n <= $iPages; $n++) {
		$oTemplate->set("Page", $n);	
		$oTemplate->parse("MultiPage"); 
		$oTemplate->parse("MultiPage2"); }
}
$oUser = $oThread->getUser();
$oTemplate->set("Show_Thread_Heading", $oThread->getRubrik());
$oTemplate->set("Show_Thread_Date", fixDateFormat($oThread->getCreatedTimestamp(), 2));
$oTemplate->set("Thread_Creater_Status", $oUser->getOnline() ? "on" : "off");
$oTemplate->set("Show_Thread_Writer_ID", $oUser->getID());
$oTemplate->set("Show_Thread_Writer_Name", htmlspecialchars($oUser->getName()));
$oTemplate->set("Reads", $oThread->getRead());
$iOld = (int)((time() - $oThread->getCreatedTimestamp()) / 86400);
if (!((double)$iOld == ($oThread->getCreatedTimestamp() / 86400)))
	$iOld++;
$oTemplate->set("Show_Thread_Answers", $oThread->getNumberPosts() - 1);
$oTemplate->set("Show_Thread_Old", $iOld);
$oTemplate->set("Show_Thread_Writer_Say", $a_oPosts[0]->getText());

// Loopa poster
$iN = count($a_oPosts);
for ($n = 1; $n < $iN; $n++)
{
	$oUser = $a_oPosts[$n]->getUser();
	$oTemplate->set("Post_User_Status", $oUser->getOnline() ? "on" : "off");
	$oTemplate->set("Show_User_ID", $oUser->getID());
	$oTemplate->set("Show_User_Name", htmlspecialchars($oUser->getName()));
	$oTemplate->set("Show_Post_Date", fixDateFormat($a_oPosts[$n]->getCreatedTimestamp(), 2));
	$oTemplate->set("Show_Thread_Say", $a_oPosts[$n]->getText());
	$oTemplate->parse("Posts");
}
// G?m posts om det inte finns n?gra
if (!($iN > 1))
	$oTemplate->replace("Posts","Empty");
	
// G?m orig om det inte ?r sida 1
if (!($_GET['Page'] == 1))
	$oTemplate->replace("Orig", "Tom");
	
// Visa
$oTemplate->set("Timer", timer());
$oTemplate->parse();
$oTemplate->spit();
?>
