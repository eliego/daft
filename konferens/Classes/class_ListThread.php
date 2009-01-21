<?PHP

// class_ListThread.php
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

class ListThread extends Thread
{
	// Membervars:
	// Anv�ndare som skapade
	var $oUser;
	
	// Timestamp f�r sista inl�gg
	var $iLastPostTimestamp;
	
	// Antal som l�st
	var $iReads;
	
	// Antal poster
	var $iPosts;
	
	// Sista inl�gg av anv�ndare
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
	
	// H�mta senaste timestamp
	function getLastPostTimestamp ()
	{
		return($this->iLastPostTimestamp);
	}
	
	// H�mta antal poster
	function getNumberPosts ()
	{
		return($this->iPosts);
	}
	
	// H�mta antal som l�st
	function getNumberReads ()
	{
		return($this->iReads);
	}
	
	// H�mta skaparen
	function getUser ()
	{
		return($this->oUser);
	}
	
	// H�mta sista user
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
	
	// H�mta ny
	function getNew ()
	{
		return($this->bNew);
	}
}

?>