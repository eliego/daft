<?PHP

// function_errorHandler.php
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

// Definiera felloggen för fatala fel
define("FATAL_ERRORLOG", "daft_errorlogFatal.log");

function errorHandler ($iErrCode, $sErrMsg, $sFile, $iLine, $aContext)
{
	// Fatalt?
	if ($iErrCode = E_USER_ERROR)
	{// TODO: Gör till EN, enda skillnad die() om ERROR
		// Logga och dö
		$sMsg = time().": ".$sFile.": ".$iLine.": ".$sErrMsg;
		error_log ($sMsg, 3, FATAL_ERRORLOG);
		die("A fatal error has occured.<BR>Errormessage: ".$sErrMsg."<BR>Please contact the sysadmin and give the following code: ".time());
	} elseif ($aContext['this']) {
		// Felet inträffade i en klass, skicka vidare till klassens felhanterare
		// Men tryck först in felmeddelanden
		$oConfig &= Configuration::createInstance();
		$iCodeLen = strspn($sErrMsg, "1234567890");
		$sErrMsg = substr_replace($sErrMsg, $oConfig->getErrorMsg($iErrNo = substr($sErrMsg, 0, $iCodeLen)), 0, $iCodeLen);
		$sErrMsg .= " (".$iErrNo.")";
		$aContext['this']->errorHandler($iErrNo, $sErrMsg);
	} else {
		// Skriv ut felet lite fint
		// TODO: Koda
		// TODO: Anropa loggobjekt
	}
	
	// Returnera
	return(NULL);
}

?>