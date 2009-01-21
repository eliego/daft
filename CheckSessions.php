<?PHP

// CheckSessions.php
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
require("Classes/class_Configuration.php");
require("Classes/class_ExternalStorage.php");
require("Classes/class_User.php");
require("Classes/class_UserPresentation.php");
require("Classes/class_Logger.php");

// Definiera felhanterare
// set_error_handler("errorHandler");

$sDir = session_save_path();

// Öppna katalog
if (!($rDir = opendir($sDir)))
	trigger_error("46", E_USER_ERROR);
	
// Loopa igenom sessioner
while (!(FALSE ===($sFile = readdir($rDir)))) {
	if (fnmatch("sess_*", $sFile)) {
		if ($sTemp = file_get_contents($sDir."/".$sFile)) {
			// Finns iValid?
			if (!(substr($sTemp, -25, 14) == "iValidUntil|i:"))
				trigger_error("48", E_USER_WARNING);
				
			if (substr($sTemp, -11, -1) < time()) { // Tiden har löpt ut
				// Ta fram oUser
				if (!($sTemp = substr($sTemp, 6, strrpos($sTemp, "}") - 6)))
						trigger_error("48", E_USER_WARNING);
				if (!($sTemp = substr($sTemp, 0, strrpos($sTemp, "}") + 1)))
						trigger_error("48", E_USER_WARNING);
						
				// Deserialisera
				$oUser = unserialize($sTemp);
				
				// Fixa egen oData, han verkar inte ?verleve deserialiseringen
				$oData = ExternalStorage::createInstance();
				$oData->markOffline($oUser->getID());
				
				// Logga ut
				//if (!($oUser->logOff()))
				//	trigger_error("49: ".$oUser->getErrorMsg(), E_USER_WARNING);
				
				// Ta bort fil
				unlink($sDir."/".$sFile);
			}
		}
	}
}


?>