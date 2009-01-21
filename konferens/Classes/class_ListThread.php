<?PHP

// class_ListThread.php
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

class ListThread extends Thread
{
	// Membervars:
	// Användare som skapade
	var $oUser;
	
	// Timestamp för sista inlägg
	var $iLastPostTimestamp;
	
	// Antal som läst
	var $iReads;
	
	// Antal poster
	var $iPosts;
	
	// Sista inlägg av användare
	var $oLastPoster;
	
	// Skapad
	var $iCreatedTimestamp;
	
	// Ny?
	var $bNew;
	
	// Metoder:
	// Konstruktor
	function ListThread ($oUs, $iLPT, $iR, $iP, $oLP, $iID, $sRubr, $iCr)
	{
		// Tryck in i membervarsen
		$this->oUser = $oUs;
		$this->iLastPostTimestamp = $iLPT;
		$this->iReads = $iR;
		$this->iPosts = $iP;
		$this->oLastPoster = $oLP;
		$this->iThreadID = $iID;
		$this->sRubrik = $sRubr;
		$this->iCreatedTimestamp = $iCr;
		$this->bNew = FALSE;
		
		// Returnera
		return(TRUE);
	}
	
	// Hämta senaste timestamp
	function getLastPostTimestamp ()
	{
		return($this->iLastPostTimestamp);
	}
	
	// Hämta antal poster
	function getNumberPosts ()
	{
		return($this->iPosts);
	}
	
	// Hämta antal som läst
	function getNumberReads ()
	{
		return($this->iReads);
	}
	
	// Hämta skaparen
	function getUser ()
	{
		return($this->oUser);
	}
	
	// Hämta sista user
	function getLastPostUser ()
	{
		return($this->oLastPoster);
	}
	
	// Set ny
	function setNew ($bNew)
	{
		$this->bNew = $bNew;
		return(TRUE);
	}
	
	// Hämta ny
	function getNew ()
	{
		return($this->bNew);
	}
}

?>