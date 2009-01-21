<?PHP

// function_validUser.php
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

function validUser ()
{	
	// Kolla grejer
	if ((isset($_SESSION['oUser'])) AND ($_SESSION['iValidUntil'] > time()) AND ($_SESSION['sIP'] == $_SERVER['REMOTE_ADDR']))
	{
		// Inloggad användare
		// Konfigurationsobjekt
		$oConfiguration =& Configuration::createInstance();
		
		// Uppdatera validitet
		$_SESSION['iValidUntil'] = time() + $oConfiguration->getCustomValue("ValidTime") * 60;
		
		// Returnera
		return(TRUE);
	} else // Ej inloggad
		return(FALSE);
		
}

?>