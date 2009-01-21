<?PHP

// class_User.php
// Skriven av Eli Kaufman f�r Daft
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

class User
{
	// Membervars:
	// Anv�ndarens ID
	var $iID;
	
	// Anv�ndarens namn
	var $sName;
	
	// �r jag inloggad?
	var $bOnline;
	
	// Metoder:
	// Konstruktor
	function User ($iID, $sName, $bOnline = FALSE)
	{
		// Tryck in skiten i membervarsen
		$this->iID 		 = $iID;
		$this->sName     = $sName;
		$this->bOnline   = $bOnline;
	}
	
	// H�mta ID
	function getID ()
	{
		return($this->iID);
	}
	
	// H�mta namn
	function getName ()
	{
		return($this->sName);
	}
	
	// H�mta online
	function getOnline ()
	{
		return($this->bOnline);
	}
}

?>